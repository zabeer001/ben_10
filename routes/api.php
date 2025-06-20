<?php
use App\Http\Controllers\AdditionalOptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerInfoController;
use App\Http\Controllers\ForgetPassowrdController;
use App\Http\Controllers\FrontendQueryController;
use App\Http\Controllers\ModelColorWiseImageController;
use App\Http\Controllers\ModelThemeWiseImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\VehicleModelController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

/* create by abu sayed (start)*/ 

Route::post('password/email', [AuthController::class, 'sendResetEmailLink']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');



/* create by abu sayed (end)*/ 

// Protected routes

//categories
Route::apiResource('categories', CategoryController::class);
// Route::put('categories/status/{id}', [CategoryController::class, 'statusUpdate']);


//colors
Route::apiResource('colors', ColorController::class);
Route::get('colors-types', [ColorController::class, 'allTypes']);
// Route::put('colors/status/{id}', [ColorController::class, 'statusUpdate']);

Route::apiResource('models', VehicleModelController::class);

Route::apiResource('customers', CustomerInfoController::class);

Route::apiResource('addtional-options', AdditionalOptionController::class);
Route::get('addtional-options-category', [AdditionalOptionController::class, 'allTypes']);


Route::get('frontend-models-category', [FrontendQueryController::class, 'frontendModelsCategory']);
Route::get('frontend-additional-options', [FrontendQueryController::class, 'frontendAdditionalOptions']);





Route::apiResource('themes', ThemeController::class);

Route::apiResource('orders', OrderController::class);
Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);




Route::apiResource('model-theme-wise-image', ModelThemeWiseImageController::class);
Route::apiResource('model-color-wise-image', ModelColorWiseImageController::class);