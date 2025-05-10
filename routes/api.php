<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PartController;

Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/parts', [PartController::class, 'index']);
    Route::post('/store-part', [PartController::class, 'store']);
    Route::post('/update-part/{part}', [PartController::class, 'update']);
    Route::delete('/deletecategory/{id}', [CategoryController::class, 'destroy']);
    Route::put('/updatecategory/{id}', [CategoryController::class, 'update']);
    Route::post('/storecategory', [CategoryController::class, 'store']);
    Route::delete('/deletepart/{id}', [PartController::class, 'destroy']);
    Route::post('/store-supplier', [SupplierController::class, 'store']);
    Route::put('/updatesupplier/{id}', [SupplierController::class, 'update']);
    Route::delete('/deletesupplier/{id}', [SupplierController::class, 'destroy']);


    // Add other protected routes here
});
