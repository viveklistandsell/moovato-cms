<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertCircle,
    ArrowLeft,
    CheckCircle2,
    Clock,
    Paperclip,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useAdminLocale } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

const t = useT();
const adminLocale = useAdminLocale();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.mail'), href: '/admin/system/email-log' },
    { title: t('sidebar.email_log'), href: '/admin/system/email-log' },
]);

type EmailLogDetail = {
    id: number;
    mailer: string;
    from_address: string;
    from_name: string | null;
    to_addresses: Array<{ address: string; name: string | null }>;
    cc_addresses: Array<{ address: string; name: string | null }> | null;
    bcc_addresses: Array<{ address: string; name: string | null }> | null;
    reply_to: Array<{ address: string; name: string | null }> | null;
    subject: string;
    status: 'pending' | 'sent' | 'failed';
    error: string | null;
    message_id: string | null;
    body_html: string | null;
    body_text: string | null;
    headers: Record<string, string>;
    attachment_count: number;
    created_at: string | null;
    sent_at: string | null;
    triggered_by: { id: number; name: string; email: string } | null;
};

defineProps<{ log: EmailLogDetail }>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const tab = ref<'preview' | 'html' | 'text' | 'headers'>('preview');

function formatTimestamp(iso: string | null): string {
    if (iso === null) {
        return '—';
    }
    return new Date(iso).toLocaleString(adminLocale.value);
}

function joinAddresses(
    list: Array<{ address: string; name: string | null }> | null,
): string {
    if (!list || list.length === 0) {
return '—';
}

    return list
        .map((a) => (a.name ? `${a.name} <${a.address}>` : a.address))
        .join(', ');
}

const STATUS_BADGE: Record<EmailLogDetail['status'], string> = {
    sent: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    pending: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
    failed: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300',
};

const STATUS_ICON: Record<EmailLogDetail['status'], typeof CheckCircle2> = {
    sent: CheckCircle2,
    pending: Clock,
    failed: AlertCircle,
};

const statusLabel = computed<Record<EmailLogDetail['status'], string>>(() => ({
    sent: t('mail.log_status_sent_short'),
    pending: t('mail.log_status_pending_short'),
    failed: t('mail.log_status_failed_short'),
}));
</script>

