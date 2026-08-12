<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminLocaleController;
use App\Http\Controllers\Admin\Blog\CategoryController as AdminBlogCategoryController;
use App\Http\Controllers\Admin\Blog\PostController as AdminBlogPostController;
use App\Http\Controllers\Admin\Blog\TagController as AdminBlogTagController;
use App\Http\Controllers\Admin\Companies\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Directory\CityController as AdminCityController;
use App\Http\Controllers\Admin\Directory\CountryController as AdminCountryController;
use App\Http\Controllers\Admin\Directory\DistrictController as AdminDistrictController;
use App\Http\Controllers\Admin\Directory\StateController as AdminStateController;
use App\Http\Controllers\Admin\LanguageController as AdminLanguageController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MediaFileController;
use App\Http\Controllers\Admin\MediaFolderController;
use App\Http\Controllers\Admin\MediaPickerController;
use App\Http\Controllers\Admin\MediaTrashController;
use App\Http\Controllers\Admin\Navigation\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\Navigation\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\Admin\Page\CategoryController as AdminPageCategoryController;
use App\Http\Controllers\Admin\Page\PageController as AdminPageController;
use App\Http\Controllers\Admin\Page\PageWidgetController as AdminPageWidgetController;
use App\Http\Controllers\Admin\Reviews\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SearchController as AdminSearchController;
use App\Http\Controllers\Admin\Service\CategoryController as AdminServiceCategoryController;
use App\Http\Controllers\Admin\Service\ParentCategoryController as AdminServiceParentCategoryController;
use App\Http\Controllers\Admin\Settings\EmailSettingsController as AdminEmailSettingsController;
use App\Http\Controllers\Admin\Settings\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\System\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\System\CacheController as AdminCacheController;
use App\Http\Controllers\Admin\System\EmailLogController as AdminEmailLogController;
use App\Http\Controllers\Admin\System\ExportController as AdminExportController;
use App\Http\Controllers\Admin\System\HealthController as AdminHealthController;
use App\Http\Controllers\Admin\System\SeoController as AdminSeoSystemController;
use App\Http\Controllers\Admin\User\PermissionController as AdminPermissionController;
use App\Http\Controllers\Admin\User\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\User\UserController as AdminUserController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\CompanyController as FrontendCompanyController;
use App\Http\Controllers\Frontend\CompanyReviewController as FrontendCompanyReviewController;
use App\Http\Controllers\Frontend\CookieConsentController as FrontendCookieConsentController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\PlaceSearchController;
use App\Http\Controllers\Frontend\RobotsController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Partner\Auth\RegisterController as PartnerRegisterController;
use App\Http\Controllers\Portal\CompanyProfileController as PortalCompanyProfileController;
use App\Http\Controllers\StorageFallbackController;
use Illuminate\Support\Facades\Route;

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', RobotsController::class)->name('robots');

Route::get('storage/{path}', StorageFallbackController::class)
    ->where('path', '.*')
    ->name('storage.fallback');

// Cookie consent audit endpoint
Route::post('cookie-consent', [FrontendCookieConsentController::class, 'store'])
    ->middleware('throttle:30,1')
    ->name('cookie-consent.store');

// Google Places proxy used by the moving cost calculator widget.
Route::middleware('throttle:60,1')->group(function (): void {
    Route::get('places/suggest', [PlaceSearchController::class, 'suggest'])
        ->name('places.suggest');
    Route::get('places/resolve', [PlaceSearchController::class, 'resolve'])
        ->name('places.resolve');
});

