<script setup lang="ts">
import { Check, MailPlus, MapPin, Send } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

type Data = {
    eyebrow?: string;
    heading_lead?: string;
    heading_highlight?: string;
    email_label?: string;
    email?: string;
    phone?: string;
    location_label?: string;
    address?: string;
    name_field_label?: string;
    email_field_label?: string;
    message_field_label?: string;
    message_placeholder?: string;
    submit_label?: string;
    success_title?: string;
    success_text?: string;
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();

const submitted = ref(false);

const form = reactive({
    name: '',
    email: '',
    message: '',
});

const errors = ref<Record<string, string>>({});

function submit(): void {
    const e: Record<string, string> = {};

    if (!form.name.trim()) {
        e.name = 'Bitte geben Sie Ihren Namen ein.';
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        e.email = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }

    if (!form.message.trim()) {
        e.message = 'Bitte schreiben Sie uns eine Nachricht.';
    }

    errors.value = e;

    if (Object.keys(e).length === 0) {
        submitted.value = true;
    }
}
</script>

<template>
    <section class="mv-contact section-py" id="kontakt">
        <div
            class="container-xl grid grid-cols-1 items-stretch gap-y-8 lg:grid-cols-2 lg:gap-x-5"
        >
            <!-- LEFT: info card (editable) -->
            <aside class="mv-contact__card">
                <span class="mv-contact__eyebrow">{{ data.eyebrow }}</span>
                <h2 class="mv-contact__title">
                    {{ data.heading_lead }}
                    <span class="mv-contact__highlight">{{
                        data.heading_highlight
                    }}</span>
                </h2>

                <div class="mv-contact__info">
                    <div class="mv-contact__info-item">
                        <span class="mv-contact__icon"
                            ><MailPlus :size="22"
                        /></span>
                        <h3>{{ data.email_label }}</h3>
                        <p>
                            <a
                                v-if="data.email"
                                :href="`mailto:${data.email}`"
                                >{{ data.email }}</a
                            >
                        </p>
                        <p v-if="data.phone">
                            <a :href="`tel:${data.phone}`">{{ data.phone }}</a>
                        </p>
                    </div>

                    <div class="mv-contact__info-item">
                        <span class="mv-contact__icon"
                            ><MapPin :size="22"
                        /></span>
                        <h3>{{ data.location_label }}</h3>
                        <p>{{ data.address }}</p>
                    </div>
                </div>
            </aside>

            <!-- RIGHT: contact form (static fields) -->
            <div class="mv-contact__form">
                <template v-if="!submitted">
                    <form novalidate @submit.prevent="submit">
                        <div class="mv-contact__row">
                            <div class="mv-contact__field">
                                <label class="mv-contact__label" for="mvc-name"
                                    >{{ data.name_field_label }}*</label
                                >
                                <input
                                    id="mvc-name"
                                    class="mv-contact__input"
                                    type="text"
                                    v-model="form.name"
                                    autocomplete="name"
                                />
                                <p v-if="errors.name" class="mv-contact__error">
                                    {{ errors.name }}
                                </p>
                            </div>
                            <div class="mv-contact__field">
                                <label class="mv-contact__label" for="mvc-email"
                                    >{{ data.email_field_label }}*</label
                                >
                                <input
                                    id="mvc-email"
                                    class="mv-contact__input"
                                    type="email"
                                    v-model="form.email"
                                    autocomplete="email"
                                />
                                <p
                                    v-if="errors.email"
                                    class="mv-contact__error"
                                >
                                    {{ errors.email }}
                                </p>
                            </div>
                        </div>

                        <div class="mv-contact__field">
                            <label class="mv-contact__label" for="mvc-message"
                                >{{ data.message_field_label }}*</label
                            >
                            <textarea
                                id="mvc-message"
                                class="mv-contact__input mv-contact__textarea"
                                v-model="form.message"
                                :placeholder="data.message_placeholder"
                                rows="5"
                            ></textarea>
                            <p v-if="errors.message" class="mv-contact__error">
                                {{ errors.message }}
                            </p>
                        </div>

                        <button type="submit" class="mv-contact__submit">
                            {{ data.submit_label }}
                            <Send :size="18" />
                        </button>
                    </form>
                </template>

                <div v-else class="mv-contact__success">
                    <span class="mv-contact__success-icon"
                        ><Check :size="28"
                    /></span>
                    <h3>{{ data.success_title }}</h3>
                    <p>{{ data.success_text }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
