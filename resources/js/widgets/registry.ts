import { defineAsyncComponent } from 'vue';

// Renderers stay statically imported: they paint the public page and must be
// present on first render for SEO and to avoid layout shift.
import AboutRenderer from './About/AboutRenderer.vue';
import AboutExperienceRenderer from './AboutExperience/AboutExperienceRenderer.vue';
import AboutStatsRenderer from './AboutStats/AboutStatsRenderer.vue';
import AssistantToolsRenderer from './AssistantTools/AssistantToolsRenderer.vue';
import BlogRenderer from './Blog/BlogRenderer.vue';
import CitySearchRenderer from './CitySearch/CitySearchRenderer.vue';
import CompanyDirectoryRenderer from './CompanyDirectory/CompanyDirectoryRenderer.vue';
import ComparisonRenderer from './Comparison/ComparisonRenderer.vue';
import ContactRenderer from './Contact/ContactRenderer.vue';
import ContentCollageRenderer from './ContentCollage/ContentCollageRenderer.vue';
import CostCalculatorRenderer from './CostCalculator/CostCalculatorRenderer.vue';
import CtaBannerRenderer from './CtaBanner/CtaBannerRenderer.vue';
import CtaWorkRenderer from './CtaWork/CtaWorkRenderer.vue';
import DarkFeatureRenderer from './DarkFeature/DarkFeatureRenderer.vue';
import DashboardPromoRenderer from './DashboardPromo/DashboardPromoRenderer.vue';
import ExpertsChoiceRenderer from './ExpertsChoice/ExpertsChoiceRenderer.vue';
import FaqRenderer from './Faq/FaqRenderer.vue';
import FaqMediaRenderer from './FaqMedia/FaqMediaRenderer.vue';
import FaqPageRenderer from './FaqPage/FaqPageRenderer.vue';
import FeaturesRenderer from './Features/FeaturesRenderer.vue';
import GalleryRenderer from './Gallery/GalleryRenderer.vue';
import HeroRenderer from './Hero/HeroRenderer.vue';
import HeronewRenderer from './Heronew/HeronewRenderer.vue';
import HowItWorksRenderer from './HowItWorks/HowItWorksRenderer.vue';
import ImageRenderer from './Image/ImageRenderer.vue';
import MapRenderer from './Map/MapRenderer.vue';
import MarqueeRenderer from './Marquee/MarqueeRenderer.vue';
import MediaChecklistRenderer from './MediaChecklist/MediaChecklistRenderer.vue';
import MissionVisionRenderer from './MissionVision/MissionVisionRenderer.vue';
import OrbitBannerRenderer from './OrbitBanner/OrbitBannerRenderer.vue';
import PartnersRenderer from './Partners/PartnersRenderer.vue';
import PricingRenderer from './Pricing/PricingRenderer.vue';
import PromoCtaRenderer from './PromoCta/PromoCtaRenderer.vue';
import QuoteFormRenderer from './QuoteForm/QuoteFormRenderer.vue';
import ReviewsRenderer from './Reviews/ReviewsRenderer.vue';
import ServiceCardsRenderer from './ServiceCards/ServiceCardsRenderer.vue';
import ServicesCategoryGridRenderer from './ServicesCategoryGrid/ServicesCategoryGridRenderer.vue';
import SplitMediaRenderer from './SplitMedia/SplitMediaRenderer.vue';
import StatFeaturesRenderer from './StatFeatures/StatFeaturesRenderer.vue';
import TeamCtaRenderer from './TeamCta/TeamCtaRenderer.vue';
import TestimonialsShowcaseRenderer from './TestimonialsShowcase/TestimonialsShowcaseRenderer.vue';
import TextBlockRenderer from './TextBlock/TextBlockRenderer.vue';
import TextColumnsRenderer from './TextColumns/TextColumnsRenderer.vue';
import TrustBarRenderer from './TrustBar/TrustBarRenderer.vue';

import type { WidgetRegistry } from './types';
import WhyChooseUsRenderer from './WhyChooseUs/WhyChooseUsRenderer.vue';
import WorkProcessRenderer from './WorkProcess/WorkProcessRenderer.vue';

