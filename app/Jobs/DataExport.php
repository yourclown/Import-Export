<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class DataExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function handle()
    {
        // Force database path
        config(['database.connections.sqlite.database' => database_path('sqlite/database.sqlite')]);
        $content = User::exportData($this->format);
        $fileName = 'export_' . time() . '.' . $this->format;
        Storage::disk('local')->put('exports/' . $fileName, $content);
    }
}