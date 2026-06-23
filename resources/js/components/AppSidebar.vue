<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    Bot,
    ChevronRight,
    Database,
    Download,
    HeartPulse,
    Mail as MailIcon,
    Files,
    FolderTree,
    ImagePlay,
    KeyRound,
    Languages,
    LayoutGrid,
    ListTree,
    Menu as MenuIcon,
    Navigation,
    Newspaper,
    Server,
    Settings2Icon,
    ShieldCheck,
    Tag,
    UserPlus,
    UserRoundCheck,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';
const BRAND_BUTTON_CLASS = [
    'transition-colors hover:bg-[var(--orange)]/8 hover:text-[var(--orange)]',
    'data-[active=true]:bg-[var(--orange)]/12 data-[active=true]:text-[var(--orange)] data-[active=true]:font-semibold',
    'data-[active=true]:hover:bg-[var(--orange)]/18 data-[active=true]:hover:text-[var(--orange)]',
    'data-[active=true]:shadow-[inset_3px_0_0_var(--orange)]',
].join(' ');

const BRAND_SUB_CLASS = [
    'transition-colors hover:bg-[var(--orange)]/8 hover:text-[var(--orange)]',
    'data-[active=true]:bg-[var(--orange)]/12 data-[active=true]:text-[var(--orange)] data-[active=true]:font-semibold',
].join(' ');

const platformItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const mediaItem: NavItem = {
    title: 'Media',
    href: '/admin/media',
    icon: ImagePlay,
};

const languagesItem: NavItem = {
    title: 'Languages',
    href: '/admin/languages',
    icon: Languages,
};

const settingsItems: NavItem[] = [
    { title: 'Site/Social Settings', href: '/admin/site-settings', icon: Settings2Icon },
    { title: 'Site Identity', href: '/admin/settings/identity', icon: Settings2Icon },
    { title: 'Branding', href: '/admin/settings/branding', icon: Settings2Icon },
    { title: 'SEO Defaults', href: '/admin/settings/seo', icon: Settings2Icon },
    { title: 'Legal Pages', href: '/admin/settings/legal', icon: Settings2Icon },
    { title: 'Analytics & Tracking', href: '/admin/settings/analytics', icon: Settings2Icon },
    { title: 'Maintenance Mode', href: '/admin/settings/maintenance', icon: Settings2Icon },
    { title: 'Layout Toggles', href: '/admin/settings/layout', icon: Settings2Icon },
    { title: 'Security', href: '/admin/settings/security', icon: Settings2Icon },
];

const mailItems: NavItem[] = [
    { title: 'Email Configuration', href: '/admin/settings/email', icon: Settings2Icon },
    { title: 'Email Log', href: '/admin/system/email-log', icon: Activity },
];

const systemItems: NavItem[] = [
    { title: 'Health', href: '/admin/system/health', icon: HeartPulse },
    { title: 'Cache Management', href: '/admin/system/cache', icon: Database },
    { title: 'Sitemap', href: '/admin/system/sitemap', icon: ListTree },
    { title: 'Robots.txt', href: '/admin/system/robots', icon: Bot },
    { title: 'Activity Log', href: '/admin/system/activity', icon: Activity },
    { title: 'Export & Backup', href: '/admin/system/export', icon: Download },
];

const blogItems: NavItem[] = [
    {
        title: 'Blog Posts',
        href: '/admin/blog/posts',
        icon: Newspaper,
    },
    {
        title: 'Blog Categories',
        href: '/admin/blog/categories',
        icon: FolderTree,
    },
    {
        title: 'Blog Tags',
        href: '/admin/blog/tags',
        icon: Tag,
    },
];

const pageItems: NavItem[] = [
    {
        title: 'Pages',
        href: '/admin/pages',
        icon: Files,
    },
    {
        title: 'Page Categories',
        href: '/admin/pages/categories',
        icon: FolderTree,
    },
];

const navigationItems: NavItem[] = [
    {
        title: 'Header Menu',
        href: '/admin/menus/header',
        icon: MenuIcon,
    },
    {
        title: 'Footer Menu',
        href: '/admin/menus/footer',
        icon: MenuIcon,
    },
];

