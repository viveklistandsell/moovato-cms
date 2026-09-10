import type { Component } from 'vue';

/** Metadata about a widget type, sent from the backend WidgetRegistry. */
export type WidgetMeta = {
    type: string;
    label: string;
    icon: string;
    category: string;
    default_settings: Record<string, unknown>;
    default_data: Record<string, unknown>;
    default_data_by_locale?: Record<string, Record<string, unknown>>;
};

/** Translatable payload keyed by language code. */
export type WidgetTranslations = Record<string, Record<string, unknown>>;

/** Which breakpoints a widget renders on. All three default to true. */
export type WidgetVisibility = {
    desktop: boolean;
    tablet: boolean;
    mobile: boolean;
};

/** A widget instance on a page — what the canvas renders and what we save. */
export type WidgetInstance = {
    id?: number | null;
    type: string;
    position: number;
    is_active: boolean;
    settings: Record<string, unknown>;
    translations: WidgetTranslations;
    visibility: WidgetVisibility;
    css_class: string;
};

/** Vue component pair (and optional label override) for a single widget type. */
export type WidgetRegistryEntry = {
    editor: Component;
    renderer: Component;
};

/** Frontend registry: type → { editor, renderer }. */
export type WidgetRegistry = Record<string, WidgetRegistryEntry>;

/** Common props every Editor component receives. */
export type WidgetEditorProps = {
    settings: Record<string, unknown>;
    data: Record<string, unknown>;
    lang: string;
    errors?: Record<string, string | undefined>;
};

/** Common props every Renderer component receives. */
export type WidgetRendererProps = {
    settings: Record<string, unknown>;
    data: Record<string, unknown>;
};
