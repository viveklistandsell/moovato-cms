<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Blog\CategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\Blog\PostController as AdminBlogPostController;
use App\Http\Controllers\Admin\Blog\TagController as AdminBlogTagController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Route::inertia('/', 'Welcome', [
//     'canRegister' => Features::enabled(Features::registration()),
// ])->name('home');

Route::redirect('/', '/de')->name('home');

Route::prefix('{locale}')
    ->where(['locale' => 'de|en'])
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

require __DIR__.'/settings.php';
