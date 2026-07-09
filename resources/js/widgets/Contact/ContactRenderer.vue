<script setup lang="ts">
import { ArrowRight, Check, Mail, MapPin, PhoneCall } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

type Settings = {
    map_embed_url?: string | null;
};

type Data = {
    phone_title?: string;
    phone?: string;
    email_title?: string;
    email?: string;
    location_title?: string;
    address?: string;
    address_url?: string;
    eyebrow?: string;
    heading_lead?: string;
    heading_highlight?: string;
    description?: string;
    name_placeholder?: string;
    email_placeholder?: string;
    phone_placeholder?: string;
    service_placeholder?: string;
    services?: string[];
    message_placeholder?: string;
    submit_label?: string;
    success_title?: string;
    success_text?: string;
    watermark?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const services = computed<string[]>(() => props.data.services ?? []);

const submitted = ref(false);

const form = reactive({
    name: '',
    email: '',
    phone: '',
    service: '',
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
    <section id="kontakt" class="mv-contact section-py">
        <div class="container-xl">
            <div
                class="grid grid-cols-1 items-center gap-y-8 lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)] lg:gap-x-8"
            >
                <!-- LEFT: info panel + map -->
                <div class="mv-contact__info">
                    <div class="mv-contact__info__inner">
                        <div v-if="data.phone" class="mv-contact__info__card">
                            <div class="mv-contact__info__icon">
                                <PhoneCall :size="40" />
                            </div>
                            <div class="mv-contact__info__content">
                                <h3 class="mv-contact__info__title">
                                    {{ data.phone_title }}
                                </h3>
                                <a
                                    :href="`tel:${data.phone}`"
                                    class="mv-contact__info__link"
                                    >{{ data.phone }}</a
                                >
                            </div>
                        </div>

                        <div v-if="data.email" class="mv-contact__info__card">
                            <div class="mv-contact__info__icon">
                                <Mail :size="40" />
                            </div>
                            <div class="mv-contact__info__content">
                                <h3 class="mv-contact__info__title">
                                    {{ data.email_title }}
                                </h3>
                                <a
                                    :href="`mailto:${data.email}`"
                                    class="mv-contact__info__link"
                                    >{{ data.email }}</a
                                >
                            </div>
                        </div>

                        <div v-if="data.address" class="mv-contact__info__card">
                            <div class="mv-contact__info__icon">
                                <MapPin :size="40" />
                            </div>
                            <div class="mv-contact__info__content">
                                <h3 class="mv-contact__info__title">
                                    {{ data.location_title }}
                                </h3>
                                <a
                                    :href="data.address_url || '#'"
                                    target="_blank"
                                    rel="noopener"
                                    class="mv-contact__info__link"
                                    >{{ data.address }}</a
                                >
                            </div>
                        </div>
                        <div
                            class="mv-contact__info__card mv-contact__info__card--map"
                        >
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5136022.174842897!2d5.1650989727241!3d51.05634991980449!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479a721ec2b1be6b%3A0x75e85d6b8e91e55b!2sGermany!5e0!3m2!1sen!2sin!4v1783491787088!5m2!1sen!2sin"
                                width="100%"
                                height="200"
                                loading="lazy"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                    

                    <div
                        v-if="settings.map_embed_url"
                        class="mv-contact__map"
                    >
                        <iframe
                            :src="settings.map_embed_url"
                            title="Standort auf der Karte"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>

                <!-- RIGHT: form -->
                <div class="mv-contact__form">
                    <div class="mv-contact__sec-title">
                        <div class="mv-contact__sec-title-top">
                            <span class="mv-contact__tagline">
                                {{ data.eyebrow }}
                            </span>
                        </div>
                        <h2 class="mv-contact__title">
                            {{ data.heading_lead }}
                            <span class="mv-contact__title-accent">{{
                                data.heading_highlight
                            }}</span>
                        </h2>
                    </div>

                    <div
                        v-if="data.description"
                        class="mv-rte mv-contact__lead"
                        v-html="data.description"
                    ></div>

                    <form
                        v-if="!submitted"
                        class="mv-contact__form-one"
                        novalidate
                        @submit.prevent="submit"
                    >
                        <div class="mv-contact__group">
                            <div
                                class="mv-contact__control mv-contact__control--full"
                            >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    :placeholder="
                                        data.name_placeholder || 'Ihr Name *'
                                    "
                                    autocomplete="name"
                                />
                                <p
                                    v-if="errors.name"
                                    class="mv-contact__error"
                                >
                                    {{ errors.name }}
                                </p>
                            </div>
                            <div
                                class="mv-contact__control mv-contact__control--full"
                            >
                                <input
                                    v-model="form.email"
                                    type="email"
                                    :placeholder="
                                        data.email_placeholder || 'Ihre E-Mail *'
                                    "
                                    autocomplete="email"
                                />
                                <p
                                    v-if="errors.email"
                                    class="mv-contact__error"
                                >
                                    {{ errors.email }}
                                </p>
                            </div>
                            <div class="mv-contact__control">
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    :placeholder="
                                        data.phone_placeholder ||
                                        'Ihre Telefonnummer *'
                                    "
                                    autocomplete="tel"
                                />
                            </div>
                            <div class="mv-contact__control">
                                <select
                                    v-model="form.service"
                                    class="mv-contact__select"
                                >
                                    <option value="" disabled>
                                        {{
                                            data.service_placeholder ||
                                            'Service auswählen'
                                        }}
                                    </option>
                                    <option
                                        v-for="(service, i) in services"
                                        :key="i"
                                        :value="service"
                                    >
                                        {{ service }}
                                    </option>
                                </select>
                            </div>
                            <div
                                class="mv-contact__control mv-contact__control--full"
                            >
                                <textarea
                                    v-model="form.message"
                                    :placeholder="
                                        data.message_placeholder ||
                                        'Ihre Nachricht *'
                                    "
                                ></textarea>
                                <p
                                    v-if="errors.message"
                                    class="mv-contact__error"
                                >
                                    {{ errors.message }}
                                </p>
                            </div>
                            <div
                                class="mv-contact__control mv-contact__control--full"
                            >
                                <button type="submit" class="mv-contact__btn">
                                    {{ data.submit_label }}
                                    <span class="mv-contact__btn-icon"
                                        ><ArrowRight :size="16"
                                    /></span>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div v-else class="mv-contact__success">
                        <span class="mv-contact__success-icon"
                            ><Check :size="28"
                        /></span>
                        <h3>{{ data.success_title }}</h3>
                        <p>{{ data.success_text }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="data.watermark" class="mv-contact__bottom">
            <h2 class="mv-contact__bottom-title" aria-hidden="true">
                {{ data.watermark }}
            </h2>
        </div>
    </section>
</template>
