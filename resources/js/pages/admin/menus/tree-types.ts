import type { InjectionKey, Ref } from 'vue';
import type { LocaleOption } from '@/components/common/LocaleTabs.vue';

export type LinkType = 'home' | 'page' | 'category' | 'url';

export type TranslationRow = { label: string; link_url: string | null };

export type MenuItemRow = {
    id: number;
    menu_id: number;
    parent_id: number | null;
    sort_order: number;
    link_type: LinkType;
    link_id: number | null;
    open_in_new_tab: boolean;
    css_class: string | null;
    is_active: boolean;
    linked_title: string | null;
    translations: Record<string, TranslationRow>;
};

export type TreeNode = MenuItemRow & { children: TreeNode[] };

export type ItemDraft = {
    translations: Record<string, TranslationRow>;
    open_in_new_tab: boolean;
    css_class: string;
    is_active: boolean;
};

// Everything a MenuTreeNode at any depth needs to render itself and react to
// user input. Stored as Refs so child components stay reactive without
// per-level prop drilling.
export type MenuTreeContext = {
    languages: Ref<LocaleOption[]>;
    defaultLang: Ref<string>;
    itemDrafts: Ref<Record<number, ItemDraft>>;
    expandedItemIds: Ref<Set<number>>;
    itemActiveLang: Ref<Record<number, string>>;
    activeLangFor: (id: number) => string;
    setActiveLangFor: (id: number, code: string) => void;
    toggleExpanded: (id: number) => void;
    syncOrder: () => void;
    saveItem: (node: TreeNode) => void;
    destroyItem: (node: TreeNode) => void;
};

export const MENU_TREE_CONTEXT: InjectionKey<MenuTreeContext> = Symbol('menu-tree-context');
