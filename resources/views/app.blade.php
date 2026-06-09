<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        @php($siteSetting = App\Models\SiteSetting::current())
        @php($siteTranslation = $siteSetting->translation(app()->getLocale()))
        @php($faviconUrl = $siteSetting->favicon_path ? '/storage/'.ltrim($siteSetting->favicon_path, '/') : null)
        @php($ogImageUrl = $siteSetting->default_og_image_path ? url('/storage/'.ltrim($siteSetting->default_og_image_path, '/')) : null)

        @if ($faviconUrl)
            <link rel="icon" href="{{ $faviconUrl }}" sizes="any">
            <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
        @else
            <link rel="icon" href="/favicon.ico" sizes="any">
            <link rel="icon" href="/favicon.svg" type="image/svg+xml">
            <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        @endif

        @if ($siteSetting->theme_color)
            <meta name="theme-color" content="{{ $siteSetting->theme_color }}">
        @endif

        @if ($siteTranslation?->default_meta_description)
            <meta name="description" content="{{ $siteTranslation->default_meta_description }}">
            <meta property="og:description" content="{{ $siteTranslation->default_meta_description }}">
        @endif
        @if ($ogImageUrl)
            <meta property="og:image" content="{{ $ogImageUrl }}">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:image" content="{{ $ogImageUrl }}">
        @endif
        @if ($siteSetting->site_name)
            <meta property="og:site_name" content="{{ $siteSetting->site_name }}">
        @endif
        @if (! ($siteSetting->robots_index ?? true))
            <meta name="robots" content="noindex,nofollow">
        @endif

        @if ($siteSetting->google_tag_manager_id)
            <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $siteSetting->google_tag_manager_id }}');</script>
        @endif
        @if ($siteSetting->google_analytics_id)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSetting->google_analytics_id }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $siteSetting->google_analytics_id }}');
            </script>
        @endif
        @if ($siteSetting->meta_pixel_id)
            <script>
                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '{{ $siteSetting->meta_pixel_id }}');
                fbq('track', 'PageView');
            </script>
        @endif

        {{-- Admin-supplied raw HTML — rendered verbatim. Only admins with the
             settings.site permission can write here, so trust is consistent
             with any other admin-authored content (page widgets, custom
             CSS, etc.). --}}
        @if ($siteSetting->custom_head_code)
            {!! $siteSetting->custom_head_code !!}
        @endif

        @fonts

        @vite(['resources/css/app.css', 'resources/css/gs.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ $siteSetting->site_name ?: config('app.name', 'Moovato') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        @if ($siteSetting->google_tag_manager_id)
            <noscript>
                <iframe src="https://www.googletagmanager.com/ns.html?id={{ $siteSetting->google_tag_manager_id }}"
                        height="0" width="0" style="display:none;visibility:hidden"></iframe>
            </noscript>
        @endif
        <x-inertia::app />
    </body>
</html>
