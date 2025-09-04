<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});
Route::get('/test-db-path', function () {
    $path = config('database.connections.sqlite.database');
    return [
        'config_path' => $path,
        'resolved_path' => realpath($path),
        'file_exists' => file_exists($path) ? 'Yes' : 'No',
    ];
});
Route::post('/data/export', [DataController::class, 'export']);
Route::post('/data/import', [DataController::class, 'import']);