<template>
    <Head :title="t('mail.show_head_prefix', { subject: log.subject })" />

    <div class="space-y-6 p-4">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <Heading
                :title="log.subject"
                :description="t('mail.show_description')"
            />
            <Button as-child variant="outline" size="sm">
                <Link href="/admin/system/email-log">
                    <ArrowLeft class="size-3.5" />
                    {{ t('mail.show_back_to_log') }}
                </Link>
            </Button>
        </div>

        <!-- Metadata -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">{{ t('mail.show_envelope') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <dl
                    class="grid grid-cols-[max-content_1fr] gap-x-6 gap-y-2 text-sm"
                >
                    <dt class="font-medium text-muted-foreground">{{ t('mail.show_status') }}</dt>
                    <dd>
                        <span
                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase"
                            :class="STATUS_BADGE[log.status]"
                        >
                            <component
                                :is="STATUS_ICON[log.status]"
                                class="size-3"
                            />
                            {{ statusLabel[log.status] }}
                        </span>
                    </dd>

                    <dt class="font-medium text-muted-foreground">{{ t('mail.show_from') }}</dt>
                    <dd>
                        <span v-if="log.from_name">
                            {{ log.from_name }}
                            &lt;{{ log.from_address }}&gt;
                        </span>
                        <span v-else>{{ log.from_address }}</span>
                    </dd>

                    <dt class="font-medium text-muted-foreground">{{ t('mail.show_to') }}</dt>
                    <dd>{{ joinAddresses(log.to_addresses) }}</dd>

                    <template v-if="log.cc_addresses">
                        <dt class="font-medium text-muted-foreground">{{ t('mail.show_cc') }}</dt>
                        <dd>{{ joinAddresses(log.cc_addresses) }}</dd>
                    </template>

                    <template v-if="log.bcc_addresses">
                        <dt class="font-medium text-muted-foreground">{{ t('mail.show_bcc') }}</dt>
                        <dd>{{ joinAddresses(log.bcc_addresses) }}</dd>
                    </template>

                    <template v-if="log.reply_to">
                        <dt class="font-medium text-muted-foreground">
                            {{ t('mail.show_reply_to') }}
                        </dt>
                        <dd>{{ joinAddresses(log.reply_to) }}</dd>
                    </template>

                    <dt class="font-medium text-muted-foreground">{{ t('mail.show_mailer') }}</dt>
                    <dd class="font-mono text-xs">{{ log.mailer }}</dd>

                    <dt class="font-medium text-muted-foreground">
                        {{ t('mail.show_created_at') }}
                    </dt>
                    <dd>{{ formatTimestamp(log.created_at) }}</dd>

                    <dt class="font-medium text-muted-foreground">{{ t('mail.show_sent_at') }}</dt>
                    <dd>{{ formatTimestamp(log.sent_at) }}</dd>

                    <template v-if="log.triggered_by">
                        <dt class="font-medium text-muted-foreground">
                            {{ t('mail.show_triggered_by') }}
                        </dt>
                        <dd>
                            {{ log.triggered_by.name }}
                            <span class="text-xs text-muted-foreground">
                                · {{ log.triggered_by.email }}
                            </span>
                        </dd>
                    </template>

                    <template v-if="log.attachment_count > 0">
                        <dt class="font-medium text-muted-foreground">
                            {{ t('mail.show_attachments') }}
                        </dt>
                        <dd
                            class="inline-flex items-center gap-1.5 text-muted-foreground"
                        >
                            <Paperclip class="size-3.5" />
                            {{ log.attachment_count }}
                        </dd>
                    </template>

                    <template v-if="log.message_id">
                        <dt class="font-medium text-muted-foreground">
                            {{ t('mail.show_message_id') }}
                        </dt>
                        <dd class="font-mono text-xs break-all">
                            {{ log.message_id }}
                        </dd>
                    </template>
                </dl>
            </CardContent>
        </Card>

        <!-- Failure detail -->
        <Card
            v-if="log.error"
            class="border-rose-200 dark:border-rose-900"
        >
            <CardHeader>
                <CardTitle class="text-base text-rose-700 dark:text-rose-300">
                    {{ t('mail.show_send_failed') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <pre
                    class="overflow-auto rounded-md bg-rose-50 p-3 font-mono text-xs whitespace-pre-wrap text-rose-900 dark:bg-rose-950 dark:text-rose-200"
                    >{{ log.error }}</pre>
            </CardContent>
        </Card>

        <!-- Body / tabs -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">{{ t('mail.show_content') }}</CardTitle>
                <div class="mt-2 flex flex-wrap gap-1 text-xs">
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 transition-colors"
                        :class="
                            tab === 'preview'
                                ? 'bg-primary text-primary-foreground'
                                : 'border bg-background hover:bg-accent'
                        "
                        @click="tab = 'preview'"
                    >
                        {{ t('mail.show_tab_preview') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 transition-colors"
                        :class="
                            tab === 'html'
                                ? 'bg-primary text-primary-foreground'
                                : 'border bg-background hover:bg-accent'
                        "
                        @click="tab = 'html'"
                    >
                        {{ t('mail.show_tab_html') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 transition-colors"
                        :class="
                            tab === 'text'
                                ? 'bg-primary text-primary-foreground'
                                : 'border bg-background hover:bg-accent'
                        "
                        @click="tab = 'text'"
                    >
                        {{ t('mail.show_tab_text') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 transition-colors"
                        :class="
                            tab === 'headers'
                                ? 'bg-primary text-primary-foreground'
                                : 'border bg-background hover:bg-accent'
                        "
                        @click="tab = 'headers'"
                    >
                        {{ t('mail.show_tab_headers') }}
                    </button>
                </div>
            </CardHeader>
            <CardContent class="space-y-2">
                <iframe
                    v-if="tab === 'preview' && log.body_html"
                    :srcdoc="log.body_html"
                    sandbox=""
                    class="min-h-[60vh] w-full rounded-md border bg-white"
                />
                <p
                    v-else-if="tab === 'preview'"
                    class="rounded-md border border-dashed p-6 text-center text-xs text-muted-foreground"
                >
                    {{ t('mail.show_no_html_body') }}
                </p>

                <pre
                    v-else-if="tab === 'html'"
                    class="max-h-[60vh] overflow-auto rounded-md border bg-muted/40 p-4 font-mono text-xs whitespace-pre-wrap"
                    >{{ log.body_html ?? t('mail.show_no_html_inline') }}</pre>

                <pre
                    v-else-if="tab === 'text'"
                    class="max-h-[60vh] overflow-auto rounded-md border bg-muted/40 p-4 font-mono text-xs whitespace-pre-wrap"
                    >{{ log.body_text ?? t('mail.show_no_text_inline') }}</pre>

                <dl
                    v-else-if="tab === 'headers'"
                    class="grid grid-cols-[max-content_1fr] gap-x-4 gap-y-1 text-xs"
                >
                    <template
                        v-for="(value, key) in log.headers"
                        :key="key"
                    >
                        <dt class="font-mono text-muted-foreground">
                            {{ key }}
                        </dt>
                        <dd class="font-mono break-all">{{ value }}</dd>
                    </template>
                    <p
                        v-if="Object.keys(log.headers).length === 0"
                        class="col-span-2 rounded-md border border-dashed p-6 text-center text-xs text-muted-foreground"
                    >
                        {{ t('mail.show_no_headers') }}
                    </p>
                </dl>
            </CardContent>
        </Card>
    </div>
</template>
