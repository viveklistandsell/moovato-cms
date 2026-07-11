<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Check, ChevronDown, Globe } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import FlagImage from '@/components/common/FlagImage.vue';

type Language = {
    code: string;
    native_name: string;
    flag: string | null;
};

const page = usePage();

const languages = computed<Language[]>(
    () => ((page.props as { adminLanguages?: Language[] }).adminLanguages ?? []),
);

// Current locale resolves from:
// 1. user.admin_locale (set via the switcher, persisted on the user)
// 2. The shared `locale` prop (URL-driven for frontend, system default
//    for admin if user hasn't picked yet)
const currentCode = computed<string>(() => {
    const fromUser = (page.props as { adminLocale?: string | null }).adminLocale;
    if (typeof fromUser === 'string' && fromUser !== '') {
        return fromUser;
    }
    const fromShared = (page.props as { locale?: string }).locale;
    return typeof fromShared === 'string' ? fromShared : 'de';
});

const current = computed<Language | null>(
    () =>
        languages.value.find((l) => l.code === currentCode.value) ??
        languages.value[0] ??
        null,
);

const localeFlagCode: Record<string, string> = { de: 'de', en: 'gb' };

function resolveFlagCode(lang: Language): string {
    const flag = (lang.flag ?? '').trim();
    if (/^[a-zA-Z]{2}$/.test(flag)) {
        return flag.toLowerCase();
    }
    return localeFlagCode[lang.code] ?? lang.code;
}

const open = ref<boolean>(false);
const saving = ref<boolean>(false);
const trigger = ref<HTMLElement | null>(null);

function toggle(): void {
    open.value = !open.value;
}

function pick(code: string): void {
    open.value = false;
    if (code === currentCode.value || saving.value) {
        return;
    }
    saving.value = true;
    router.patch(
        '/admin/locale',
        { locale: code },
        {
            preserveScroll: true,
            preserveState: false,
            onFinish: () => {
                saving.value = false;
            },
        },
    );
}

// Close on outside click.
function onDocumentClick(event: MouseEvent): void {
    const target = event.target as Node | null;
    if (open.value && target && trigger.value && !trigger.value.contains(target)) {
        open.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', onDocumentClick);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick);
});
</script>

<template>
    <div
        v-if="languages.length > 1"
        ref="trigger"
        class="relative"
    >
        <button
            type="button"
            class="inline-flex h-8 items-center gap-1.5 rounded-md border bg-background px-2 text-xs font-semibold text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
            :disabled="saving"
            @click="toggle"
        >
            <Globe v-if="!current" class="size-3.5" />
            <FlagImage v-else :code="resolveFlagCode(current)" size="sm" />
            <span class="hidden md:inline">
                {{ current ? current.code.toUpperCase() : '—' }}
            </span>
            <ChevronDown
                class="size-3 transition-transform"
                :class="open ? 'rotate-180' : ''"
            />
        </button>

        <div
            v-if="open"
            class="animate-in fade-in slide-in-from-top-1 absolute right-0 z-50 mt-1 w-44 overflow-hidden rounded-md border bg-background shadow-lg duration-150"
        >
            <button
                v-for="lang in languages"
                :key="lang.code"
                type="button"
                class="flex w-full items-center justify-between px-3 py-2 text-left text-xs font-medium transition-colors hover:bg-accent"
                :class="lang.code === currentCode ? 'text-[var(--orange)]' : 'text-foreground'"
                @click="pick(lang.code)"
            >
                <span class="flex items-center gap-2">
                    <FlagImage :code="resolveFlagCode(lang)" size="sm" />
                    <span class="font-semibold">
                        {{ lang.code.toUpperCase() }}
                    </span>
                    <span class="text-muted-foreground">
                        {{ lang.native_name }}
                    </span>
                </span>
                <Check
                    v-if="lang.code === currentCode"
                    class="size-3.5 text-[var(--orange)]"
                />
            </button>
        </div>
    </div>
</template>
