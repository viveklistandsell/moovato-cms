<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle2,
    Eye,
    EyeOff,
    Mail as MailIcon,
    Save,
    Send,
} from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.mail'), href: '/admin/settings/email' },
    { title: t('sidebar.email_configuration'), href: '/admin/settings/email' },
]);

type Settings = {
    mail_transport: string;
    mail_host: string | null;
    mail_port: number | null;
    mail_username: string | null;
    mail_password_set: boolean;
    mail_encryption: string | null;
    mail_from_address: string | null;
    mail_from_name: string | null;
};

const props = defineProps<{ settings: Settings }>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const form = useForm({
    mail_transport: props.settings.mail_transport ?? 'smtp',
    mail_host: props.settings.mail_host ?? '',
    mail_port: props.settings.mail_port ?? 587,
    mail_username: props.settings.mail_username ?? '',
    // Empty in the form = "don't change". The masked placeholder tells the
    // admin a password is already saved.
    mail_password: '' as string,
    mail_encryption: props.settings.mail_encryption ?? 'tls',
    mail_from_address: props.settings.mail_from_address ?? '',
    mail_from_name: props.settings.mail_from_name ?? '',
});

function submit(): void {
    form.put('/admin/settings/email', { preserveScroll: true });
}

const showPassword = ref<boolean>(false);

// Test mail form — kept as a local ref instead of useForm because it
// doesn't need optimistic state and we want fully controlled banners.
const testForm = ref<{ email: string; subject: string; message: string }>({
    email: '',
    subject: 'Moovato — Email configuration test',
    message:
        'This is a test message from the Moovato admin to verify SMTP credentials. If you received this, outbound mail is working.',
});
const testSending = ref<boolean>(false);
const testResult = ref<
    | { kind: 'success'; text: string }
    | { kind: 'error'; text: string }
    | null
>(null);
const testErrors = ref<Record<string, string>>({});

function sendTest(): void {
    testResult.value = null;
    testErrors.value = {};
    testSending.value = true;

    router.post('/admin/settings/email/test', testForm.value, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            const flash =
                (page.props.flash as Record<string, string> | undefined) ?? {};
            if (flash.test_mail_error) {
                testResult.value = { kind: 'error', text: flash.test_mail_error };
            } else if (flash.test_mail_success) {
                testResult.value = {
                    kind: 'success',
                    text: flash.test_mail_success,
                };
            }
        },
        onError: (errors) => {
            testErrors.value = errors as Record<string, string>;
        },
        onFinish: () => {
            testSending.value = false;
        },
    });
}
</script>

