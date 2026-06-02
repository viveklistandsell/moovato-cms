import AboutEditor from './About/AboutEditor.vue';
import AboutRenderer from './About/AboutRenderer.vue';
import BannerEditor from './Banner/BannerEditor.vue';
import BannerRenderer from './Banner/BannerRenderer.vue';
import BlogEditor from './Blog/BlogEditor.vue';
import BlogRenderer from './Blog/BlogRenderer.vue';
import CompanyDirectoryEditor from './CompanyDirectory/CompanyDirectoryEditor.vue';
import CompanyDirectoryRenderer from './CompanyDirectory/CompanyDirectoryRenderer.vue';
import ComparisonEditor from './Comparison/ComparisonEditor.vue';
import ComparisonRenderer from './Comparison/ComparisonRenderer.vue';
import CtaEditor from './Cta/CtaEditor.vue';
import CtaRenderer from './Cta/CtaRenderer.vue';
import FaqEditor from './Faq/FaqEditor.vue';
import FaqRenderer from './Faq/FaqRenderer.vue';
import FaqMediaEditor from './FaqMedia/FaqMediaEditor.vue';
import FaqMediaRenderer from './FaqMedia/FaqMediaRenderer.vue';
import FeaturesEditor from './Features/FeaturesEditor.vue';
import FeaturesRenderer from './Features/FeaturesRenderer.vue';
import GalleryEditor from './Gallery/GalleryEditor.vue';
import GalleryRenderer from './Gallery/GalleryRenderer.vue';
import HeroEditor from './Hero/HeroEditor.vue';
import HeroRenderer from './Hero/HeroRenderer.vue';
import HeronewEditor from './Heronew/HeronewEditor.vue';
import HeronewRenderer from './Heronew/HeronewRenderer.vue';
import HowItWorksEditor from './HowItWorks/HowItWorksEditor.vue';
import HowItWorksRenderer from './HowItWorks/HowItWorksRenderer.vue';
import ImageEditor from './Image/ImageEditor.vue';
import ImageRenderer from './Image/ImageRenderer.vue';
import MapEditor from './Map/MapEditor.vue';
import MapRenderer from './Map/MapRenderer.vue';
import MarqueeEditor from './Marquee/MarqueeEditor.vue';
import MarqueeRenderer from './Marquee/MarqueeRenderer.vue';
import PartnersEditor from './Partners/PartnersEditor.vue';
import PartnersRenderer from './Partners/PartnersRenderer.vue';
import PricingEditor from './Pricing/PricingEditor.vue';
import PricingRenderer from './Pricing/PricingRenderer.vue';
import PromoCtaEditor from './PromoCta/PromoCtaEditor.vue';
import PromoCtaRenderer from './PromoCta/PromoCtaRenderer.vue';
import QuoteFormEditor from './QuoteForm/QuoteFormEditor.vue';
import QuoteFormRenderer from './QuoteForm/QuoteFormRenderer.vue';
import ReviewsEditor from './Reviews/ReviewsEditor.vue';
import ReviewsRenderer from './Reviews/ReviewsRenderer.vue';
import ServiceCardsEditor from './ServiceCards/ServiceCardsEditor.vue';
import ServiceCardsRenderer from './ServiceCards/ServiceCardsRenderer.vue';
import TestimonialEditor from './Testimonial/TestimonialEditor.vue';
import TestimonialRenderer from './Testimonial/TestimonialRenderer.vue';
import TextBlockEditor from './TextBlock/TextBlockEditor.vue';
import TextBlockRenderer from './TextBlock/TextBlockRenderer.vue';
import TrustBarEditor from './TrustBar/TrustBarEditor.vue';
import TrustBarRenderer from './TrustBar/TrustBarRenderer.vue';
import WhyChooseUsEditor from './WhyChooseUs/WhyChooseUsEditor.vue';
import WhyChooseUsRenderer from './WhyChooseUs/WhyChooseUsRenderer.vue';
import type { WidgetRegistry } from './types';

/**
 * Frontend widget registry. To add a new widget, drop a new folder with an
 * Editor + Renderer pair, then append an entry here. The backend keeps its own
 * registry (app/Widgets/Registry/WidgetRegistry.php) — keep the slugs in sync.
 */
export const widgetRegistry: WidgetRegistry = {
    hero: { editor: HeroEditor, renderer: HeroRenderer },
    heronew: { editor: HeronewEditor, renderer: HeronewRenderer },
    banner: { editor: BannerEditor, renderer: BannerRenderer },
    text_block: { editor: TextBlockEditor, renderer: TextBlockRenderer },
    image: { editor: ImageEditor, renderer: ImageRenderer },
    features: { editor: FeaturesEditor, renderer: FeaturesRenderer },
    cta: { editor: CtaEditor, renderer: CtaRenderer },
    faq: { editor: FaqEditor, renderer: FaqRenderer },
    testimonial: { editor: TestimonialEditor, renderer: TestimonialRenderer },
    gallery: { editor: GalleryEditor, renderer: GalleryRenderer },
    trust_bar: { editor: TrustBarEditor, renderer: TrustBarRenderer },
    marquee: { editor: MarqueeEditor, renderer: MarqueeRenderer },
    quote_form: { editor: QuoteFormEditor, renderer: QuoteFormRenderer },
    service_cards: {
        editor: ServiceCardsEditor,
        renderer: ServiceCardsRenderer,
    },
    how_it_works: {
        editor: HowItWorksEditor,
        renderer: HowItWorksRenderer,
    },
    pricing: { editor: PricingEditor, renderer: PricingRenderer },
    reviews: { editor: ReviewsEditor, renderer: ReviewsRenderer },
    why_choose_us: {
        editor: WhyChooseUsEditor,
        renderer: WhyChooseUsRenderer,
    },
    partners: { editor: PartnersEditor, renderer: PartnersRenderer },
    faq_media: { editor: FaqMediaEditor, renderer: FaqMediaRenderer },
    blog: { editor: BlogEditor, renderer: BlogRenderer },
    map: { editor: MapEditor, renderer: MapRenderer },
    promo_cta: { editor: PromoCtaEditor, renderer: PromoCtaRenderer },
    about: { editor: AboutEditor, renderer: AboutRenderer },
    comparison: { editor: ComparisonEditor, renderer: ComparisonRenderer },
    company_directory: {
        editor: CompanyDirectoryEditor,
        renderer: CompanyDirectoryRenderer,
    },
};

export function getWidgetEntry(type: string) {
    return widgetRegistry[type] ?? null;
}
