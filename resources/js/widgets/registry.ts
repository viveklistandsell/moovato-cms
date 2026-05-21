import BannerEditor from './Banner/BannerEditor.vue';
import BannerRenderer from './Banner/BannerRenderer.vue';
import CtaEditor from './Cta/CtaEditor.vue';
import CtaRenderer from './Cta/CtaRenderer.vue';
import FaqEditor from './Faq/FaqEditor.vue';
import FaqRenderer from './Faq/FaqRenderer.vue';
import FeaturesEditor from './Features/FeaturesEditor.vue';
import FeaturesRenderer from './Features/FeaturesRenderer.vue';
import GalleryEditor from './Gallery/GalleryEditor.vue';
import GalleryRenderer from './Gallery/GalleryRenderer.vue';
import HeroEditor from './Hero/HeroEditor.vue';
import HeroRenderer from './Hero/HeroRenderer.vue';
import ImageEditor from './Image/ImageEditor.vue';
import ImageRenderer from './Image/ImageRenderer.vue';
import TestimonialEditor from './Testimonial/TestimonialEditor.vue';
import TestimonialRenderer from './Testimonial/TestimonialRenderer.vue';
import TextBlockEditor from './TextBlock/TextBlockEditor.vue';
import TextBlockRenderer from './TextBlock/TextBlockRenderer.vue';
import type { WidgetRegistry } from './types';

/**
 * Frontend widget registry. To add a new widget, drop a new folder with an
 * Editor + Renderer pair, then append an entry here. The backend keeps its own
 * registry (app/Widgets/Registry/WidgetRegistry.php) — keep the slugs in sync.
 */
export const widgetRegistry: WidgetRegistry = {
    hero: { editor: HeroEditor, renderer: HeroRenderer },
    banner: { editor: BannerEditor, renderer: BannerRenderer },
    text_block: { editor: TextBlockEditor, renderer: TextBlockRenderer },
    image: { editor: ImageEditor, renderer: ImageRenderer },
    features: { editor: FeaturesEditor, renderer: FeaturesRenderer },
    cta: { editor: CtaEditor, renderer: CtaRenderer },
    faq: { editor: FaqEditor, renderer: FaqRenderer },
    testimonial: { editor: TestimonialEditor, renderer: TestimonialRenderer },
    gallery: { editor: GalleryEditor, renderer: GalleryRenderer },
};

export function getWidgetEntry(type: string) {
    return widgetRegistry[type] ?? null;
}
