<?php

namespace App\Console\Commands\Recurring;

use App\Models\Expense;
use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:generate-recurring-expenses')]
#[Description('Genera gastos draft a partir de gastos recurrentes activos cuya fecha de vencimiento ya llegó')]
class GenerateRecurringExpenses extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting recurring expenses generation...');
        logger()->info('Starting recurring expenses generation...');

        $now = now();
        $totalGenerated = 0;

        $recurringExpenses = RecurringExpense::query()
            ->where('is_active', true)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<=', $now)
            ->get();

        foreach ($recurringExpenses as $recurringExpense) {
            $generatedCount = $this->processRecurringExpense($recurringExpense);
            $totalGenerated += $generatedCount;
        }

        $message = "Recurring expenses generation finished. Generated {$totalGenerated} expense(s).";
        $this->info($message);
        logger()->info($message);

        return self::SUCCESS;
    }

    private function processRecurringExpense(RecurringExpense $recurringExpense): int
    {
        $generatedCount = 0;

        DB::transaction(function () use ($recurringExpense, &$generatedCount) {
            while ($recurringExpense->next_due_date !== null && $recurringExpense->next_due_date->lessThanOrEqualTo(now())) {
                Expense::create([
                    'user_id' => $recurringExpense->user_id,
                    'code' => Expense::generateCode($recurringExpense->user_id),
                    'draft' => true,
                    'description' => $recurringExpense->description,
                    'amount' => $recurringExpense->amount,
                    'category_id' => $recurringExpense->category_id,
                    'type' => 'recurring_child',
                    'expense_date' => $recurringExpense->next_due_date,
                    'recurring_expense_id' => $recurringExpense->id,
                ]);

                $generatedCount++;

                $recurringExpense->next_due_date = $this->advanceDate($recurringExpense->next_due_date, $recurringExpense);
            }

            $recurringExpense->save();
        });

        return $generatedCount;
    }

    private function advanceDate(Carbon $date, RecurringExpense $recurringExpense): Carbon
    {
        $value = (int) $recurringExpense->custom_interval_value;
        $unit = $recurringExpense->custom_interval_unit;

        return match ($unit) {
            'days' => $date->copy()->addDays($value),
            'weeks' => $date->copy()->addWeeks($value),
            'months' => $date->copy()->addMonths($value),
            'years' => $date->copy()->addYears($value),
            default => $date->copy()->addMonths($value),
        };
    }
}
