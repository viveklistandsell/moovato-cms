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
    >
        <TabsList>
            <TabsTrigger
                v-for="lang in languages"
                :key="lang.code"
                :value="lang.code"
            >
                <FlagImage v-if="lang.flag" :code="lang.flag" size="sm" />
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
