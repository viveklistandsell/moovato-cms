<script setup lang="ts">
import { Flag, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import FlagImage from '@/components/common/FlagImage.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { useT } from '@/composables/useT';

const emit = defineEmits<{ select: [code: string] }>();
const t = useT();
const open = ref(false);
const query = ref('');

type FlagEntry = {
    emoji: string;
    code: string;
    name_en: string;
    name_de: string;
};

const FLAGS: readonly FlagEntry[] = [
    { emoji: '🇩🇪', code: 'DE', name_en: 'Germany', name_de: 'Deutschland' },
    { emoji: '🇺🇸', code: 'US', name_en: 'United States', name_de: 'Vereinigte Staaten' },
    { emoji: '🇬🇧', code: 'GB', name_en: 'United Kingdom', name_de: 'Vereinigtes Königreich' },
    { emoji: '🇫🇷', code: 'FR', name_en: 'France', name_de: 'Frankreich' },
    { emoji: '🇪🇸', code: 'ES', name_en: 'Spain', name_de: 'Spanien' },
    { emoji: '🇮🇹', code: 'IT', name_en: 'Italy', name_de: 'Italien' },
    { emoji: '🇵🇹', code: 'PT', name_en: 'Portugal', name_de: 'Portugal' },
    { emoji: '🇳🇱', code: 'NL', name_en: 'Netherlands', name_de: 'Niederlande' },
    { emoji: '🇧🇪', code: 'BE', name_en: 'Belgium', name_de: 'Belgien' },
    { emoji: '🇦🇹', code: 'AT', name_en: 'Austria', name_de: 'Österreich' },
    { emoji: '🇨🇭', code: 'CH', name_en: 'Switzerland', name_de: 'Schweiz' },
    { emoji: '🇱🇺', code: 'LU', name_en: 'Luxembourg', name_de: 'Luxemburg' },
    { emoji: '🇮🇪', code: 'IE', name_en: 'Ireland', name_de: 'Irland' },
    { emoji: '🇵🇱', code: 'PL', name_en: 'Poland', name_de: 'Polen' },
    { emoji: '🇨🇿', code: 'CZ', name_en: 'Czech Republic', name_de: 'Tschechien' },
    { emoji: '🇸🇰', code: 'SK', name_en: 'Slovakia', name_de: 'Slowakei' },
    { emoji: '🇭🇺', code: 'HU', name_en: 'Hungary', name_de: 'Ungarn' },
    { emoji: '🇷🇴', code: 'RO', name_en: 'Romania', name_de: 'Rumänien' },
    { emoji: '🇧🇬', code: 'BG', name_en: 'Bulgaria', name_de: 'Bulgarien' },
    { emoji: '🇬🇷', code: 'GR', name_en: 'Greece', name_de: 'Griechenland' },
    { emoji: '🇭🇷', code: 'HR', name_en: 'Croatia', name_de: 'Kroatien' },
    { emoji: '🇸🇮', code: 'SI', name_en: 'Slovenia', name_de: 'Slowenien' },
    { emoji: '🇷🇸', code: 'RS', name_en: 'Serbia', name_de: 'Serbien' },
    { emoji: '🇹🇷', code: 'TR', name_en: 'Turkey', name_de: 'Türkei' },
    { emoji: '🇷🇺', code: 'RU', name_en: 'Russia', name_de: 'Russland' },
    { emoji: '🇺🇦', code: 'UA', name_en: 'Ukraine', name_de: 'Ukraine' },
    { emoji: '🇧🇾', code: 'BY', name_en: 'Belarus', name_de: 'Belarus' },
    { emoji: '🇸🇪', code: 'SE', name_en: 'Sweden', name_de: 'Schweden' },
    { emoji: '🇳🇴', code: 'NO', name_en: 'Norway', name_de: 'Norwegen' },
    { emoji: '🇩🇰', code: 'DK', name_en: 'Denmark', name_de: 'Dänemark' },
    { emoji: '🇫🇮', code: 'FI', name_en: 'Finland', name_de: 'Finnland' },
    { emoji: '🇮🇸', code: 'IS', name_en: 'Iceland', name_de: 'Island' },
    { emoji: '🇨🇦', code: 'CA', name_en: 'Canada', name_de: 'Kanada' },
    { emoji: '🇲🇽', code: 'MX', name_en: 'Mexico', name_de: 'Mexiko' },
    { emoji: '🇧🇷', code: 'BR', name_en: 'Brazil', name_de: 'Brasilien' },
    { emoji: '🇦🇷', code: 'AR', name_en: 'Argentina', name_de: 'Argentinien' },
    { emoji: '🇨🇱', code: 'CL', name_en: 'Chile', name_de: 'Chile' },
    { emoji: '🇨🇴', code: 'CO', name_en: 'Colombia', name_de: 'Kolumbien' },
    { emoji: '🇮🇳', code: 'IN', name_en: 'India', name_de: 'Indien' },
    { emoji: '🇨🇳', code: 'CN', name_en: 'China', name_de: 'China' },
    { emoji: '🇯🇵', code: 'JP', name_en: 'Japan', name_de: 'Japan' },
    { emoji: '🇰🇷', code: 'KR', name_en: 'South Korea', name_de: 'Südkorea' },
    { emoji: '🇻🇳', code: 'VN', name_en: 'Vietnam', name_de: 'Vietnam' },
    { emoji: '🇹🇭', code: 'TH', name_en: 'Thailand', name_de: 'Thailand' },
    { emoji: '🇮🇩', code: 'ID', name_en: 'Indonesia', name_de: 'Indonesien' },
    { emoji: '🇵🇭', code: 'PH', name_en: 'Philippines', name_de: 'Philippinen' },
    { emoji: '🇲🇾', code: 'MY', name_en: 'Malaysia', name_de: 'Malaysia' },
    { emoji: '🇸🇬', code: 'SG', name_en: 'Singapore', name_de: 'Singapur' },
    { emoji: '🇦🇺', code: 'AU', name_en: 'Australia', name_de: 'Australien' },
    { emoji: '🇳🇿', code: 'NZ', name_en: 'New Zealand', name_de: 'Neuseeland' },
    { emoji: '🇮🇱', code: 'IL', name_en: 'Israel', name_de: 'Israel' },
    { emoji: '🇸🇦', code: 'SA', name_en: 'Saudi Arabia', name_de: 'Saudi-Arabien' },
    { emoji: '🇦🇪', code: 'AE', name_en: 'UAE', name_de: 'VAE' },
    { emoji: '🇪🇬', code: 'EG', name_en: 'Egypt', name_de: 'Ägypten' },
    { emoji: '🇿🇦', code: 'ZA', name_en: 'South Africa', name_de: 'Südafrika' },
    { emoji: '🇳🇬', code: 'NG', name_en: 'Nigeria', name_de: 'Nigeria' },
];

const filtered = computed<readonly FlagEntry[]>(() => {
    const q = query.value.trim().toLowerCase();
    if (q === '') {
        return FLAGS;
    }
    return FLAGS.filter(
        (f) =>
            f.code.toLowerCase().includes(q) ||
            f.name_en.toLowerCase().includes(q) ||
            f.name_de.toLowerCase().includes(q),
    );
});

function pick(entry: FlagEntry): void {
    emit('select', entry.code);
    open.value = false;
    query.value = '';
}
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                type="button"
                variant="outline"
                size="sm"
                class="gap-1.5"
                :title="t('languages.flag_picker_open')"
            >
                <Flag class="size-3.5" />
                <span>{{ t('languages.flag_picker_open') }}</span>
            </Button>
        </PopoverTrigger>
        <PopoverContent align="end" class="w-80 p-0">
            <div class="border-b p-2">
                <div class="relative">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-2 size-3.5 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="query"
                        :placeholder="t('languages.flag_picker_search')"
                        class="h-8 pl-8 text-xs"
                    />
                </div>
            </div>
            <div class="max-h-64 overflow-y-auto p-2">
                <div
                    v-if="filtered.length === 0"
                    class="px-2 py-6 text-center text-xs text-muted-foreground"
                >
                    {{ t('languages.flag_picker_empty') }}
                </div>
                <div v-else class="grid grid-cols-5 gap-1">
                    <button
                        v-for="entry in filtered"
                        :key="entry.code"
                        type="button"
                        :title="`${entry.name_en} (${entry.code})`"
                        class="flex aspect-square flex-col items-center justify-center gap-1 rounded-md border border-transparent transition-all hover:scale-105 hover:border-border hover:bg-accent focus:border-border focus:bg-accent focus:outline-none"
                        @click="pick(entry)"
                    >
                        <FlagImage :code="entry.code" size="md" />
                        <span class="text-[9px] font-medium text-muted-foreground">
                            {{ entry.code }}
                        </span>
                    </button>
                </div>
            </div>
        </PopoverContent>
    </Popover>
</template>
