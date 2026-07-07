import AboutEditor from './About/AboutEditor.vue';
import AboutRenderer from './About/AboutRenderer.vue';
import AboutExperienceEditor from './AboutExperience/AboutExperienceEditor.vue';
import AboutExperienceRenderer from './AboutExperience/AboutExperienceRenderer.vue';
import AboutStatsEditor from './AboutStats/AboutStatsEditor.vue';
import AboutStatsRenderer from './AboutStats/AboutStatsRenderer.vue';
import BannerEditor from './Banner/BannerEditor.vue';
import BannerRenderer from './Banner/BannerRenderer.vue';
import BlogEditor from './Blog/BlogEditor.vue';
import BlogRenderer from './Blog/BlogRenderer.vue';
import CompanyDirectoryEditor from './CompanyDirectory/CompanyDirectoryEditor.vue';
import CompanyDirectoryRenderer from './CompanyDirectory/CompanyDirectoryRenderer.vue';
import ContactEditor from './Contact/ContactEditor.vue';
import ContactRenderer from './Contact/ContactRenderer.vue';
import ContentCollageEditor from './ContentCollage/ContentCollageEditor.vue';
import ContentCollageRenderer from './ContentCollage/ContentCollageRenderer.vue';
import ComparisonEditor from './Comparison/ComparisonEditor.vue';
import ComparisonRenderer from './Comparison/ComparisonRenderer.vue';
import CtaEditor from './Cta/CtaEditor.vue';
import CtaRenderer from './Cta/CtaRenderer.vue';
import CtaBannerEditor from './CtaBanner/CtaBannerEditor.vue';
import CtaBannerRenderer from './CtaBanner/CtaBannerRenderer.vue';
import DarkFeatureEditor from './DarkFeature/DarkFeatureEditor.vue';
import DarkFeatureRenderer from './DarkFeature/DarkFeatureRenderer.vue';
import ExpertsChoiceEditor from './ExpertsChoice/ExpertsChoiceEditor.vue';
import ExpertsChoiceRenderer from './ExpertsChoice/ExpertsChoiceRenderer.vue';
import MediaChecklistEditor from './MediaChecklist/MediaChecklistEditor.vue';
import MediaChecklistRenderer from './MediaChecklist/MediaChecklistRenderer.vue';
import SplitMediaEditor from './SplitMedia/SplitMediaEditor.vue';
import SplitMediaRenderer from './SplitMedia/SplitMediaRenderer.vue';
import StatFeaturesEditor from './StatFeatures/StatFeaturesEditor.vue';
import StatFeaturesRenderer from './StatFeatures/StatFeaturesRenderer.vue';
import StatsBandEditor from './StatsBand/StatsBandEditor.vue';
import StatsBandRenderer from './StatsBand/StatsBandRenderer.vue';
import TeamCtaEditor from './TeamCta/TeamCtaEditor.vue';
import TeamCtaRenderer from './TeamCta/TeamCtaRenderer.vue';
import TextColumnsEditor from './TextColumns/TextColumnsEditor.vue';
import TextColumnsRenderer from './TextColumns/TextColumnsRenderer.vue';
import FaqEditor from './Faq/FaqEditor.vue';
import FaqRenderer from './Faq/FaqRenderer.vue';
import FaqMediaEditor from './FaqMedia/FaqMediaEditor.vue';
import FaqMediaRenderer from './FaqMedia/FaqMediaRenderer.vue';
import FaqPageEditor from './FaqPage/FaqPageEditor.vue';
import FaqPageRenderer from './FaqPage/FaqPageRenderer.vue';
import FeaturesEditor from './Features/FeaturesEditor.vue';
import FeaturesRenderer from './Features/FeaturesRenderer.vue';
import GalleryEditor from './Gallery/GalleryEditor.vue';
import GalleryRenderer from './Gallery/GalleryRenderer.vue';
import GetInTouchEditor from './GetInTouch/GetInTouchEditor.vue';
import GetInTouchRenderer from './GetInTouch/GetInTouchRenderer.vue';
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
import OrbitBannerEditor from './OrbitBanner/OrbitBannerEditor.vue';
import OrbitBannerRenderer from './OrbitBanner/OrbitBannerRenderer.vue';
import PageBannerEditor from './PageBanner/PageBannerEditor.vue';
import PageBannerRenderer from './PageBanner/PageBannerRenderer.vue';
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
import ServicesCategoryGridEditor from './ServicesCategoryGrid/ServicesCategoryGridEditor.vue';
import ServicesCategoryGridRenderer from './ServicesCategoryGrid/ServicesCategoryGridRenderer.vue';
import TestimonialEditor from './Testimonial/TestimonialEditor.vue';
import TestimonialRenderer from './Testimonial/TestimonialRenderer.vue';
import TestimonialsShowcaseEditor from './TestimonialsShowcase/TestimonialsShowcaseEditor.vue';
import TestimonialsShowcaseRenderer from './TestimonialsShowcase/TestimonialsShowcaseRenderer.vue';
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
    page_banner: { editor: PageBannerEditor, renderer: PageBannerRenderer },
    orbit_banner: { editor: OrbitBannerEditor, renderer: OrbitBannerRenderer },
    cta_banner: { editor: CtaBannerEditor, renderer: CtaBannerRenderer },
    text_block: { editor: TextBlockEditor, renderer: TextBlockRenderer },
    image: { editor: ImageEditor, renderer: ImageRenderer },
    features: { editor: FeaturesEditor, renderer: FeaturesRenderer },
    cta: { editor: CtaEditor, renderer: CtaRenderer },
    faq: { editor: FaqEditor, renderer: FaqRenderer },
    testimonial: { editor: TestimonialEditor, renderer: TestimonialRenderer },
    testimonials_showcase: {
        editor: TestimonialsShowcaseEditor,
        renderer: TestimonialsShowcaseRenderer,
    },
    gallery: { editor: GalleryEditor, renderer: GalleryRenderer },
    trust_bar: { editor: TrustBarEditor, renderer: TrustBarRenderer },
    marquee: { editor: MarqueeEditor, renderer: MarqueeRenderer },
    quote_form: { editor: QuoteFormEditor, renderer: QuoteFormRenderer },
    service_cards: {
        editor: ServiceCardsEditor,
        renderer: ServiceCardsRenderer,
    },
    services_category_grid: {
        editor: ServicesCategoryGridEditor,
        renderer: ServicesCategoryGridRenderer,
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
    faq_page: { editor: FaqPageEditor, renderer: FaqPageRenderer },
    blog: { editor: BlogEditor, renderer: BlogRenderer },
    map: { editor: MapEditor, renderer: MapRenderer },
    promo_cta: { editor: PromoCtaEditor, renderer: PromoCtaRenderer },
    about: { editor: AboutEditor, renderer: AboutRenderer },
    about_stats: { editor: AboutStatsEditor, renderer: AboutStatsRenderer },
    about_experience: {
        editor: AboutExperienceEditor,
        renderer: AboutExperienceRenderer,
    },
    experts_choice: {
        editor: ExpertsChoiceEditor,
        renderer: ExpertsChoiceRenderer,
    },
    media_checklist: {
        editor: MediaChecklistEditor,
        renderer: MediaChecklistRenderer,
    },
    text_columns: {
        editor: TextColumnsEditor,
        renderer: TextColumnsRenderer,
    },
    dark_feature: { editor: DarkFeatureEditor, renderer: DarkFeatureRenderer },
    team_cta: { editor: TeamCtaEditor, renderer: TeamCtaRenderer },
    stat_features: {
        editor: StatFeaturesEditor,
        renderer: StatFeaturesRenderer,
    },
    stats_band: { editor: StatsBandEditor, renderer: StatsBandRenderer },
    why_choose_media: {
        editor: MediaChecklistEditor,
        renderer: MediaChecklistRenderer,
    },
    supporting_media: {
        editor: MediaChecklistEditor,
        renderer: MediaChecklistRenderer,
    },
    dark_intro: { editor: DarkFeatureEditor, renderer: DarkFeatureRenderer },
    content_style_1: {
        editor: MediaChecklistEditor,
        renderer: MediaChecklistRenderer,
    },
    content_style_2: {
        editor: MediaChecklistEditor,
        renderer: MediaChecklistRenderer,
    },
    split_media: { editor: SplitMediaEditor, renderer: SplitMediaRenderer },
    split_media_left: {
        editor: SplitMediaEditor,
        renderer: SplitMediaRenderer,
    },
    content_collage: {
        editor: ContentCollageEditor,
        renderer: ContentCollageRenderer,
    },
    comparison: { editor: ComparisonEditor, renderer: ComparisonRenderer },
    company_directory: {
        editor: CompanyDirectoryEditor,
        renderer: CompanyDirectoryRenderer,
    },
    contact: { editor: ContactEditor, renderer: ContactRenderer },
    get_in_touch: { editor: GetInTouchEditor, renderer: GetInTouchRenderer },
};

export function getWidgetEntry(type: string) {
    return widgetRegistry[type] ?? null;
}
