<script setup lang="ts">
import { ArrowRight, Megaphone, Zap } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Data = {
    eyebrow?: string;
    heading?: string;
    lead?: string;
    primary_label?: string;
    primary_url?: string;
    secondary_label?: string;
    secondary_url?: string;
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();
</script>

<template>
    <section class="mv-getintouch section-py">
        <div class="container-xl">
            <div
                class="mv-getintouch__card grid grid-cols-1 items-center gap-y-8 lg:grid-cols-[1.3fr_1fr] lg:gap-x-5"
            >
                <div class="mv-getintouch__body">
                    <span class="mv-getintouch__eyebrow">{{
                        data.eyebrow
                    }}</span>
                    <h2 class="mv-getintouch__title">{{ data.heading }}</h2>
                    <p class="mv-getintouch__lead">{{ data.lead }}</p>

                    <div class="mv-getintouch__actions">
                        <NextButton
                            v-if="data.primary_label"
                            :label="data.primary_label"
                            :href="data.primary_url || '#'"
                        />
                        <a
                            v-if="data.secondary_label"
                            :href="data.secondary_url || '#'"
                            class="link-cta"
                        >
                            {{ data.secondary_label }} <ArrowRight :size="12" />
                        </a>
                    </div>
                </div>

                <div class="mv-getintouch__art" aria-hidden="true">
                    <Zap
                        class="mv-getintouch__bolt mv-getintouch__bolt--1"
                        :size="34"
                    />
                    <Zap
                        class="mv-getintouch__bolt mv-getintouch__bolt--2"
                        :size="26"
                    />
                    <Zap
                        class="mv-getintouch__bolt mv-getintouch__bolt--3"
                        :size="20"
                    />
                    <span class="mv-getintouch__megaphone"
                        ><Megaphone :size="88"
                    /></span>
                </div>
            </div>
        </div>
    </section>
</template>
