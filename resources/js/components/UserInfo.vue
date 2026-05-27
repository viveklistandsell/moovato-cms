<script setup lang="ts">
import { computed } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';

type Props = {
    user: User;
    showEmail?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

const avatarSrc = computed<string | null>(
    () => props.user.avatar_url ?? props.user.avatar ?? null,
);
</script>

<template>
    <div class="relative">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarImage v-if="avatarSrc" :src="avatarSrc" :alt="user.name" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">
                {{ getInitials(user.name) }}
            </AvatarFallback>
        </Avatar>
        <!-- Online dot pinned to the bottom-right of the avatar. -->
        <span
            class="absolute -bottom-0.5 -right-0.5 size-2.5 rounded-full bg-emerald-500 ring-2 ring-sidebar"
            title="Online"
        />
    </div>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span
            v-if="user.role_display_name"
            class="truncate text-xs text-muted-foreground"
        >
            {{ user.role_display_name }}
        </span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">
            {{ user.email }}
        </span>
    </div>
</template>
