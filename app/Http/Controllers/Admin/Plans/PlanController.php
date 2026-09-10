<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Plans;

use App\Enums\PlanTier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Plans\UpdatePlanRequest;
use App\Mail\PartnerPlanUpdated;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\PartnerNotification;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class PlanController extends Controller
{
    private const FEATURE_KEYS = [
        'short_description', 'about', 'founded', 'employees',
        'faqs', 'cover', 'trust', 'google', 'lead',
    ];

    private const CAP_KEYS = ['contacts', 'services', 'areas', 'gallery', 'faqs', 'reply_reviews'];

    private const PLACEMENTS = ['standard', 'boosted', 'featured'];

    public function index(): Response
    {
        $plans = Plan::query()
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Plan $p): array => $this->present($p, withCounts: true))
            ->values()
            ->all();

        return Inertia::render('admin/plans/Index', [
            'plans' => $plans,
        ]);
    }

    public function edit(string $plan): Response
    {
        $row = Plan::query()->where('slug', $plan)->first();
        if ($row === null) {
            throw new NotFoundHttpException;
        }

        return Inertia::render('admin/plans/Edit', [
            'plan' => $this->present($row),
            'feature_keys' => self::FEATURE_KEYS,
            'cap_keys' => self::CAP_KEYS,
            'placements' => self::PLACEMENTS,
            'company_count' => Company::query()->where('plan_tier', $row->slug)->count(),
        ]);
    }

    public function update(UpdatePlanRequest $request, string $plan): RedirectResponse
    {
        $row = Plan::query()->where('slug', $plan)->first();
        if ($row === null) {
            throw new NotFoundHttpException;
        }

        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $caps = [];
        foreach (self::CAP_KEYS as $key) {
            $v = $data['caps'][$key] ?? null;
            $caps[$key] = ($v === null || $v === '') ? null : (int) $v;
        }

        $features = [];
        foreach (self::FEATURE_KEYS as $key) {
            $features[$key] = (bool) ($data['features'][$key] ?? false);
        }

        $before = [
            'label' => (string) $row->label,
            'price' => (int) $row->price,
            'currency' => (string) $row->currency,
            'period' => (string) $row->period,
            'placement' => (string) $row->placement,
            'features' => (array) ($row->features ?? []),
            'caps' => (array) ($row->caps ?? []),
        ];

        $row->update([
            'label' => (string) $data['label'],
            'price' => (int) $data['price'],
            'currency' => (string) $data['currency'],
            'period' => (string) $data['period'],
            'positioning' => $data['positioning'] ?? null,
            'placement' => (string) $data['placement'],
            'lead_url' => $this->trimToNull($data['lead_url'] ?? null),
            'lead_label' => $this->trimToNull($data['lead_label'] ?? null),
            'is_active' => (bool) $data['is_active'],
            'features' => $features,
            'caps' => $caps,
        ]);

        $after = [
            'label' => (string) $data['label'],
            'price' => (int) $data['price'],
            'currency' => (string) $data['currency'],
            'period' => (string) $data['period'],
            'placement' => (string) $data['placement'],
            'features' => $features,
            'caps' => $caps,
        ];

        $notified = $this->notifyPartnersOnPlan($row->fresh(), $before, $after);

        $flash = $notified > 0
            ? __('admin.plans.flash.updated_with_notify', ['name' => $row->label, 'n' => $notified])
            : __('admin.plans.flash.updated', ['name' => $row->label]);

        return redirect()
            ->route('admin.plans.index')
            ->with('toast', ['type' => 'success', 'message' => $flash]);
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return int Number of partners emailed (0 if nothing changed
     *             OR nobody is on this tier).
     */
    private function notifyPartnersOnPlan(Plan $plan, array $before, array $after): int
    {
        $changes = $this->diff($before, $after);
        if ($changes === []) {
            return 0;
        }

        $partners = CompanyUser::query()
            ->where('status', CompanyUser::STATUS_APPROVED)
            ->whereNotNull('company_id')
            ->whereHas('company', fn ($q) => $q->where('plan_tier', $plan->slug))
            ->with(['company:id,primary_city_id', 'company.translations:id,company_id,lang,name,permalink'])
            ->get();

        $sent = 0;
        foreach ($partners as $partner) {
            $company = $partner->company;
            if ($company === null || $partner->email === null || $partner->email === '') {
                continue;
            }

            PartnerNotification::query()->create([
                'company_user_id' => (int) $partner->id,
                'type' => PartnerNotification::TYPE_PLAN_UPDATED,
                'title' => __('partner.notifications.plan_updated_title', ['plan' => $plan->label]),
                'body' => __('partner.notifications.plan_updated_body'),
                'data' => [
                    'plan_slug' => (string) $plan->slug,
                    'plan_label' => (string) $plan->label,
                    'changes' => $changes,
                ],
            ]);

            try {
                Mail::to($partner->email)->send(new PartnerPlanUpdated(
                    $partner,
                    $company,
                    (string) $plan->label,
                    $changes,
                ));
                $sent++;
            } catch (Throwable $e) {
                report($e);
            }
        }

        return $sent;
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @return array<int, array{label:string, from:string, to:string}>
     */
    private function diff(array $before, array $after): array
    {
        $changes = [];
        $currency = (string) ($after['currency'] ?? '€');
        $period = (string) ($after['period'] ?? 'month');

        if ((int) $before['price'] !== (int) $after['price']) {
            $changes[] = [
                'label' => __('admin.plans.label_price'),
                'from' => $currency.$before['price'].' / '.$period,
                'to' => $currency.$after['price'].' / '.$period,
            ];
        }

        if ((string) $before['placement'] !== (string) $after['placement']) {
            $changes[] = [
                'label' => __('admin.plans.label_placement'),
                'from' => (string) $before['placement'],
                'to' => (string) $after['placement'],
            ];
        }

        foreach (self::FEATURE_KEYS as $key) {
            $b = (bool) ($before['features'][$key] ?? false);
            $a = (bool) ($after['features'][$key] ?? false);
            if ($b !== $a) {
                $changes[] = [
                    'label' => __('admin.plans.feat_'.$key),
                    'from' => $b ? '✓' : '—',
                    'to' => $a ? '✓' : '—',
                ];
            }
        }

        foreach (self::CAP_KEYS as $key) {
            $b = $before['caps'][$key] ?? null;
            $a = $after['caps'][$key] ?? null;
            if ($b !== $a) {
                $changes[] = [
                    'label' => __('admin.plans.cap_'.$key),
                    'from' => $b === null ? __('admin.plans.unlimited') : (string) $b,
                    'to' => $a === null ? __('admin.plans.unlimited') : (string) $a,
                ];
            }
        }

        return $changes;
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Plan $p, bool $withCounts = false): array
    {
        $out = [
            'slug' => (string) $p->slug,
            'label' => (string) $p->label,
            'price' => (int) $p->price,
            'currency' => (string) $p->currency,
            'period' => (string) $p->period,
            'positioning' => $p->positioning,
            'placement' => (string) $p->placement,
            'lead_url' => $p->lead_url,
            'lead_label' => $p->lead_label,
            'features' => (array) ($p->features ?? []),
            'caps' => (array) ($p->caps ?? []),
            'sort_order' => (int) $p->sort_order,
            'is_active' => (bool) $p->is_active,
            'is_free' => PlanTier::from($p->slug)->isFree(),
        ];

        if ($withCounts) {
            $out['company_count'] = Company::query()->where('plan_tier', $p->slug)->count();
        }

        return $out;
    }
    private function trimToNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $trimmed = mb_trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