// Editors are admin-only and open behind a drawer, so they load on demand.
// Keeping them static made every public visitor download the whole editing
// stack (RichTextEditor, MediaPicker) with the page.
const AboutEditor = defineAsyncComponent(() => import('./About/AboutEditor.vue'));
const AboutExperienceEditor = defineAsyncComponent(() => import('./AboutExperience/AboutExperienceEditor.vue'));
const AboutStatsEditor = defineAsyncComponent(() => import('./AboutStats/AboutStatsEditor.vue'));
const AssistantToolsEditor = defineAsyncComponent(() => import('./AssistantTools/AssistantToolsEditor.vue'));
const BlogEditor = defineAsyncComponent(() => import('./Blog/BlogEditor.vue'));
const CitySearchEditor = defineAsyncComponent(() => import('./CitySearch/CitySearchEditor.vue'));
const CompanyDirectoryEditor = defineAsyncComponent(() => import('./CompanyDirectory/CompanyDirectoryEditor.vue'));
const ContactEditor = defineAsyncComponent(() => import('./Contact/ContactEditor.vue'));
const CostCalculatorEditor = defineAsyncComponent(() => import('./CostCalculator/CostCalculatorEditor.vue'));
const ContentCollageEditor = defineAsyncComponent(() => import('./ContentCollage/ContentCollageEditor.vue'));
const ComparisonEditor = defineAsyncComponent(() => import('./Comparison/ComparisonEditor.vue'));
const CtaBannerEditor = defineAsyncComponent(() => import('./CtaBanner/CtaBannerEditor.vue'));
const CtaWorkEditor = defineAsyncComponent(() => import('./CtaWork/CtaWorkEditor.vue'));
const DarkFeatureEditor = defineAsyncComponent(() => import('./DarkFeature/DarkFeatureEditor.vue'));
const DashboardPromoEditor = defineAsyncComponent(() => import('./DashboardPromo/DashboardPromoEditor.vue'));
const ExpertsChoiceEditor = defineAsyncComponent(() => import('./ExpertsChoice/ExpertsChoiceEditor.vue'));
const MediaChecklistEditor = defineAsyncComponent(() => import('./MediaChecklist/MediaChecklistEditor.vue'));
const MissionVisionEditor = defineAsyncComponent(() => import('./MissionVision/MissionVisionEditor.vue'));
const SplitMediaEditor = defineAsyncComponent(() => import('./SplitMedia/SplitMediaEditor.vue'));
const StatFeaturesEditor = defineAsyncComponent(() => import('./StatFeatures/StatFeaturesEditor.vue'));
const TeamCtaEditor = defineAsyncComponent(() => import('./TeamCta/TeamCtaEditor.vue'));
const TextColumnsEditor = defineAsyncComponent(() => import('./TextColumns/TextColumnsEditor.vue'));
const FaqEditor = defineAsyncComponent(() => import('./Faq/FaqEditor.vue'));
const FaqMediaEditor = defineAsyncComponent(() => import('./FaqMedia/FaqMediaEditor.vue'));
const FaqPageEditor = defineAsyncComponent(() => import('./FaqPage/FaqPageEditor.vue'));
const FeaturesEditor = defineAsyncComponent(() => import('./Features/FeaturesEditor.vue'));
const GalleryEditor = defineAsyncComponent(() => import('./Gallery/GalleryEditor.vue'));
const HeroEditor = defineAsyncComponent(() => import('./Hero/HeroEditor.vue'));
const HeronewEditor = defineAsyncComponent(() => import('./Heronew/HeronewEditor.vue'));
const HowItWorksEditor = defineAsyncComponent(() => import('./HowItWorks/HowItWorksEditor.vue'));
const ImageEditor = defineAsyncComponent(() => import('./Image/ImageEditor.vue'));
const MapEditor = defineAsyncComponent(() => import('./Map/MapEditor.vue'));
const MarqueeEditor = defineAsyncComponent(() => import('./Marquee/MarqueeEditor.vue'));
const OrbitBannerEditor = defineAsyncComponent(() => import('./OrbitBanner/OrbitBannerEditor.vue'));
const PartnersEditor = defineAsyncComponent(() => import('./Partners/PartnersEditor.vue'));
const PricingEditor = defineAsyncComponent(() => import('./Pricing/PricingEditor.vue'));
const PromoCtaEditor = defineAsyncComponent(() => import('./PromoCta/PromoCtaEditor.vue'));
const QuoteFormEditor = defineAsyncComponent(() => import('./QuoteForm/QuoteFormEditor.vue'));
const ReviewsEditor = defineAsyncComponent(() => import('./Reviews/ReviewsEditor.vue'));
const ServiceCardsEditor = defineAsyncComponent(() => import('./ServiceCards/ServiceCardsEditor.vue'));
const ServicesCategoryGridEditor = defineAsyncComponent(() => import('./ServicesCategoryGrid/ServicesCategoryGridEditor.vue'));
const TestimonialsShowcaseEditor = defineAsyncComponent(() => import('./TestimonialsShowcase/TestimonialsShowcaseEditor.vue'));
const TextBlockEditor = defineAsyncComponent(() => import('./TextBlock/TextBlockEditor.vue'));
const TrustBarEditor = defineAsyncComponent(() => import('./TrustBar/TrustBarEditor.vue'));
const WhyChooseUsEditor = defineAsyncComponent(() => import('./WhyChooseUs/WhyChooseUsEditor.vue'));
const WorkProcessEditor = defineAsyncComponent(() => import('./WorkProcess/WorkProcessEditor.vue'));

/**
 * Frontend widget registry. To add a new widget, drop a new folder with an
 * Editor + Renderer pair, then append an entry here. The backend keeps its own
 * registry (app/Widgets/Registry/WidgetRegistry.php) — keep the slugs in sync.
 */
export const widgetRegistry: WidgetRegistry = {
    hero: { editor: HeroEditor, renderer: HeroRenderer },
    heronew: { editor: HeronewEditor, renderer: HeronewRenderer },
    dashboard_promo: {
        editor: DashboardPromoEditor,
        renderer: DashboardPromoRenderer,
    },
    assistant_tools: {
        editor: AssistantToolsEditor,
        renderer: AssistantToolsRenderer,
    },
    orbit_banner: { editor: OrbitBannerEditor, renderer: OrbitBannerRenderer },
    cta_banner: { editor: CtaBannerEditor, renderer: CtaBannerRenderer },
    cta_work: { editor: CtaWorkEditor, renderer: CtaWorkRenderer },
    text_block: { editor: TextBlockEditor, renderer: TextBlockRenderer },
    image: { editor: ImageEditor, renderer: ImageRenderer },
    features: { editor: FeaturesEditor, renderer: FeaturesRenderer },
    faq: { editor: FaqEditor, renderer: FaqRenderer },
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
    cost_calculator: {
        editor: CostCalculatorEditor,
        renderer: CostCalculatorRenderer,
    },
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
    work_process: {
        editor: WorkProcessEditor,
        renderer: WorkProcessRenderer,
    },
    mission_vision: {
        editor: MissionVisionEditor,
        renderer: MissionVisionRenderer,
    },
    city_search: {
        editor: CitySearchEditor,
        renderer: CitySearchRenderer,
    },
};

export function getWidgetEntry(type: string) {
    return widgetRegistry[type] ?? null;
}
