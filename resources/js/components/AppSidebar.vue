<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    ChevronRight,
    Files,
    FolderTree,
    ImagePlay,
    KeyRound,
    Languages,
    LayoutGrid,
    Newspaper,
    Settings2Icon,
    ShieldCheck,
    Tag,
    UserPlus,
    UserRoundCheck,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
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

const page = usePage();

const platformItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const isAdmin = computed<boolean>(
    () => page.props.auth?.user?.is_admin === true,
);

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
        href: '/admin/users?new=1',
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

const footerNavItems: NavItem[] = [
    {
        title: 'Settings',
        href: '/admin/settings',
        icon: Settings2Icon,
    },
];

const { isCurrentUrl } = useCurrentUrl();

const isBlogSectionActive = computed(() =>
    blogItems.some((item) => isCurrentUrl(item.href)),
);

const isPageSectionActive = computed(() =>
    pageItems.some((item) => isCurrentUrl(item.href)),
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
                        >
                            <Link :href="languagesItem.href">
                                <component :is="languagesItem.icon" />
                                <span>{{ languagesItem.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>

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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
