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
        Schema::create('expense_splits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('expense_id')->nullable()->constrained('expenses')->cascadeOnDelete()->nullOnUpdate();
            $table->string('person_name', 255)->nullable()->index();
            $table->decimal('amount', 8, 4)->nullable()->index(); // usamos 4 digitos en la db, en el front seran 2 o 0 si es un numero entero
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
        Schema::dropIfExists('expense_splits');
    }
};