// Default-locale (DE) routes live at the root with no /de prefix.
Route::middleware('locale')->group(function (): void {
    Route::get('/', [FrontendPageController::class, 'home'])->name('home');

    Route::get('blog', [FrontendBlogController::class, 'index'])
        ->name('blog.index');
    Route::get('blog/{permalink}', [FrontendBlogController::class, 'show'])
        ->where('permalink', '[a-z0-9-]+')
        ->name('blog.show');

    Route::get('companies', [FrontendCompanyController::class, 'index'])
        ->name('companies.index');
    Route::get('company/{permalink}', [FrontendCompanyController::class, 'show'])
        ->where('permalink', '[a-z0-9-]+')
        ->name('companies.show');
    Route::get('company/{slug}/review', [FrontendCompanyReviewController::class, 'create'])
        ->where('slug', '[a-z0-9-]+')
        ->name('companies.review.create');
    Route::post('company/{slug}/review', [FrontendCompanyReviewController::class, 'store'])
        ->where('slug', '[a-z0-9-]+')
        ->middleware('throttle:5,60')
        ->name('companies.review.store');
    Route::get('company/{slug}/review/thanks', [FrontendCompanyReviewController::class, 'thanks'])
        ->where('slug', '[a-z0-9-]+')
        ->name('companies.review.thanks');
    Route::post('reviews/{review}/helpful', [FrontendCompanyReviewController::class, 'helpful'])
        ->whereNumber('review')
        ->middleware('throttle:60,1')
        ->name('reviews.helpful');
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

        Route::get('companies', [FrontendCompanyController::class, 'index'])
            ->name('companies.index');
        Route::get('company/{permalink}', [FrontendCompanyController::class, 'show'])
            ->where('permalink', '[a-z0-9-]+')
            ->name('companies.show');
        Route::get('company/{slug}/review', [FrontendCompanyReviewController::class, 'create'])
            ->where('slug', '[a-z0-9-]+')
            ->name('companies.review.create');
        Route::post('company/{slug}/review', [FrontendCompanyReviewController::class, 'store'])
            ->where('slug', '[a-z0-9-]+')
            ->middleware('throttle:5,60')
            ->name('companies.review.store');
        Route::get('company/{slug}/review/thanks', [FrontendCompanyReviewController::class, 'thanks'])
            ->where('slug', '[a-z0-9-]+')
            ->name('companies.review.thanks');
        Route::post('reviews/{review}/helpful', [FrontendCompanyReviewController::class, 'helpful'])
            ->whereNumber('review')
            ->middleware('throttle:60,1')
            ->name('reviews.helpful');
    });

// Canonical: old /de/* URLs 301-redirect to the unprefixed root.
Route::get('/de/{rest?}', function (?string $rest = null) {
    $target = '/'.($rest ?? '');
    $query = request()->getQueryString();

    return redirect($query !== null ? "{$target}?{$query}" : $target, 301);
})->where('rest', '.*');

Route::middleware('locale')
    ->get('/{permalink}', [FrontendPageController::class, 'show'])
    ->where('permalink', '(?!admin|blog|companies|company|de|en|login|register|dashboard|forgot-password|reset-password|email|user|two-factor-challenge|logout|settings|_boost|storage|build)[a-z0-9-]+')
    ->name('pages.show');

Route::prefix('{locale}')
    ->where(['locale' => 'en'])
    ->middleware('locale')
    ->name('localized.')
    ->get('/{permalink}', [FrontendPageController::class, 'show'])
    ->where('permalink', '(?!blog|companies|company)[a-z0-9-]+')
    ->name('pages.show');

