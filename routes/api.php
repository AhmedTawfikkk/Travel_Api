<?php

use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use App\Http\Controllers\Api\V1\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user(); 
})->middleware('auth:sanctum');

Route::get('travels',[TravelController::class,'index']);
Route::get('travels/{travel:slug}/tours',[TourController::class,'index']);

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function(){
    Route::middleware('role:admin')->group(function()
    {
        Route::post('travels',[Admin\TravelController::class,'store']);
        Route::post('travels/{travel}/tours',[Admin\TourController::class,'store']);
    });

     Route::put('travels/{travel}',[Admin\TravelController::class,'update']);  //admin and editor can acess this route
   
});

Route::post('login',LoginController::class);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('email-verification',[EmailVerificationController::class,'emailverification']);
    Route::get('email-verification',[EmailVerificationController::class,'sendemailverification']);

});