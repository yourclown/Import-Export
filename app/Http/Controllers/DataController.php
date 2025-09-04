<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\DataExport;
use App\Jobs\DataImport;

class DataController extends Controller
{
    public function export(Request $request)
    {
        dump($request);
        $request->validate(['format' => 'required|in:csv,json,xml']);
        DataExport::dispatch($request->format);
        return response()->json(['message' => 'Export job dispatched']);
    }

  public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'format' => 'required|in:csv,json',
        ]);

        // Store the uploaded file temporarily
        $file = $request->file('file');
        $filePath = $file->store('temp'); // Stores in storage/app/temp
        $fullPath = storage_path('app/' . $filePath);

        // Dispatch job with file path
        DataImport::dispatch($fullPath, $request->format);

        return response()->json(['message' => 'Import job dispatched']);
    }
}