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
</script>

<template>
    <Tabs
        :model-value="modelValue"
        @update:model-value="(v) => $emit('update:modelValue', v as string)"
        class="mv-locale-tabs"
    >
        <TabsList
            class="!h-auto !w-fit gap-1 rounded-xl border border-border !bg-muted/60 shadow-inner"
        >
            <TabsTrigger
                v-for="lang in languages"
                :key="lang.code"
                :value="lang.code"
                class="!h-9 gap-2 rounded-lg !px-4 text-sm data-[state=active]:!bg-background data-[state=active]:!shadow-md data-[state=active]:font-semibold data-[state=active]:text-primary data-[state=inactive]:text-muted-foreground data-[state=inactive]:hover:text-foreground"
            >
                <FlagImage v-if="lang.flag" :code="lang.flag" size="sm" />
                <span>{{ lang.native_name }}</span>
                <span
                    v-if="lang.is_default"
                    class="rounded-sm bg-primary/10 px-1.5 py-0.5 text-[9px] font-medium tracking-wide text-primary uppercase"
                    >default</span
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
