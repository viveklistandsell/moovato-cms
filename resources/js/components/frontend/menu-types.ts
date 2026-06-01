export type MenuNode = {
    id: number;
    type: 'home' | 'page' | 'category' | 'url';
    label: string;
    url: string | null;
    open_in_new_tab: boolean;
    css_class: string | null;
    children: MenuNode[];
};
