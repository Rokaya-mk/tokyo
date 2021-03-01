<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/verified-only', function(Request $request){
    dd('your are verified', $request->user()->name);
})->middleware('auth:api','verified');


 Route::post('register', 'App\Http\Controllers\Api\AuthController@register');
 Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
 Route::post('logout','App\Http\Controllers\Api\AuthController@logoutApi');

 Route::post('/password/email', 'App\Http\Controllers\Api\ForgotPasswordController@sendResetLinkEmail');
 Route::post('/password/reset', 'App\Http\Controllers\Api\ResetPasswordController@reset');


 Route::get('/email/resend', 'App\Http\Controllers\Api\VerificationController@resend')->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', 'App\Http\Controllers\Api\VerificationController@verify')->name('verification.verify');

 Route::middleware('auth:api')->group( function (){
    Route::get('tasks/todayTask', 'App\Http\Controllers\Api\TaskController@displayTodayTasks');
    Route::get('tasks/tomorrowTask', 'App\Http\Controllers\Api\TaskController@displayTomorrowTasks');
    Route::post('tasks/storeToday', 'App\Http\Controllers\Api\TaskController@storeTodayTask');
    Route::post('tasks/storeTomorrow', 'App\Http\Controllers\Api\TaskController@storeTomorrowTask');
    Route::put('tasks/markDone/{id}', 'App\Http\Controllers\Api\TaskController@markDone');
    Route::put('tasks/mergeTasks', 'App\Http\Controllers\Api\TaskController@mergeTasks');
    Route::delete('tasks/destroy/{id}', 'App\Http\Controllers\Api\TaskController@destroy');

});
