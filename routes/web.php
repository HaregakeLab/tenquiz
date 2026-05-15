<?php

use App\Http\Controllers\ProblemController;
use App\Http\Controllers\Admin\ProblemController as AdminProblemController;
use Illuminate\Support\Facades\Route;

// プレイヤー側
Route::get('/', [ProblemController::class, 'index'])->name('problems.index');
Route::get('/problems/{problem}', [ProblemController::class, 'show'])->name('problems.show');

// 管理画面
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminProblemController::class, 'index'])->name('problems.index');
    Route::get('/problems/create', [AdminProblemController::class, 'create'])->name('problems.create');
    Route::post('/problems', [AdminProblemController::class, 'store'])->name('problems.store');
    Route::get('/problems/{problem}/edit', [AdminProblemController::class, 'edit'])->name('problems.edit');
    Route::put('/problems/{problem}', [AdminProblemController::class, 'update'])->name('problems.update');
    Route::delete('/problems/{problem}', [AdminProblemController::class, 'destroy'])->name('problems.destroy');
    Route::post('/problems/{problem}/slots/{slot}/image', [AdminProblemController::class, 'uploadImage'])->name('problems.slots.image.upload');
    Route::delete('/problems/{problem}/slots/{slot}/image', [AdminProblemController::class, 'deleteImage'])->name('problems.slots.image.delete');
});
