<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, resolvedAppearance, updateAppearance } = useAppearance();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon-sm"
                aria-label="Toggle theme"
                title="Toggle theme"
            >
                <Sun v-if="resolvedAppearance === 'light'" class="size-4" />
                <Moon v-else class="size-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="min-w-[140px]">
            <DropdownMenuItem
                :class="
                    appearance === 'light' ? 'bg-accent text-foreground' : ''
                "
                @click="updateAppearance('light')"
            >
                <Sun class="size-4" />
                <span>Light</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                :class="
                    appearance === 'dark' ? 'bg-accent text-foreground' : ''
                "
                @click="updateAppearance('dark')"
            >
                <Moon class="size-4" />
                <span>Dark</span>
            </DropdownMenuItem>
            <DropdownMenuItem
                :class="
                    appearance === 'system' ? 'bg-accent text-foreground' : ''
                "
                @click="updateAppearance('system')"
            >
                <Monitor class="size-4" />
                <span>System</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
