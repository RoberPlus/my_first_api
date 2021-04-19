<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ClienteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ClienteController::class, 'index']);

// Registrar cliente
Route::post('/register', [ClienteController::class, 'store']);

// Cursos
Route::get('/cursos', [CursoController::class, 'index']);
Route::post('/cursos', [CursoController::class, 'store']);
Route::get('/cursos/{id}', [CursoController::class, 'show']);
Route::put('/cursos/{id}/editar', [CursoController::class, 'update']);
Route::delete('/cursos/{id}', [CursoController::class, 'destroy']);
