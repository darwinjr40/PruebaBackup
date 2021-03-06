<?php

use App\Http\Controllers\Api\Inventario\MateriaPrimaController;
use App\Http\Controllers\Api\Inventario\MateriaPrimaApiController;
use App\Http\Controllers\Api\Inventario\ProductoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TokenAuthController;
use App\Http\Controllers\Api\Login\UserController;


use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//------------------------------------------------------ USERS ----------------------------------------------------------------------------------//
Route::get('users', [UserController::class, 'index'])->name('users');
//-----------------------------------------------------------------------------------------------------------------------------------------------//


Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user->tokens()->where('name', $request->device_name)->delete();

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::middleware(['auth:sanctum'])->get('/user/revoke', function (Request $request) {
    $user = $request->user();
    $user->tokens()->delete();
    return "The tokens has been deleted";
})->name('login.revoke');

//------------------------------------------------------ MATERIA PRIMA API----------------------------------------------------------------------------------//
Route::get('materia-prima-api', [MateriaPrimaController::class, 'index'])->name('materia-prima-api');
Route::post('materia-prima-api/delete/{materia}', [MateriaPrimaController::class, 'delete'])->name('materia-prima-api.delete');
Route::post('create/materia-prima-api', [MateriaPrimaController::class, 'create'])->name('materia-prima-api.create');
Route::post('update/materia-prima-api/{materia}', [MateriaPrimaController::class, 'update'])->name('materia-prima-api.update');
//------------------------------------------------------ MATERIA PRIMA API RESOURCES----------------------------------------------------------------------------------//
Route::apiResource('materia-prima-api2', MateriaPrimaApiController::class)->names('api.materia-prima');


Route::apiResource('productos', ProductoApiController::class)->names('api.productos');
