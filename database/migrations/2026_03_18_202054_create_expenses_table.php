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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete()->nullOnUpdate();
            $table->string('code', 20)->nullable();
            $table->unique(['user_id', 'code']);
            $table->index('code');
            $table->boolean('draft')->nullable()->index(); // status pasa a ser draft. Los montos no tiene estado, sino que son representaciones de algo ya pagado. Draft es sollo para esperar a mi confirmacion
            $table->string('description', 255)->nullable()->index();
            $table->decimal('amount', 8, 4)->nullable()->index(); // usamos 4 digitos en la db, en el front seran 2 o 0 si es un numero entero
            $table->foreignUuid('category_id')->nullable()->constrained('categories')->cascadeOnDelete()->nullOnUpdate();
            $table->text('notes')->nullable();
            $table->enum('type', ['one_time', 'recurring_child', 'installment'])->nullable()->index();
            $table->dateTime('expense_date')->nullable()->index(); // en la db manejamos datetime, en el front usaremos date, pero dejando la ventana abierta a poder usar la hora
            // para los adjuntos usare medialibrary. El almacenamiento sera en local por ahora, despues vere si puedo hacer ua intervencion mas profesional
            $table->foreignUuid('recurring_expense_id')->nullable()->constrained('recurring_expenses')->cascadeOnDelete()->nullOnUpdate();
            $table->foreignUuid('installment_group_id')->nullable()->constrained('installment_groups')->cascadeOnDelete()->nullOnUpdate();
            $table->integer('installment_number')->nullable()->index();
            $table->index(['installment_group_id', 'installment_number']); // un compose index para tomar la cuota especifica, no se si esta bien hecho o sera de utilidad
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
        Schema::dropIfExists('expenses');
    }
};
