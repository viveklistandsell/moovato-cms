<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Blog\CategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\Blog\PostController as AdminBlogPostController;
use App\Http\Controllers\Admin\Blog\TagController as AdminBlogTagController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFileController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MediaTrashController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Default-locale (DE) routes live at the root with no /de prefix.
Route::middleware('locale')->group(function (): void {
    Route::inertia('/', 'Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ])->name('home');

    Route::get('blog', [FrontendBlogController::class, 'index'])
        ->name('blog.index');
    Route::get('blog/{permalink}', [FrontendBlogController::class, 'show'])
        ->where('permalink', '[a-z0-9-]+')
        ->name('blog.show');
});

// Non-default locales (EN, ...) keep the /{locale}/ prefix.
Route::prefix('{locale}')
    ->where(['locale' => 'en'])
    ->middleware('locale')
    ->name('localized.')
    ->group(function (): void {
        Route::inertia('/', 'Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ])->name('welcome');

        Route::get('blog', [FrontendBlogController::class, 'index'])
            ->name('blog.index');
        Route::get('blog/{permalink}', [FrontendBlogController::class, 'show'])
            ->where('permalink', '[a-z0-9-]+')
            ->name('blog.show');
    });

// Canonical: old /de/* URLs 301-redirect to the unprefixed root.
Route::get('/de/{rest?}', function (?string $rest = null) {
    $target = '/'.($rest ?? '');
    $query = request()->getQueryString();

    return redirect($query !== null ? "{$target}?{$query}" : $target, 301);
})->where('rest', '.*');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::prefix('blog')->name('blog.')->group(function (): void {
            Route::post('categories/reorder', [AdminBlogCategoryController::class, 'reorder'])
                ->name('categories.reorder');

            Route::post('categories/bulk-action', [AdminBlogCategoryController::class, 'bulkAction'])
                ->name('categories.bulk-action');

            Route::resource('categories', AdminBlogCategoryController::class)
                ->parameters(['categories' => 'category'])
                ->except('show');

            Route::post('tags/reorder', [AdminBlogTagController::class, 'reorder'])
                ->name('tags.reorder');

            Route::post('tags/bulk-action', [AdminBlogTagController::class, 'bulkAction'])
                ->name('tags.bulk-action');

            Route::resource('tags', AdminBlogTagController::class)
                ->parameters(['tags' => 'tag'])
                ->except('show');

            Route::post('posts/reorder', [AdminBlogPostController::class, 'reorder'])
                ->name('posts.reorder');

            Route::post('posts/bulk-action', [AdminBlogPostController::class, 'bulkAction'])
                ->name('posts.bulk-action');

            Route::resource('posts', AdminBlogPostController::class)
                ->parameters(['posts' => 'post'])
                ->except('show');
        });
    });
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
