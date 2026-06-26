<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthlyExpenses = Expense::byAuthor($user->id)
            ->where('draft', false)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalSpent = $monthlyExpenses->sum('amount');
        $expenseCount = $monthlyExpenses->count();

        $pendingDrafts = Expense::byAuthor($user->id)
            ->where('draft', true)
            ->count();

        $recentExpenses = Expense::byAuthor($user->id)
            ->with('category')
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('pages.dashboard', compact(
            'totalSpent',
            'expenseCount',
            'pendingDrafts',
            'recentExpenses',
        ));
    }
}
