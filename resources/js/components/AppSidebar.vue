<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    ChevronRight,
    Files,
    FolderGit2,
    FolderTree,
    ImagePlay,
    Languages,
    LayoutGrid,
    Newspaper,
    Tag,
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
    SidebarGroupLabel,
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

// const languagesItem: NavItem = {
//     title: 'Languages',
//     href: '/admin/languages',
//     icon: Languages,
// };

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

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];

const { isCurrentUrl } = useCurrentUrl();

const isBlogSectionActive = computed(() =>
    blogItems.some((item) => isCurrentUrl(item.href)),
);

const isPageSectionActive = computed(() =>
    pageItems.some((item) => isCurrentUrl(item.href)),
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

                    <SidebarMenuItem v-if="isAdmin">
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

                    <!-- <SidebarMenuItem>
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
                    </SidebarMenuItem> -->
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
