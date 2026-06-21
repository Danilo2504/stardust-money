<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:trash-cleaner {--chunkSize= : Número de registros a procesar por lote (default: 1000)} {--model= : Modelo a limpiar opcional (default: todos los modelos que extienden BaseModel)}')]
#[Description('Elimina los registros soft-deleteados hace más de 30 días')]
class TrashCleaner extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = $this->argument('chunkSize') ?? 1000; // Número de registros a procesar por lote
        $model = $this->argument('model');

        $this->info('Starting trash cleaner...');
        logger()->info('Starting trash cleaner...', ['chunkSize' => $chunkSize, 'model' => $model]);

        $params = [
            'chunk' => $chunkSize,
        ];

        if (filled($model) && class_exists($model)) {
            $params['model'] = $model;
        }

        $this->call('model:prune', $params);

        $this->info('Trash cleaner finished.');
        logger()->info('Trash cleaner finished.', ['chunkSize' => $chunkSize, 'model' => $model]);
    }
}
