<?php

declare(strict_types=1);

namespace App\Providers;

use App\Widgets\Banner\BannerWidget;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\Cta\CtaWidget;
use App\Widgets\Faq\FaqWidget;
use App\Widgets\Features\FeaturesWidget;
use App\Widgets\Gallery\GalleryWidget;
use App\Widgets\Hero\HeroWidget;
use App\Widgets\Image\ImageWidget;
use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\Testimonial\TestimonialWidget;
use App\Widgets\TextBlock\TextBlockWidget;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Registers every concrete widget with the singleton WidgetRegistry. To add a
 * new widget, drop a new class implementing WidgetContract under app/Widgets
 * and append it to the list below.
 */
final class WidgetServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /** @var array<int, class-string<WidgetContract>> */
    public const WIDGETS = [
        HeroWidget::class,
        BannerWidget::class,
        TextBlockWidget::class,
        ImageWidget::class,
        FeaturesWidget::class,
        CtaWidget::class,
        FaqWidget::class,
        TestimonialWidget::class,
        GalleryWidget::class,
    ];

    public function register(): void
    {
        $this->app->singleton(WidgetRegistry::class, function (): WidgetRegistry {
            $registry = new WidgetRegistry();
            $registry->registerMany(self::WIDGETS);

            return $registry;
        });
    }

    /** @return array<int, string> */
    public function provides(): array
    {
        return [WidgetRegistry::class];
    }
}