<template>
    <Head :title="t('mail.config_title')" />

    <div class="space-y-6 p-4">
        <Heading
            :title="t('mail.config_title')"
            :description="t('mail.config_description')"
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Left column: SMTP form -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <MailIcon class="size-4" />
                        Email Configuration
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_transport">Type</Label>
                            <select
                                id="mail_transport"
                                v-model="form.mail_transport"
                                class="h-9 rounded-md border bg-background px-3 text-sm"
                            >
                                <option value="smtp">smtp</option>
                                <option value="log">log (writes to laravel.log)</option>
                                <option value="sendmail">sendmail</option>
                                <option value="ses">ses (Amazon SES)</option>
                                <option value="mailgun">mailgun</option>
                            </select>
                            <InputError :message="form.errors.mail_transport" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_host">Mail Host</Label>
                            <Input
                                id="mail_host"
                                v-model="form.mail_host"
                                placeholder="smtp.mailtrap.io"
                            />
                            <InputError :message="form.errors.mail_host" />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="grid grid-cols-1 gap-1.5">
                                <Label for="mail_port">Mail Port</Label>
                                <Input
                                    id="mail_port"
                                    v-model.number="form.mail_port"
                                    type="number"
                                    min="1"
                                    max="65535"
                                    placeholder="587"
                                />
                                <InputError :message="form.errors.mail_port" />
                            </div>
                            <div class="grid grid-cols-1 gap-1.5">
                                <Label for="mail_encryption">Mail Encryption</Label>
                                <select
                                    id="mail_encryption"
                                    v-model="form.mail_encryption"
                                    class="h-9 rounded-md border bg-background px-3 text-sm"
                                >
                                    <option value="tls">tls (STARTTLS, port 587)</option>
                                    <option value="ssl">ssl (port 465)</option>
                                    <option value="">none</option>
                                </select>
                                <InputError :message="form.errors.mail_encryption" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_username">Mail Username</Label>
                            <Input
                                id="mail_username"
                                v-model="form.mail_username"
                                autocomplete="off"
                            />
                            <InputError :message="form.errors.mail_username" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_password">Mail Password</Label>
                            <div class="relative">
                                <Input
                                    id="mail_password"
                                    v-model="form.mail_password"
                                    :type="showPassword ? 'text' : 'password'"
                                    :placeholder="
                                        settings.mail_password_set
                                            ? '•••••••• (leave blank to keep)'
                                            : 'Enter password'
                                    "
                                    autocomplete="new-password"
                                    class="pr-10"
                                />
                                <button
                                    type="button"
                                    class="absolute top-1/2 right-2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    @click="showPassword = !showPassword"
                                >
                                    <component
                                        :is="showPassword ? EyeOff : Eye"
                                        class="size-4"
                                    />
                                </button>
                            </div>
                            <p
                                v-if="settings.mail_password_set"
                                class="text-xs text-muted-foreground"
                            >
                                A password is already saved. Leave blank to keep
                                the existing one.
                            </p>
                            <InputError :message="form.errors.mail_password" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_from_address">Mail From Address</Label>
                            <Input
                                id="mail_from_address"
                                v-model="form.mail_from_address"
                                type="email"
                                placeholder="hello@moovato.de"
                            />
                            <InputError :message="form.errors.mail_from_address" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="mail_from_name">Mail From Name</Label>
                            <Input
                                id="mail_from_name"
                                v-model="form.mail_from_name"
                                placeholder="Moovato"
                            />
                            <InputError :message="form.errors.mail_from_name" />
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                <Save class="size-4" />
                                Save Changes
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Right column: Send Test Mail -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Send class="size-4" />
                        Send Test Mail
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-4" @submit.prevent="sendTest">
                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="test_email">Email</Label>
                            <Input
                                id="test_email"
                                v-model="testForm.email"
                                type="email"
                                placeholder="recipient@example.com"
                                required
                            />
                            <InputError :message="testErrors.email" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="test_subject">Subject</Label>
                            <Input
                                id="test_subject"
                                v-model="testForm.subject"
                                required
                            />
                            <InputError :message="testErrors.subject" />
                        </div>

                        <div class="grid grid-cols-1 gap-1.5">
                            <Label for="test_message">Message</Label>
                            <RichTextEditor v-model="testForm.message" />
                            <p class="text-xs text-muted-foreground">
                                Rich-text — bold / italic / lists / links / images all
                                render in the recipient's inbox.
                            </p>
                            <InputError :message="testErrors.message" />
                        </div>

                        <div
                            v-if="testResult"
                            class="flex items-start gap-2 rounded-md border p-3 text-sm"
                            :class="
                                testResult.kind === 'success'
                                    ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200'
                                    : 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-200'
                            "
                        >
                            <component
                                :is="testResult.kind === 'success' ? CheckCircle2 : AlertTriangle"
                                class="mt-0.5 size-4 shrink-0"
                            />
                            <span class="whitespace-pre-wrap">
                                {{ testResult.text }}
                            </span>
                        </div>

                        <div class="flex justify-end">
                            <Button
                                type="submit"
                                :disabled="testSending"
                            >
                                <Send class="size-4" />
                                {{ testSending ? 'Sending…' : 'Send' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
