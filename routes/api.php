<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentAuthController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/check/student/login', [StudentAuthController::class, 'checkTokenExpiration']);
Route::middleware('auth:student')->group(function () {
    Route::post('/student/logout', [StudentAuthController::class, 'logout']);
    Route::get('/student/check-token', [StudentAuthController::class, 'checkToken']);
    // Add other protected routes specific to students here
});


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/check/login', [AuthController::class, 'checkTokenExpiration'])->name('checklogin');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/students', [StudentController::class, 'store']);


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::apiResource('students', StudentController::class);
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);

    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('batches', BatchController::class);
    Route::apiResource('exams', ExamController::class);
});
