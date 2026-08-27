<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    Bot,
    Building2,
    ChevronRight,
    Compass,
    Database,
    Download,
    Flag,
    HeartPulse,
    Mail as MailIcon,
    Map as MapIcon,
    Boxes,
    Briefcase,
    Files,
    FolderTree,
    ImagePlay,
    KeyRound,
    Languages,
    LayoutGrid,
    List,
    ListTree,
    Menu as MenuIcon,
    Navigation,
    Newspaper,
    Plus,
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
import { useT } from '@/composables/useT';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const t = useT();
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

const platformItems = computed<NavItem[]>(() => [
    { title: t('sidebar.dashboard'), href: dashboard(), icon: LayoutGrid },
]);

const mediaItem = computed<NavItem>(() => ({
    title: t('sidebar.media'),
    href: '/admin/media',
    icon: ImagePlay,
}));

const languagesItem = computed<NavItem>(() => ({
    title: t('sidebar.languages'),
    href: '/admin/languages',
    icon: Languages,
}));

const settingsItems = computed<NavItem[]>(() => [
    { title: t('sidebar.site_social_settings'), href: '/admin/site-settings', icon: Settings2Icon },
    { title: t('sidebar.site_identity'), href: '/admin/settings/identity', icon: Settings2Icon },
    { title: t('sidebar.branding'), href: '/admin/settings/branding', icon: Settings2Icon },
    { title: t('sidebar.seo_defaults'), href: '/admin/settings/seo', icon: Settings2Icon },
    { title: t('sidebar.legal_pages'), href: '/admin/settings/legal', icon: Settings2Icon },
    { title: t('sidebar.analytics_tracking'), href: '/admin/settings/analytics', icon: Settings2Icon },
    { title: t('sidebar.maintenance_mode'), href: '/admin/settings/maintenance', icon: Settings2Icon },
    { title: t('sidebar.layout_toggles'), href: '/admin/settings/layout', icon: Settings2Icon },
    { title: t('sidebar.security'), href: '/admin/settings/security', icon: Settings2Icon },
]);

const mailItems = computed<NavItem[]>(() => [
    { title: t('sidebar.email_configuration'), href: '/admin/settings/email', icon: Settings2Icon },
    { title: t('sidebar.email_log'), href: '/admin/system/email-log', icon: Activity },
]);

const systemItems = computed<NavItem[]>(() => [
    { title: t('sidebar.health'), href: '/admin/system/health', icon: HeartPulse },
    { title: t('sidebar.cache_management'), href: '/admin/system/cache', icon: Database },
    { title: t('sidebar.sitemap'), href: '/admin/system/sitemap', icon: ListTree },
    { title: t('sidebar.robots_txt'), href: '/admin/system/robots', icon: Bot },
    { title: t('sidebar.activity_log'), href: '/admin/system/activity', icon: Activity },
    { title: t('sidebar.export_backup'), href: '/admin/system/export', icon: Download },
]);

const blogItems = computed<NavItem[]>(() => [
    { title: t('sidebar.blog_posts'), href: '/admin/blog/posts', icon: Newspaper },
    { title: t('sidebar.blog_categories'), href: '/admin/blog/categories', icon: FolderTree },
    { title: t('sidebar.blog_tags'), href: '/admin/blog/tags', icon: Tag },
]);

const pageItems = computed<NavItem[]>(() => [
    { title: t('sidebar.pages'), href: '/admin/pages', icon: Files },
    { title: t('sidebar.page_categories'), href: '/admin/pages/categories', icon: FolderTree },
]);

const directoryItems = computed<NavItem[]>(() => [
    { title: t('sidebar.countries'), href: '/admin/directory/countries', icon: Flag },
    { title: t('sidebar.states'), href: '/admin/directory/states', icon: MapIcon },
    { title: t('sidebar.cities'), href: '/admin/directory/cities', icon: Building2 },
    { title: t('sidebar.districts'), href: '/admin/directory/districts', icon: Compass },
]);

const servicesItems = computed<NavItem[]>(() => [
    { title: t('sidebar.service_parent_categories'), href: '/admin/services/parent-categories', icon: FolderTree },
    { title: t('sidebar.service_categories'), href: '/admin/services/categories', icon: FolderTree },
]);

type SidebarCounts = { pending_applications?: number };
const sidebarCounts = computed<SidebarCounts>(
    () => (usePage().props.sidebarCounts as SidebarCounts | undefined) ?? {},
);

