<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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



Route::post('/register', 'Api\AuthController@register');

 Route::post('login', 'Api\AuthController@login');

 Route::middleware('auth:api')-> post('logout','Api\AuthController@logoutApi');


 Route::post('/password/email', 'Api\ForgotPasswordController@sendResetLinkEmail');
 Route::post('/password/reset', 'Api\ResetPasswordController@reset');


 Route::get('/email/resend', 'Api\VerificationController@resend')->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', 'Api\VerificationController@verify')->name('verification.verify');

 Route::middleware('auth:api')->group( function (){
    Route::post('tasks/todayTask', 'Api\TaskController@displayTodayTasks');
    Route::post('tasks/tomorrowTask', 'Api\TaskController@displayTomorrowTasks');
    Route::post('tasks/storeToday', 'Api\TaskController@storeTodayTask');
    Route::post('tasks/storeTomorrow', 'Api\TaskController@storeTomorrowTask');
    Route::put('tasks/markDone/{id}', 'Api\TaskController@markDone');
    Route::put('tasks/UncheckeTask/{id}', 'Api\TaskController@UncheckeTask');
    Route::put('/tasks/update/{task}', 'Api\TaskController@update' );
    Route::put('tasks/mergeTasks', 'Api\TaskController@mergeTasks');
    Route::delete('tasks/destroy/{id}', 'Api\TaskController@destroy');

});