Route::middleware(['auth', 'verified', 'admin.locale'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Header language switcher — persists user.admin_locale.
    Route::patch('admin/locale', AdminLocaleController::class)
        ->name('admin.locale.update');

    // Bare /admin (and /admin/) has no landing view of its own — redirect to
    // the dashboard so authenticated visitors don't see a confusing 404.
    Route::redirect('admin', '/dashboard');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('search', AdminSearchController::class)->name('search');
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

        Route::prefix('directory')->name('directory.')->middleware('permission:locations.view')->group(function (): void {
            Route::post('countries/reorder', [AdminCountryController::class, 'reorder'])
                ->name('countries.reorder');
            Route::post('countries/bulk-action', [AdminCountryController::class, 'bulkAction'])
                ->name('countries.bulk-action');
            Route::resource('countries', AdminCountryController::class)
                ->parameters(['countries' => 'country'])
                ->except('show');

            Route::post('states/reorder', [AdminStateController::class, 'reorder'])
                ->name('states.reorder');
            Route::post('states/bulk-action', [AdminStateController::class, 'bulkAction'])
                ->name('states.bulk-action');
            Route::get('states/export', [AdminStateController::class, 'export'])
                ->name('states.export');
            Route::get('states/sample-csv', [AdminStateController::class, 'sampleCsv'])
                ->name('states.sample-csv');
            Route::post('states/import', [AdminStateController::class, 'import'])
                ->name('states.import');
            Route::resource('states', AdminStateController::class)
                ->parameters(['states' => 'state'])
                ->except('show');

            Route::post('cities/reorder', [AdminCityController::class, 'reorder'])
                ->name('cities.reorder');
            Route::post('cities/bulk-action', [AdminCityController::class, 'bulkAction'])
                ->name('cities.bulk-action');
            Route::get('cities/export', [AdminCityController::class, 'export'])
                ->name('cities.export');
            Route::get('cities/sample-csv', [AdminCityController::class, 'sampleCsv'])
                ->name('cities.sample-csv');
            Route::post('cities/import', [AdminCityController::class, 'import'])
                ->name('cities.import');
            Route::resource('cities', AdminCityController::class)
                ->parameters(['cities' => 'city'])
                ->except('show');
            Route::post('districts/reorder', [AdminDistrictController::class, 'reorder'])
                ->name('districts.reorder');
            Route::post('districts/bulk-action', [AdminDistrictController::class, 'bulkAction'])
                ->name('districts.bulk-action');
            Route::get('districts/export', [AdminDistrictController::class, 'export'])
                ->name('districts.export');
            Route::get('districts/sample-csv', [AdminDistrictController::class, 'sampleCsv'])
                ->name('districts.sample-csv');
            Route::post('districts/import', [AdminDistrictController::class, 'import'])
                ->name('districts.import');
            Route::resource('districts', AdminDistrictController::class)
                ->parameters(['districts' => 'district'])
                ->except('show');
        });

        Route::prefix('services')->name('services.')->middleware('permission:service_categories.view')->group(function (): void {
            Route::post('categories/reorder', [AdminServiceCategoryController::class, 'reorder'])
                ->name('categories.reorder');
            Route::post('categories/bulk-action', [AdminServiceCategoryController::class, 'bulkAction'])
                ->name('categories.bulk-action');
            Route::resource('categories', AdminServiceCategoryController::class)
                ->parameters(['categories' => 'category'])
                ->except('show');
            Route::post('parent-categories/reorder', [AdminServiceParentCategoryController::class, 'reorder'])
                ->name('parent-categories.reorder');
            Route::post('parent-categories/bulk-action', [AdminServiceParentCategoryController::class, 'bulkAction'])
                ->name('parent-categories.bulk-action');
            Route::get('parent-categories/options', [AdminServiceParentCategoryController::class, 'options'])
                ->name('parent-categories.options');
            Route::get('parent-categories/export', [AdminServiceParentCategoryController::class, 'export'])
                ->name('parent-categories.export');
            Route::get('parent-categories/sample-csv', [AdminServiceParentCategoryController::class, 'sampleCsv'])
                ->name('parent-categories.sample-csv');
            Route::post('parent-categories/import', [AdminServiceParentCategoryController::class, 'import'])
                ->name('parent-categories.import');
            Route::resource('parent-categories', AdminServiceParentCategoryController::class)
                ->parameters(['parent-categories' => 'category'])
                ->except('show');
        });

        Route::middleware('permission:companies.view')->group(function (): void {
            Route::post('companies/reorder', [AdminCompanyController::class, 'reorder'])
                ->name('companies.reorder');
            Route::post('companies/bulk-action', [AdminCompanyController::class, 'bulkAction'])
                ->name('companies.bulk-action');
            Route::get('companies/lookup', [AdminCompanyController::class, 'lookup'])
                ->name('companies.lookup');
            Route::resource('companies', AdminCompanyController::class)
                ->parameters(['companies' => 'company'])
                ->except('show');
            Route::get('reviews/{review}/proof', [AdminReviewController::class, 'downloadProof'])
                ->whereNumber('review')
                ->name('reviews.proof');
            Route::post('reviews/{review}/reply', [AdminReviewController::class, 'reply'])
                ->whereNumber('review')
                ->name('reviews.reply');
            Route::delete('reviews/{review}/reply', [AdminReviewController::class, 'destroyReply'])
                ->whereNumber('review')
                ->name('reviews.reply.destroy');
            Route::post('reviews/{review}/status', [AdminReviewController::class, 'setStatus'])
                ->whereNumber('review')
                ->name('reviews.status');
            Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])
                ->whereNumber('review')
                ->name('reviews.destroy');
        });

        Route::prefix('pages')->name('pages.')->group(function (): void {
            Route::post('categories/reorder', [AdminPageCategoryController::class, 'reorder'])
                ->name('categories.reorder');
            Route::post('categories/bulk-action', [AdminPageCategoryController::class, 'bulkAction'])
                ->name('categories.bulk-action');
            Route::resource('categories', AdminPageCategoryController::class)
                ->parameters(['categories' => 'category'])
                ->except('show');

            Route::post('bulk-action', [AdminPageController::class, 'bulkAction'])
                ->name('bulk-action');

            Route::post('{page}/duplicate', [AdminPageController::class, 'duplicate'])
                ->where('page', '[0-9]+')
                ->name('duplicate');

            Route::post('{page}/widgets', [AdminPageWidgetController::class, 'sync'])
                ->where('page', '[0-9]+')
                ->name('widgets.sync');

            Route::resource('/', AdminPageController::class)
                ->parameters(['' => 'page'])
                ->except('show')
                ->where(['page' => '[0-9]+']);
        });

        Route::prefix('users')->name('users.')->group(function (): void {
            Route::get('/', [AdminUserController::class, 'index'])
                ->middleware('permission:users.view')
                ->name('index');
            Route::get('create', [AdminUserController::class, 'create'])
                ->middleware('permission:users.create')
                ->name('create');

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

        Route::prefix('site-settings')->name('site-settings.')->group(function (): void {
            Route::get('/', [AdminSiteSettingController::class, 'edit'])
                ->middleware('permission:settings.site')->name('edit');
            Route::match(['put', 'patch'], '/', [AdminSiteSettingController::class, 'update'])
                ->middleware('permission:settings.site')->name('update');
        });
        Route::prefix('settings')
            ->name('settings.')
            ->middleware('permission:settings.site')
            ->group(function (): void {
                foreach ([
                    'identity' => 'identity',
                    'branding' => 'branding',
                    'seo' => 'seo',
                    'legal' => 'legal',
                    'analytics' => 'analytics',
                    'maintenance' => 'maintenance',
                    'layout' => 'layout',
                    'security' => 'security',
                ] as $slug => $method) {
                    Route::get($slug, [AdminSettingsController::class, $method])
                        ->name($slug);
                    Route::match(['put', 'patch'], $slug, [
                        AdminSettingsController::class,
                        'update'.ucfirst($method),
                    ])->name($slug.'.update');
                }
            });

        Route::prefix('settings/email')->name('settings.email.')->middleware('permission:settings.email')->group(function (): void {
            Route::get('/', [AdminEmailSettingsController::class, 'edit'])->name('edit');
            Route::match(['put', 'patch'], '/', [AdminEmailSettingsController::class, 'update'])->name('update');
            Route::post('test', [AdminEmailSettingsController::class, 'sendTest'])->name('test');
        });

        Route::prefix('system')->name('system.')->group(function (): void {
            Route::get('cache', [AdminCacheController::class, 'index'])
                ->middleware('permission:system.cache')
                ->name('cache');
            Route::post('cache/clear', [AdminCacheController::class, 'clear'])
                ->middleware('permission:system.cache')
                ->name('cache.clear');
            Route::get('sitemap', [AdminSeoSystemController::class, 'sitemap'])
                ->middleware('permission:system.cache')
                ->name('sitemap');
            Route::post('sitemap/flush', [AdminSeoSystemController::class, 'flushSitemap'])
                ->middleware('permission:system.cache')
                ->name('sitemap.flush');
            Route::get('sitemap/download', [AdminSeoSystemController::class, 'downloadSitemap'])
                ->middleware('permission:system.cache')
                ->name('sitemap.download');

            Route::get('robots', [AdminSeoSystemController::class, 'robots'])
                ->middleware('permission:system.cache')
                ->name('robots');
            Route::get('activity', [AdminActivityLogController::class, 'index'])
                ->middleware('permission:settings.activity')
                ->name('activity');
            Route::get('email-log', [AdminEmailLogController::class, 'index'])
                ->middleware('permission:settings.activity')
                ->name('email-log.index');
            Route::get('email-log/{emailLog}', [AdminEmailLogController::class, 'show'])
                ->middleware('permission:settings.activity')
                ->where('emailLog', '[0-9]+')
                ->name('email-log.show');
            Route::delete('email-log/{emailLog}', [AdminEmailLogController::class, 'destroy'])
                ->middleware('permission:settings.activity')
                ->where('emailLog', '[0-9]+')
                ->name('email-log.destroy');
            Route::get('export', [AdminExportController::class, 'index'])
                ->middleware('permission:system.cache')
                ->name('export');
            Route::get('export/pages', [AdminExportController::class, 'pages'])
                ->middleware('permission:system.cache')
                ->name('export.pages');
            Route::get('export/posts', [AdminExportController::class, 'posts'])
                ->middleware('permission:system.cache')
                ->name('export.posts');
            Route::get('export/database', [AdminExportController::class, 'database'])
                ->middleware('permission:system.cache')
                ->name('export.database');
            Route::get('health', [AdminHealthController::class, 'index'])
                ->middleware('permission:system.cache')
                ->name('health');
        });

        Route::prefix('menus')->name('menus.')->group(function (): void {
            Route::get('/', [AdminMenuController::class, 'index'])
                ->middleware('permission:menus.view')->name('index');
            Route::get('{menu:key}', [AdminMenuController::class, 'edit'])
                ->middleware('permission:menus.view')->name('edit');
            Route::post('{menu:key}/items', [AdminMenuController::class, 'storeItem'])
                ->middleware('permission:menus.create')->name('items.store');
            Route::post('{menu:key}/items/bulk-add', [AdminMenuController::class, 'bulkAddItems'])
                ->middleware('permission:menus.create')->name('items.bulk-add');
            Route::match(['put', 'patch'], '{menu:key}/items/{item}', [AdminMenuController::class, 'updateItem'])
                ->middleware('permission:menus.update')->where('item', '[0-9]+')->name('items.update');
            Route::delete('{menu:key}/items/{item}', [AdminMenuController::class, 'destroyItem'])
                ->middleware('permission:menus.delete')->where('item', '[0-9]+')->name('items.destroy');
            Route::post('{menu:key}/items/reorder', [AdminMenuController::class, 'reorder'])
                ->middleware('permission:menus.update')->name('items.reorder');
        });
    });
});

