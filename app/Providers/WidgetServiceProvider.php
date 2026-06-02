<?php

declare(strict_types=1);

namespace App\Providers;

use App\Widgets\About\AboutWidget;
use App\Widgets\Banner\BannerWidget;
use App\Widgets\Blog\BlogWidget;
use App\Widgets\CompanyDirectory\CompanyDirectoryWidget;
use App\Widgets\Comparison\ComparisonWidget;
use App\Widgets\Contracts\WidgetContract;
use App\Widgets\Cta\CtaWidget;
use App\Widgets\Faq\FaqWidget;
use App\Widgets\FaqMedia\FaqMediaWidget;
use App\Widgets\Features\FeaturesWidget;
use App\Widgets\Gallery\GalleryWidget;
use App\Widgets\Hero\HeroWidget;
use App\Widgets\Heronew\HeronewWidget;
use App\Widgets\HowItWorks\HowItWorksWidget;
use App\Widgets\Image\ImageWidget;
use App\Widgets\Map\MapWidget;
use App\Widgets\Marquee\MarqueeWidget;
use App\Widgets\Partners\PartnersWidget;
use App\Widgets\Pricing\PricingWidget;
use App\Widgets\PromoCta\PromoCtaWidget;
use App\Widgets\QuoteForm\QuoteFormWidget;
use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\Reviews\ReviewsWidget;
use App\Widgets\ServiceCards\ServiceCardsWidget;
use App\Widgets\Testimonial\TestimonialWidget;
use App\Widgets\TextBlock\TextBlockWidget;
use App\Widgets\TrustBar\TrustBarWidget;
use App\Widgets\WhyChooseUs\WhyChooseUsWidget;
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
        HeronewWidget::class,
        TrustBarWidget::class,
        MarqueeWidget::class,
        QuoteFormWidget::class,
        ServiceCardsWidget::class,
        HowItWorksWidget::class,
        PricingWidget::class,
        ReviewsWidget::class,
        WhyChooseUsWidget::class,
        PartnersWidget::class,
        FaqMediaWidget::class,
        BlogWidget::class,
        MapWidget::class,
        PromoCtaWidget::class,
        AboutWidget::class,
        ComparisonWidget::class,
        CompanyDirectoryWidget::class,
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
