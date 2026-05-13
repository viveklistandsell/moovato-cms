<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFileController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MediaTrashController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('media', [MediaController::class, 'index'])->name('media.index');

        Route::post('media/folders', [MediaFolderController::class, 'store'])->name('media.folders.store');
        Route::post('media/folders/bulk-delete', [MediaFolderController::class, 'bulkDestroy'])->name('media.folders.bulk-delete');
        Route::patch('media/folders/{folder}', [MediaFolderController::class, 'update'])->name('media.folders.update');
        Route::delete('media/folders/{folder}', [MediaFolderController::class, 'destroy'])->name('media.folders.destroy');

        Route::get('media/files/upload', [MediaFileController::class, 'check'])->name('media.files.check');
        Route::post('media/files', [MediaFileController::class, 'store'])->name('media.files.store');
        Route::post('media/files/bulk-delete', [MediaFileController::class, 'bulkDestroy'])->name('media.files.bulk-delete');
        Route::post('media/files/bulk-move', [MediaFileController::class, 'bulkMove'])->name('media.files.bulk-move');
        Route::patch('media/files/{file}', [MediaFileController::class, 'update'])->name('media.files.update');
        Route::delete('media/files/{file}', [MediaFileController::class, 'destroy'])->name('media.files.destroy');
        Route::get('media/files/{file}/download', [MediaFileController::class, 'download'])->name('media.files.download');

        Route::get('media/trash', [MediaTrashController::class, 'index'])->name('media.trash.index');
        Route::post('media/trash/folders/{id}/restore', [MediaTrashController::class, 'restoreFolder'])->name('media.trash.folders.restore');
        Route::post('media/trash/files/{id}/restore', [MediaTrashController::class, 'restoreFile'])->name('media.trash.files.restore');
        Route::delete('media/trash/folders/{id}', [MediaTrashController::class, 'forceDeleteFolder'])->name('media.trash.folders.force-destroy');
        Route::delete('media/trash/files/{id}', [MediaTrashController::class, 'forceDeleteFile'])->name('media.trash.files.force-destroy');
    });

require __DIR__.'/settings.php';
