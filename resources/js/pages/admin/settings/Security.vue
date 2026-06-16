<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';

type Settings = {
    require_2fa_for_admins: boolean;
    session_lifetime_minutes: number | null;
    login_throttle_attempts: number | null;
};

const props = defineProps<{ settings: Settings }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/admin/settings/identity' },
            { title: 'Security', href: '/admin/settings/security' },
        ],
    },
});

const form = useForm({
    require_2fa_for_admins: props.settings.require_2fa_for_admins,
    session_lifetime_minutes: props.settings.session_lifetime_minutes,
    login_throttle_attempts: props.settings.login_throttle_attempts,
});

function submit(): void {
    form.put('/admin/settings/security', { preserveScroll: true });
}
</script>

<template>
    <Head title="Security" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Security"
            description="2FA enforcement, session lifetime and login throttling."
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardContent class="pt-6">
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border p-3 text-sm">
                        <Switch
                            :model-value="form.require_2fa_for_admins"
                            @update:model-value="(v) => (form.require_2fa_for_admins = v as boolean)"
                        />
                        <span class="flex-1">
                            <span class="block font-medium">Require 2FA for admins</span>
                            <span class="block text-xs text-muted-foreground">
                                Admins are pushed to the 2FA setup page until enabled.
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Sessions &amp; throttling</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <Label for="session_lifetime_minutes">
                            Session lifetime (minutes)
                        </Label>
                        <Input
                            id="session_lifetime_minutes"
                            v-model.number="form.session_lifetime_minutes"
                            type="number"
                            min="5"
                            max="10080"
                            step="5"
                            placeholder="120"
                        />
                        <p class="text-xs text-muted-foreground">
                            Accepted range: <strong>5 – 10 080 min</strong>
                            (5 min to 1 week). Leave empty to fall back to
                            <code>SESSION_LIFETIME</code> in .env.
                        </p>
                        <InputError
                            :message="form.errors.session_lifetime_minutes"
                        />
                    </div>
                    <div class="space-y-1">
                        <Label for="login_throttle_attempts">
                            Login throttle (attempts per minute)
                        </Label>
                        <Input
                            id="login_throttle_attempts"
                            v-model.number="form.login_throttle_attempts"
                            type="number"
                            min="1"
                            max="60"
                            placeholder="5"
                        />
                        <p class="text-xs text-muted-foreground">
                            Accepted range: <strong>1 – 60 attempts/min</strong>
                            before the IP + email combo is locked out. Leave
                            empty for Fortify's default of 5.
                        </p>
                        <InputError
                            :message="form.errors.login_throttle_attempts"
                        />
                    </div>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save security
                </Button>
            </div>
        </form>
    </div>
</template>
