<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\CourseModuleController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\payment\PaymentController;

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




Route::post('create/payment', [PaymentController::class, 'create']);
Route::post('check/payment', [PaymentController::class, 'checkPayment']);
Route::post('checkout/payment/status', [PaymentController::class, 'queryPayment']);

Route::post('create/payment/ekpay', [PaymentController::class, 'ekpayPayment']);
Route::post('payment/ekpay/ipn', [PaymentController::class, 'ekpayPaymentIpn']);
Route::post('re/call/ekpay/ipn',[PaymentController::class ,'ekpayReCallIpn']);



Route::get('courses/categories', [CourseCategoryController::class, 'index']);
Route::post('courses/categories', [CourseCategoryController::class, 'store']);
Route::get('courses/categories/{id}', [CourseCategoryController::class, 'show']);
Route::put('courses/categories/{id}', [CourseCategoryController::class, 'update']);
Route::delete('courses/categories/{id}', [CourseCategoryController::class, 'destroy']);


Route::get('courses/', [CourseController::class, 'index']);
Route::post('courses/', [CourseController::class, 'store']);
Route::get('courses/{id}', [CourseController::class, 'show']);
Route::get('get/courses/{id}', [CourseController::class, 'getcourses']);
Route::put('courses/{id}', [CourseController::class, 'update']);
Route::delete('courses/{id}', [CourseController::class, 'destroy']);



Route::get('modules/', [CourseModuleController::class, 'index']);
Route::post('modules/', [CourseModuleController::class, 'store']);
Route::get('modules/{id}', [CourseModuleController::class, 'show']);
Route::put('modules/{id}', [CourseModuleController::class, 'update']);
Route::delete('modules/{id}', [CourseModuleController::class, 'destroy']);

Route::get('course/videos', [CourseVideoController::class, 'index']);
Route::post('course/videos/', [CourseVideoController::class, 'store']);
Route::get('course/videos/{id}', [CourseVideoController::class, 'show']);
Route::put('course/videos/{id}', [CourseVideoController::class, 'update']);
Route::delete('course/videos/{id}', [CourseVideoController::class, 'destroy']);


Route::get('/enrollments', [EnrollmentController::class, 'index']);
Route::post('/enrollments', [EnrollmentController::class, 'store']);
Route::get('/enrollments/{id}', [EnrollmentController::class, 'show']);
Route::put('/enrollments/{id}', [EnrollmentController::class, 'update']);
Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy']);

Route::post('/enrollmented/course/{student_id}', [EnrollmentController::class, 'enrolledcourse']);














Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/check/student/login', [StudentAuthController::class, 'checkTokenExpiration']);
Route::middleware('auth:student')->group(function () {
    Route::post('/student/logout', [StudentAuthController::class, 'logout']);
    Route::get('/student/check-token', [StudentAuthController::class, 'checkToken']);

    Route::get('/students/profile/{id}', [StudentController::class, 'show']);
    Route::get('/exam/questions', [QuestionController::class, 'index']);
    Route::get('/student/exams', [ExamController::class, 'index']);
    Route::post('/student/exams', [ExamController::class, 'store']);

    Route::post('/course-video/{path}', function ($path) {
        // Serve the file from the protected disk
        return response()->file(Storage::disk('protected')->path($path));
    })->where('path', '.*');



});


Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/check/login', [AuthController::class, 'checkTokenExpiration'])->name('checklogin');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/students', [StudentController::class, 'store']);


Route::group(['middleware' => 'auth:api'], function () {




    Route::post('/logout', [AuthController::class, 'logout']);
    // Route::apiResource('students', StudentController::class);

    Route::post('/students/set/ratings/{id}', [StudentController::class, 'setRating']);



    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);

    Route::apiResource('questions', QuestionController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('batches', BatchController::class);
    Route::apiResource('exams', ExamController::class);




});



