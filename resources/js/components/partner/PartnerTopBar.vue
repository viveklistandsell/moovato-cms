<script setup lang="ts">
/**
 * Shared top bar for the partner portal (dashboard + follow-up
 * pages). Left side shows the Moovato logo linking to the home
 * page; right side shows the current partner's initials avatar
 * with a dropdown that offers the "view public listing" link and
 * the logout button.
 */
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronDown, ExternalLink, LogOut } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

type UserProp = {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    company_id: number | null;
};

type SiteSettings = {
    site_name?: string | null;
    logo_light_url?: string | null;
};

const props = defineProps<{
    locale: string;
    user: UserProp | null;
    portalUrl?: string | null;
}>();

const site = computed<SiteSettings>(
    () => (usePage().props as { siteSettings?: SiteSettings }).siteSettings ?? {},
);

const siteName = computed(() => site.value.site_name || 'Moovato');
const logoUrl = computed(() => site.value.logo_light_url || null);

const initials = computed(() => {
    if (props.user === null) return '?';
    const parts = props.user.full_name.trim().split(/\s+/);
    const a = parts[0]?.charAt(0) ?? '';
    const b = parts.length > 1 ? (parts[parts.length - 1]?.charAt(0) ?? '') : '';
    return (a + b).toUpperCase() || '?';
});

const menuOpen = ref(false);
const menuEl = ref<HTMLElement | null>(null);

function toggleMenu(): void {
    menuOpen.value = !menuOpen.value;
}

function closeMenu(): void {
    menuOpen.value = false;
}

function onDocumentClick(event: MouseEvent): void {
    if (!menuEl.value) return;
    if (!menuEl.value.contains(event.target as Node)) {
        menuOpen.value = false;
    }
}

onMounted(() => document.addEventListener('click', onDocumentClick));
onUnmounted(() => document.removeEventListener('click', onDocumentClick));

function logout(): void {
    router.post('/partner/logout');
}

const t = computed(() => (props.locale === 'de'
    ? {
        portal: 'Portal',
        view_listing: 'Öffentliches Profil ansehen',
        logout: 'Abmelden',
        signed_in_as: 'Angemeldet als',
    }
    : {
        portal: 'Portal',
        view_listing: 'View public listing',
        logout: 'Sign out',
        signed_in_as: 'Signed in as',
    }));
</script>

<template>
    <header class="sticky top-0 z-30 border-b border-[var(--midnight)] bg-[var(--midnight)] text-[var(--paper)] shadow-sm">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <!-- Left: logo -->
            <div class="flex items-center gap-3">
                <Link href="/" class="inline-flex items-center gap-2">
                    <img
                        v-if="logoUrl"
                        :src="logoUrl"
                        :alt="siteName"
                        class="h-8 w-auto"
                    />
                    <span v-else class="text-base font-bold tracking-tight text-[var(--white)]">
                        {{ siteName }}
                    </span>
                </Link>
                <span class="hidden text-[10px] font-medium uppercase tracking-[0.16em] text-[var(--orange-soft)] opacity-80 sm:inline">
                    · {{ t.portal }}
                </span>
            </div>

            <!-- Right: user menu -->
            <div ref="menuEl" class="relative">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/5"
                    :aria-expanded="menuOpen"
                    @click="toggleMenu"
                >
                    <span class="flex size-8 items-center justify-center rounded-full bg-[var(--orange)] text-xs font-semibold text-[var(--white)]">
                        {{ initials }}
                    </span>
                    <span class="hidden text-left sm:block">
                        <span class="block text-xs font-medium text-[var(--white)]">
                            {{ user?.full_name ?? '—' }}
                        </span>
                        <span class="block text-[10px] text-[var(--slate-light)]">
                            {{ user?.email ?? '' }}
                        </span>
                    </span>
                    <ChevronDown class="size-4 text-[var(--slate-light)] transition-transform" :class="{ 'rotate-180': menuOpen }" />
                </button>

                <transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="menuOpen"
                        class="absolute right-0 top-full mt-2 w-64 overflow-hidden rounded-xl border border-[var(--linen)] bg-[var(--white)] shadow-lg"
                    >
                        <div class="border-b border-[var(--linen)] bg-[var(--paper)] px-4 py-3">
                            <p class="text-[10px] font-medium uppercase tracking-wider text-[var(--slate-light)]">
                                {{ t.signed_in_as }}
                            </p>
                            <p class="mt-0.5 truncate text-sm font-semibold text-[var(--midnight)]">
                                {{ user?.full_name ?? '—' }}
                            </p>
                            <p class="truncate text-xs text-[var(--slate)]">
                                {{ user?.email ?? '' }}
                            </p>
                        </div>
                        <ul class="py-1">
                            <li v-if="portalUrl">
                                <a
                                    :href="portalUrl"
                                    target="_blank"
                                    rel="noopener"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--slate)] hover:bg-[var(--paper)] hover:text-[var(--orange)]"
                                    @click="closeMenu"
                                >
                                    <ExternalLink class="size-4" />
                                    {{ t.view_listing }}
                                </a>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-[var(--slate)] hover:bg-[var(--orange-soft)] hover:text-[var(--orange)]"
                                    @click="() => { closeMenu(); logout(); }"
                                >
                                    <LogOut class="size-4" />
                                    {{ t.logout }}
                                </button>
                            </li>
                        </ul>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>