Route::get('company-portal/{company}/{slug?}', [PortalCompanyProfileController::class, 'show'])
    ->whereNumber('company')
    ->where('slug', '[a-z0-9-]+')
    ->middleware('locale')
    ->name('portal.company.profile');

// Partner (company owner) auth stack — SEPARATE from admin auth.
// Uses the `company` guard (see config/auth.php). Live surfaces so
// far: register + thanks (this phase). Login / logout / forgot /
// reset land in Phase 3.
Route::middleware(['locale'])->prefix('partner')->name('partner.')->group(function (): void {
    Route::get('register', [PartnerRegisterController::class, 'create'])
        ->name('register');
    Route::post('register', [PartnerRegisterController::class, 'store'])
        ->middleware('throttle:5,60')
        ->name('register.store');
    Route::get('register/thanks', [PartnerRegisterController::class, 'thanks'])
        ->name('register.thanks');
});
Route::post('company-portal/{company}/name', [PortalCompanyProfileController::class, 'updateName'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-name');
Route::post('company-portal/{company}/founded', [PortalCompanyProfileController::class, 'updateFounded'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-founded');
Route::post('company-portal/{company}/employees', [PortalCompanyProfileController::class, 'updateEmployees'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-employees');
Route::post('company-portal/{company}/website', [PortalCompanyProfileController::class, 'updateWebsite'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-website');
Route::post('company-portal/{company}/trust', [PortalCompanyProfileController::class, 'updateTrust'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-trust');
Route::post('company-portal/{company}/about', [PortalCompanyProfileController::class, 'updateAbout'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-about');
Route::post('company-portal/{company}/short-description', [PortalCompanyProfileController::class, 'updateShortDescription'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-short-description');
Route::post('company-portal/{company}/google', [PortalCompanyProfileController::class, 'updateGoogle'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-google');
Route::post('company-portal/{company}/address', [PortalCompanyProfileController::class, 'updateAddress'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-address');
Route::post('company-portal/{company}/contacts', [PortalCompanyProfileController::class, 'updateContacts'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-contacts');
Route::post('company-portal/{company}/services', [PortalCompanyProfileController::class, 'updateServices'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-services');
Route::post('company-portal/{company}/areas', [PortalCompanyProfileController::class, 'updateAreas'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-areas');
Route::post('company-portal/{company}/faqs', [PortalCompanyProfileController::class, 'updateFaqs'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-faqs');
Route::post('company-portal/{company}/branding', [PortalCompanyProfileController::class, 'updateBranding'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:10,1'])
    ->name('portal.company.update-branding');
Route::post('company-portal/{company}/gallery', [PortalCompanyProfileController::class, 'addGalleryImage'])
    ->whereNumber('company')
    ->middleware(['locale', 'throttle:20,1'])
    ->name('portal.company.gallery-add');
Route::delete('company-portal/{company}/gallery/{media}', [PortalCompanyProfileController::class, 'deleteGalleryImage'])
    ->whereNumber('company')
    ->whereNumber('media')
    ->middleware(['locale', 'throttle:20,1'])
    ->name('portal.company.gallery-delete');

Route::middleware(['auth', 'verified', 'admin', 'admin.locale'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::get('media/lookup', [MediaController::class, 'lookup'])->name('media.lookup');

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
