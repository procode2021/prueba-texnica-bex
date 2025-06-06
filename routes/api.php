<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitController; // Importa el controlador de visitas
use App\Http\Controllers\Auth\AuthController; // Importa el controlador de autenticación

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ruta para el usuario autenticado (ejemplo, puedes removerla si no la necesitas)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Grupo de rutas para el CRUD de Visitas, protegidas con el middleware 'auth:sanctum'.
// Esto significa que se requiere un token de acceso válido para acceder a estas rutas.
Route::middleware('auth:sanctum')->group(function () {
    // Ruta para listar todas las visitas y crear una nueva visita.
    Route::get('/visits', [VisitController::class, 'index']); // GET /api/visits
    Route::post('/visits', [VisitController::class, 'store']); // POST /api/visits

    // Rutas para obtener, actualizar y eliminar una visita específica por ID.
    Route::get('/visits/{id}', [VisitController::class, 'show']); // GET /api/visits/{id}
    Route::put('/visits/{id}', [VisitController::class, 'update']); // PUT /api/visits/{id}
    Route::delete('/visits/{id}', [VisitController::class, 'destroy']); // DELETE /api/visits/{id}
    Route::post('/visits/import', [VisitController::class, 'importFromExcel']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