const companiesItems = computed<NavItem[]>(() => [
    { title: t('sidebar.all_companies'), href: '/admin/companies', icon: List },
    { title: t('sidebar.add_company'), href: '/admin/companies/create', icon: Plus },
    {
        title: t('sidebar.partner_applications'),
        href: '/admin/company-applications',
        icon: UserRoundCheck,
        badge: sidebarCounts.value.pending_applications ?? 0,
    },
    { title: t('sidebar.plans'), href: '/admin/plans', icon: Boxes },
]);

const navigationItems = computed<NavItem[]>(() => [
    { title: t('sidebar.header_menu'), href: '/admin/menus/header', icon: MenuIcon },
    { title: t('sidebar.footer_menu'), href: '/admin/menus/footer', icon: MenuIcon },
]);

const userItems = computed<NavItem[]>(() => [
    { title: t('sidebar.all_users'), href: '/admin/users', icon: Users },
    { title: t('sidebar.add_user'), href: '/admin/users/create', icon: UserPlus },
    { title: t('sidebar.roles'), href: '/admin/roles', icon: ShieldCheck },
    { title: t('sidebar.permissions'), href: '/admin/permissions', icon: KeyRound },
]);

const { isCurrentUrl } = useCurrentUrl();

const isBlogSectionActive = computed(() =>
    blogItems.value.some((item) => isCurrentUrl(item.href)),
);

const isPageSectionActive = computed(() =>
    pageItems.value.some((item) => isCurrentUrl(item.href)),
);

const isDirectorySectionActive = computed(() =>
    directoryItems.value.some((item) => isCurrentUrl(item.href)),
);

const isServicesSectionActive = computed(() =>
    servicesItems.value.some((item) => isCurrentUrl(item.href)),
);

const isCompaniesSectionActive = computed(() =>
    companiesItems.value.some((item) => isCurrentUrl(item.href)) ||
    (typeof window !== 'undefined' && window.location.pathname.startsWith('/admin/companies')),
);

const isNavigationSectionActive = computed(() =>
    navigationItems.value.some((item) => isCurrentUrl(item.href)),
);

const isSettingsSectionActive = computed(() =>
    settingsItems.value.some((item) => isCurrentUrl(item.href)),
);

const isSystemSectionActive = computed(() =>
    systemItems.value.some((item) => isCurrentUrl(item.href)),
);

const isMailSectionActive = computed(() =>
    mailItems.value.some((item) => isCurrentUrl(item.href)),
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
                                        <span>{{ t('sidebar.blog_management') }}</span>
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
                                        <span>{{ t('sidebar.page_management') }}</span>
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
                        :default-open="isDirectorySectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    :tooltip="t('sidebar.directory_management')"
                                    :is-active="isDirectorySectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="directoryItems[0].href">
                                        <MapIcon />
                                        <span>{{ t('sidebar.directory_management') }}</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in directoryItems"
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
                        :default-open="isServicesSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    tooltip="Services Management"
                                    :is-active="isServicesSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="servicesItems[0].href">
                                        <Boxes />
                                        <span>{{ t('sidebar.services_management') }}</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in servicesItems"
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
                        :default-open="isCompaniesSectionActive"
                        class="group/collapsible"
                        as-child
                    >
                        <SidebarMenuItem class="relative">
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton
                                    as-child
                                    :tooltip="t('sidebar.companies')"
                                    :is-active="isCompaniesSectionActive"
                                    :class="BRAND_BUTTON_CLASS"
                                >
                                    <Link :href="companiesItems[0].href">
                                        <Briefcase />
                                        <span>{{ t('sidebar.companies') }}</span>
                                        <ChevronRight
                                            class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                        />
                                    </Link>
                                </SidebarMenuButton>
                            </CollapsibleTrigger>
                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem
                                        v-for="item in companiesItems"
                                        :key="item.title"
                                        class="relative"
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
                                        <span
                                            v-if="item.badge && item.badge > 0"
                                            class="pointer-events-none absolute right-0 top-1/2 z-10 inline-flex h-5 min-w-5 -translate-y-1/2 items-center justify-center rounded-full bg-[var(--orange)] px-1.5 text-[10px] font-semibold text-white shadow-sm group-data-[collapsible=icon]:hidden"
                                        >
                                            {{ item.badge > 99 ? '99+' : item.badge }}
                                        </span>
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
                                        <span>{{ t('sidebar.navigation_management') }}</span>
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
                                        <span>{{ t('sidebar.settings') }}</span>
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
                                        <span>{{ t('sidebar.mail') }}</span>
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
                                        <span>{{ t('sidebar.system') }}</span>
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
                                        <span>{{ t('sidebar.user_management') }}</span>
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
