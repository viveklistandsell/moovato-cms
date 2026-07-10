<script setup lang="ts" generic="T">
import FlagImage from '@/components/common/FlagImage.vue';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

export type LocaleOption = {
    code: string;
    name: string;
    native_name: string;
    flag?: string | null;
    is_default?: boolean;
};

const props = defineProps<{
    languages: LocaleOption[];
    modelValue: string;
}>();

defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const localeFlagCode: Record<string, string> = { de: 'de', en: 'gb' };

function resolveFlagCode(lang: LocaleOption): string {
    const flag = (lang.flag ?? '').trim();
    if (/^[a-zA-Z]{2}$/.test(flag)) {
        return flag.toLowerCase();
    }
    return localeFlagCode[lang.code] ?? lang.code;
}
</script>

<template>
    <Tabs
        :model-value="modelValue"
        @update:model-value="(v) => $emit('update:modelValue', v as string)"
    >
        <TabsList class="h-auto p-1">
            <TabsTrigger
                v-for="lang in languages"
                :key="lang.code"
                :value="lang.code"
                class="px-4 py-1"
            >
                <FlagImage :code="resolveFlagCode(lang)" size="sm" />
                <span class="font-medium">{{ lang.native_name }}</span>
                <span
                    v-if="lang.is_default"
                    class="text-[10px] tracking-wide text-muted-foreground uppercase"
                    >(default)</span
                >
            </TabsTrigger>
        </TabsList>

        <TabsContent
            v-for="lang in languages"
            :key="lang.code"
            :value="lang.code"
        >
            <slot :code="lang.code" :lang="lang" />
        </TabsContent>
    </Tabs>
</template>
