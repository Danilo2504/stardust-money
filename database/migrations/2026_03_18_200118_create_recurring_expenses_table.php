<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete()->nullOnUpdate();
            $table->string('description', 255)->nullable()->index();
            $table->decimal('amount', 8, 4)->nullable()->index(); // usamos 4 digitos en la db, en el front seran 2 o 0 si es un numero entero
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->cascadeOnDelete()->nullOnUpdate();
            $table->integer('custom_interval_value')->nullable();
            $table->enum('custom_interval_unit', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->dateTime('next_due_date')->nullable()->index();
            $table->boolean('is_active')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};
