import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import { reveal } from '@/directives/reveal';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import FrontendLayout from '@/layouts/frontend/FrontendLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Moovato';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            // Page renderer handles its own chrome — the "nolayout" template
            // skips header/footer entirely, so we don't wrap globally here.
            case name === 'frontend/page/Index':
                return null;
            case name.startsWith('frontend/'):
                return FrontendLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            case name.startsWith('portal/'):
                return null;
            // Partner (company owner) auth + dashboard pages — bespoke
            // centered-card layout, no admin chrome, no public site header.
            case name.startsWith('partner/'):
                return null;
            default:
                return AppLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
    withApp(app) {
        app.directive('reveal', reveal);
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
