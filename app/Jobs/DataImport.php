<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DataImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $format;

    public function __construct(string $filePath, string $format)
    {
        $this->filePath = $filePath;
        $this->format = $format;
    }

    public function handle()
    {
        // Force database path
        config(['database.connections.sqlite.database' => database_path('sqlite/database.sqlite')]);

        // Pass the file path to the import method
        User::importData($this->filePath, $this->format);

        // Optionally delete the temporary file after import
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}