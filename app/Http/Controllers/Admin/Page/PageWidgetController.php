<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\Widget\SyncPageWidgetsRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;

final class PageWidgetController extends Controller
{
    public function sync(
        SyncPageWidgetsRequest $request,
        Page $page,
        SyncPageWidgets $action,
    ): RedirectResponse {
        /** @var array{widgets: array<int, array<string, mixed>>} $data */
        $data = $request->validated();

        $action->handle($page, $data['widgets']);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Widgets saved.',
        ]);
    }
}