// User management sub-menu shown under the collapsible "User Management" item.
// "Add User" jumps to the index page with `?new=1` so Index.vue auto-opens
// the create drawer — no separate route required.
const userItems: NavItem[] = [
    {
        title: 'All Users',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Add User',
        href: '/admin/users/create',
        icon: UserPlus,
    },
    {
        title: 'Roles',
        href: '/admin/roles',
        icon: ShieldCheck,
    },
    {
        title: 'Permissions',
        href: '/admin/permissions',
        icon: KeyRound,
    },
];

const { isCurrentUrl } = useCurrentUrl();

const isBlogSectionActive = computed(() =>
    blogItems.some((item) => isCurrentUrl(item.href)),
);

const isPageSectionActive = computed(() =>
    pageItems.some((item) => isCurrentUrl(item.href)),
);

const isNavigationSectionActive = computed(() =>
    navigationItems.some((item) => isCurrentUrl(item.href)),
);

const isSettingsSectionActive = computed(() =>
    settingsItems.some((item) => isCurrentUrl(item.href)),
);

const isSystemSectionActive = computed(() =>
    systemItems.some((item) => isCurrentUrl(item.href)),
);

const isMailSectionActive = computed(() =>
    mailItems.some((item) => isCurrentUrl(item.href)),
);

const isUserSectionActive = computed(
    () =>
        isCurrentUrl('/admin/users') ||
        isCurrentUrl('/admin/roles') ||
        isCurrentUrl('/admin/permissions'),
);
</script>

<template>
    <Sidebar collapsible="icon" variant="sidebar">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="platformItems" label="Platform" />
            <SidebarGroup class="px-2 py-0">
                <SidebarMenu>
                    <Collapsible
                        :default-open="isBlogSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Blog Management"
                                    :is-active="isBlogSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="blogItems[0].href">
                                        <Newspaper />
                                        <span>Blog Management</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in blogItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <Collapsible
                        :default-open="isPageSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Page Management"
                                    :is-active="isPageSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="pageItems[0].href">
                                        <Files />
                                        <span>Page Management</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in pageItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <Collapsible
                        :default-open="isNavigationSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Navigation Management"
                                    :is-active="isNavigationSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="navigationItems[0].href">
                                        <Navigation />
                                        <span>Navigation Management</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in navigationItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <SidebarMenuItem>
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(mediaItem.href)"
                            :tooltip="mediaItem.title"
                            :class="BRAND_BUTTON_CLASS"
                        >
                            <Link :href="mediaItem.href">
                                <component :is="mediaItem.icon" />
                                <span>{{ mediaItem.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>

                    <SidebarMenuItem>
                        <SidebarMenuButton
                            as-child
                            :is-active="isCurrentUrl(languagesItem.href)"
                            :tooltip="languagesItem.title"
                            :class="BRAND_BUTTON_CLASS"
                        >
                            <Link :href="languagesItem.href">
                                <component :is="languagesItem.icon" />
                                <span>{{ languagesItem.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>

                    <Collapsible
                        :default-open="isSettingsSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Settings"
                                    :is-active="isSettingsSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="settingsItems[0].href">
                                        <Settings2Icon />
                                        <span>Settings</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in settingsItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <Collapsible
                        :default-open="isMailSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Mail"
                                    :is-active="isMailSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="mailItems[0].href">
                                        <MailIcon />
                                        <span>Mail</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in mailItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <Collapsible
                        :default-open="isSystemSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="System"
                                    :is-active="isSystemSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="systemItems[0].href">
                                        <Server />
                                        <span>System</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in systemItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>

                    <Collapsible
                        :default-open="isUserSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="User Management"
                                    :is-active="isUserSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="userItems[0].href">
                                        <UserRoundCheck />
                                        <span>User Management</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in userItems"
                                        :key="item.title"
                                    >
                                        <SidebarMenuSubButton
                                            as-child
                                            :is-active="isCurrentUrl(item.href)"
                                            :class="BRAND_SUB_CLASS"
                                        >
                                            <Link :href="item.href">
                                                <component :is="item.icon" />
                                                <span>{{ item.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
