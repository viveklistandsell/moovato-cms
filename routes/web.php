<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Blog\CategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\Blog\PostController as AdminBlogPostController;
use App\Http\Controllers\Admin\Blog\TagController as AdminBlogTagController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFileController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MediaPickerController;
use App\Http\Controllers\Admin\MediaTrashController;
use App\Http\Controllers\Admin\Page\CategoryController as AdminPageCategoryController;
use App\Http\Controllers\Admin\Page\PageController as AdminPageController;
use App\Http\Controllers\Admin\Page\PageWidgetController as AdminPageWidgetController;
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use Illuminate\Support\Facades\Route;

// Default-locale (DE) routes live at the root with no /de prefix.
Route::middleware('locale')->group(function (): void {
    Route::get('/', [FrontendPageController::class, 'home'])->name('home');

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

        Route::get('/', [FrontendPageController::class, 'home'])->name('home');

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

// Public page show — must be registered AFTER all other named routes so it
// doesn't shadow /blog, /admin, /login, etc. The slug regex blocks reserved
// segments at the URL boundary.
Route::middleware('locale')
    ->get('/{permalink}', [FrontendPageController::class, 'show'])
    ->where('permalink', '(?!admin|blog|de|en|login|register|dashboard|forgot-password|reset-password|email|user|two-factor-challenge|logout|settings|_boost|storage|build)[a-z0-9-]+')
    ->name('pages.show');

Route::prefix('{locale}')
    ->where(['locale' => 'en'])
    ->middleware('locale')
    ->name('localized.')
    ->get('/{permalink}', [FrontendPageController::class, 'show'])
    ->where('permalink', '(?!blog)[a-z0-9-]+')
    ->name('pages.show');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Bare /admin (and /admin/) has no landing view of its own — redirect to
    // the dashboard so authenticated visitors don't see a confusing 404.
    Route::redirect('admin', '/dashboard');

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

        Route::resource('languages', AdminLanguageController::class)
            ->parameters(['languages' => 'language'])
            ->except('show');

        Route::prefix('pages')->name('pages.')->group(function (): void {
            // Sub-resource: page categories (under /admin/pages/categories).
            Route::post('categories/reorder', [AdminPageCategoryController::class, 'reorder'])
                ->name('categories.reorder');
            Route::post('categories/bulk-action', [AdminPageCategoryController::class, 'bulkAction'])
                ->name('categories.bulk-action');
            Route::resource('categories', AdminPageCategoryController::class)
                ->parameters(['categories' => 'category'])
                ->except('show');

            // Top-level resource: pages live at /admin/pages directly.
            // Numeric constraint on {page} prevents collision with /categories.
            Route::post('bulk-action', [AdminPageController::class, 'bulkAction'])
                ->name('bulk-action');

            // Widget builder sync: full replace-all of the widget stack for a
            // given page. Must be registered BEFORE the catch-all resource
            // route so the {page} param resolves to a Page model bound here.
            Route::post('{page}/widgets', [AdminPageWidgetController::class, 'sync'])
                ->where('page', '[0-9]+')
                ->name('widgets.sync');

            Route::resource('/', AdminPageController::class)
                ->parameters(['' => 'page'])
                ->except('show')
                ->where(['page' => '[0-9]+']);
        });

        // User management — single-page CRUD via drawer (no separate create/edit
        // routes). Permission middleware gates each action; the controller's
        // own actions throw on guard violations (self-delete, last super admin).
        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [AdminUserController::class, 'index'])
                ->middleware('permission:users.view')
                ->name('index');

            Route::post('/', [AdminUserController::class, 'store'])
                ->middleware('permission:users.create')
                ->name('store');

            Route::post('bulk-action', [AdminUserController::class, 'bulkAction'])
                ->name('bulk-action');

            Route::post('{user}/reset-password', [AdminUserController::class, 'resetPassword'])
                ->middleware('permission:users.reset-password')
                ->where('user', '[0-9]+')
                ->name('reset-password');

            Route::match(['put', 'patch'], '{user}', [AdminUserController::class, 'update'])
                ->middleware('permission:users.update')
                ->where('user', '[0-9]+')
                ->name('update');

            Route::delete('{user}', [AdminUserController::class, 'destroy'])
                ->middleware('permission:users.delete')
                ->where('user', '[0-9]+')
                ->name('destroy');
        });

        // Roles — full CRUD with separate create/edit pages (the form needs
        // more vertical space than the user drawer comfortably allows).
        // Each verb is gated by its own permission so an admin without
        // roles.delete still sees the list and edit form.
        Route::prefix('roles')->name('roles.')->group(function (): void {
            Route::get('/', [AdminRoleController::class, 'index'])
                ->middleware('permission:roles.view')->name('index');
            Route::get('create', [AdminRoleController::class, 'create'])
                ->middleware('permission:roles.create')->name('create');
            Route::post('/', [AdminRoleController::class, 'store'])
                ->middleware('permission:roles.create')->name('store');
            Route::get('{role}/edit', [AdminRoleController::class, 'edit'])
                ->middleware('permission:roles.update')->where('role', '[0-9]+')->name('edit');
            Route::match(['put', 'patch'], '{role}', [AdminRoleController::class, 'update'])
                ->middleware('permission:roles.update')->where('role', '[0-9]+')->name('update');
            Route::delete('{role}', [AdminRoleController::class, 'destroy'])
                ->middleware('permission:roles.delete')->where('role', '[0-9]+')->name('destroy');
        });

        // Permissions — read-only listing. Editing is done via the role form.
        Route::get('permissions', [AdminPermissionController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('permissions.index');
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

        // In-form picker — listing + simple direct upload, image-only.
        // Same admin gate as the rest of media management.
        Route::get('media/picker', [MediaPickerController::class, 'index'])->name('media.picker.index');
        Route::post('media/picker/upload', [MediaPickerController::class, 'upload'])->name('media.picker.upload');
        Route::post('media/picker/folders', [MediaPickerController::class, 'createFolder'])->name('media.picker.folders.store');
        Route::delete('media/picker/folders/{folder}', [MediaPickerController::class, 'destroyFolder'])->name('media.picker.folders.destroy');
        Route::post('media/picker/files/move', [MediaPickerController::class, 'moveFiles'])->name('media.picker.files.move');
        Route::patch('media/picker/files/{file}', [MediaPickerController::class, 'updateFile'])->name('media.picker.files.update');
        Route::delete('media/picker/files/{file}', [MediaPickerController::class, 'destroyFile'])->name('media.picker.files.destroy');
        Route::delete('media/picker/files/{file}/force', [MediaPickerController::class, 'forceDestroyFile'])->name('media.picker.files.force-destroy');

        Route::get('media/trash', [MediaTrashController::class, 'index'])->name('media.trash.index');
        Route::post('media/trash/folders/{id}/restore', [MediaTrashController::class, 'restoreFolder'])->name('media.trash.folders.restore');
        Route::post('media/trash/files/{id}/restore', [MediaTrashController::class, 'restoreFile'])->name('media.trash.files.restore');
        Route::delete('media/trash/folders/{id}', [MediaTrashController::class, 'forceDeleteFolder'])->name('media.trash.folders.force-destroy');
        Route::delete('media/trash/files/{id}', [MediaTrashController::class, 'forceDeleteFile'])->name('media.trash.files.force-destroy');
    });

require __DIR__.'/settings.php';
