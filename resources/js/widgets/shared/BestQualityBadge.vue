<script setup lang="ts">
import { onMounted, useId, useTemplateRef } from 'vue';

const uid = useId();
const root = useTemplateRef<SVGSVGElement>('root');

onMounted(() => {
    const el = root.value;
    if (!el) {
        return;
    }
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.style.opacity = '1';
        return;
    }

    const rect = el.getBoundingClientRect();
    const dx = window.innerWidth / 2 - (rect.left + rect.width / 2);
    const dy = window.innerHeight / 2 - (rect.top + rect.height / 2);

    el.style.opacity = '1';
    el.style.transform = `translate(${dx}px, ${dy}px) scale(5) rotate(360deg)`;

    requestAnimationFrame(() => {
        el.style.transition = 'transform 2s cubic-bezier(0.32, 0.72, 0, 1)';
        el.style.transform = 'translate(0, 0) scale(1) rotate(0deg)';
    });
});
</script>

<template>
    <svg
        ref="root"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 512 512"
        class="mv-badge-quality"
        aria-hidden="true"
    >
        <defs>
            <linearGradient :id="`rimGold-${uid}`" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#FFF3B4" />
                <stop offset="0.28" stop-color="#F3D678" />
                <stop offset="0.55" stop-color="#D9A93F" />
                <stop offset="0.8" stop-color="#B07C1F" />
                <stop offset="1" stop-color="#8A5C12" />
            </linearGradient>
            <linearGradient :id="`bandGoldTop-${uid}`" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#FFF0A8" />
                <stop offset="1" stop-color="#C4922C" />
            </linearGradient>
            <linearGradient :id="`bandGoldBot-${uid}`" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#F7DE8C" />
                <stop offset="1" stop-color="#A9761B" />
            </linearGradient>
            <linearGradient :id="`textGold-${uid}`" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#FFEFAC" />
                <stop offset="0.5" stop-color="#F0CE6E" />
                <stop offset="1" stop-color="#D2A344" />
            </linearGradient>
            <linearGradient :id="`starGold-${uid}`" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="#FFEFA8" />
                <stop offset="1" stop-color="#D6A63F" />
            </linearGradient>
            <radialGradient :id="`darkBg-${uid}`" cx="0.5" cy="0.42" r="0.75">
                <stop offset="0" stop-color="#4A1708" />
                <stop offset="0.6" stop-color="#3A1106" />
                <stop offset="1" stop-color="#2B0B04" />
            </radialGradient>
            <radialGradient :id="`darkBand-${uid}`" cx="0.5" cy="0.5" r="0.9">
                <stop offset="0" stop-color="#451507" />
                <stop offset="1" stop-color="#300D04" />
            </radialGradient>
            <clipPath :id="`hexClip-${uid}`">
                <path
                    d="M 228.55 36.06 Q 256.00 16.00 283.45 36.06 L 458.85 164.20 Q 475.00 176.00 475.00 196.00 L 475.00 316.00 Q 475.00 336.00 458.85 347.80 L 283.45 475.94 Q 256.00 496.00 228.55 475.94 L 53.15 347.80 Q 37.00 336.00 37.00 316.00 L 37.00 196.00 Q 37.00 176.00 53.15 164.20 Z"
                />
            </clipPath>
        </defs>

        <path
            d="M 228.55 36.06 Q 256.00 16.00 283.45 36.06 L 458.85 164.20 Q 475.00 176.00 475.00 196.00 L 475.00 316.00 Q 475.00 336.00 458.85 347.80 L 283.45 475.94 Q 256.00 496.00 228.55 475.94 L 53.15 347.80 Q 37.00 336.00 37.00 316.00 L 37.00 196.00 Q 37.00 176.00 53.15 164.20 Z"
            :fill="`url(#rimGold-${uid})`"
        />
        <path
            d="M 233.34 52.69 Q 256.00 36.25 278.66 52.69 L 448.67 176.03 Q 460.00 184.25 460.00 198.25 L 460.00 313.75 Q 460.00 327.75 448.67 335.97 L 278.66 459.31 Q 256.00 475.75 233.34 459.31 L 63.33 335.97 Q 52.00 327.75 52.00 313.75 L 52.00 198.25 Q 52.00 184.25 63.33 176.03 Z"
            :fill="`url(#darkBg-${uid})`"
        />
        <path
            d="M 234.93 59.59 Q 256.00 44.35 277.07 59.59 L 444.28 180.52 Q 454.00 187.55 454.00 199.55 L 454.00 312.45 Q 454.00 324.45 444.28 331.48 L 277.07 452.41 Q 256.00 467.65 234.93 452.41 L 67.72 331.48 Q 58.00 324.45 58.00 312.45 L 58.00 199.55 Q 58.00 187.55 67.72 180.52 Z"
            fill="none"
            stroke="#C89A3F"
            stroke-opacity="0.45"
            stroke-width="1.5"
        />

        <g :clip-path="`url(#hexClip-${uid})`">
            <rect x="30" y="180" width="452" height="152" :fill="`url(#bandGoldBot-${uid})`" />
            <rect x="30" y="181" width="452" height="14" :fill="`url(#bandGoldTop-${uid})`" />
            <rect x="30" y="195" width="452" height="122" :fill="`url(#darkBand-${uid})`" />
            <rect x="30" y="317" width="452" height="13" :fill="`url(#bandGoldBot-${uid})`" />
            <rect x="30" y="316.4" width="452" height="1.6" fill="#FFF2AE" opacity="0.85" />
            <rect x="30" y="194.4" width="452" height="1.2" fill="#5A2E0A" opacity="0.8" />
        </g>

        <g :fill="`url(#starGold-${uid})`">
            <polygon points="152.00,140.50 154.70,148.28 162.94,148.45 156.37,153.42 158.76,161.30 152.00,156.60 145.24,161.30 147.63,153.42 141.06,148.45 149.30,148.28" />
            <polygon points="204.00,140.50 206.70,148.28 214.94,148.45 208.37,153.42 210.76,161.30 204.00,156.60 197.24,161.30 199.63,153.42 193.06,148.45 201.30,148.28" />
            <polygon points="256.00,140.50 258.70,148.28 266.94,148.45 260.37,153.42 262.76,161.30 256.00,156.60 249.24,161.30 251.63,153.42 245.06,148.45 253.30,148.28" />
            <polygon points="308.00,140.50 310.70,148.28 318.94,148.45 312.37,153.42 314.76,161.30 308.00,156.60 301.24,161.30 303.63,153.42 297.06,148.45 305.30,148.28" />
            <polygon points="360.00,140.50 362.70,148.28 370.94,148.45 364.37,153.42 366.76,161.30 360.00,156.60 353.24,161.30 355.63,153.42 349.06,148.45 357.30,148.28" />
            <polygon points="152.00,337.50 154.70,345.28 162.94,345.45 156.37,350.42 158.76,358.30 152.00,353.60 145.24,358.30 147.63,350.42 141.06,345.45 149.30,345.28" />
            <polygon points="204.00,337.50 206.70,345.28 214.94,345.45 208.37,350.42 210.76,358.30 204.00,353.60 197.24,358.30 199.63,350.42 193.06,345.45 201.30,345.28" />
            <polygon points="256.00,337.50 258.70,345.28 266.94,345.45 260.37,350.42 262.76,358.30 256.00,353.60 249.24,358.30 251.63,350.42 245.06,345.45 253.30,345.28" />
            <polygon points="308.00,337.50 310.70,345.28 318.94,345.45 312.37,350.42 314.76,358.30 308.00,353.60 301.24,358.30 303.63,350.42 297.06,345.45 305.30,345.28" />
            <polygon points="360.00,337.50 362.70,345.28 370.94,345.45 364.37,350.42 366.76,358.30 360.00,353.60 353.24,358.30 355.63,350.42 349.06,345.45 357.30,345.28" />
        </g>

        <g :fill="`url(#textGold-${uid})`">
            <path transform="translate(81.00,301.0) scale(0.07869,-0.12857)" d="M41 700H207Q292 700 331.0 660.5Q370 621 370 539V511Q370 457 352.5 423.0Q335 389 299 374V372Q381 344 381 226V166Q381 85 338.5 42.5Q296 0 214 0H41ZM194 415Q227 415 243.5 432.0Q260 449 260 489V528Q260 566 246.5 583.0Q233 600 204 600H151V415ZM214 100Q243 100 257.0 115.5Q271 131 271 169V230Q271 278 254.5 296.5Q238 315 200 315H151V100Z" />
            <path transform="translate(114.68,301.0) scale(0.07869,-0.12857)" d="M41 700H341V600H151V415H302V315H151V100H341V0H41Z" />
            <path transform="translate(145.13,301.0) scale(0.07869,-0.12857)" d="M22 166V206H126V158Q126 90 183 90Q211 90 225.5 106.5Q240 123 240 160Q240 204 220.0 237.5Q200 271 146 318Q78 378 51.0 426.5Q24 475 24 536Q24 619 66.0 664.5Q108 710 188 710Q267 710 307.5 664.5Q348 619 348 534V505H244V541Q244 577 230.0 593.5Q216 610 189 610Q134 610 134 543Q134 505 154.5 472.0Q175 439 229 392Q298 332 324.0 283.0Q350 234 350 168Q350 82 307.5 36.0Q265 -10 184 -10Q104 -10 63.0 35.5Q22 81 22 166Z" />
            <path transform="translate(176.29,301.0) scale(0.07869,-0.12857)" d="M127 600H12V700H352V600H237V0H127Z" />
            <path transform="translate(223.19,301.0) scale(0.07869,-0.12857)" d="M273 2Q242 -10 200 -10Q119 -10 76.0 36.0Q33 82 33 166V534Q33 618 76.0 664.0Q119 710 200 710Q281 710 324.0 664.0Q367 618 367 534V166Q367 96 336 51Q342 41 351.0 38.0Q360 35 377 35H394V-65H365Q290 -65 273 2ZM257 159V541Q257 610 200 610Q143 610 143 541V159Q143 90 200 90Q257 90 257 159Z" />
            <path transform="translate(256.55,301.0) scale(0.07869,-0.12857)" d="M37 166V700H147V158Q147 122 161.5 106.0Q176 90 203 90Q230 90 244.5 106.0Q259 122 259 158V700H365V166Q365 81 323.0 35.5Q281 -10 201 -10Q121 -10 79.0 35.5Q37 81 37 166Z" />
            <path transform="translate(290.07,301.0) scale(0.07869,-0.12857)" d="M126 700H275L389 0H279L259 139V137H134L114 0H12ZM246 232 197 578H195L147 232Z" />
            <path transform="translate(323.51,301.0) scale(0.07869,-0.12857)" d="M41 700H151V100H332V0H41Z" />
            <path transform="translate(352.47,301.0) scale(0.07869,-0.12857)" d="M41 700H151V0H41Z" />
            <path transform="translate(369.47,301.0) scale(0.07869,-0.12857)" d="M127 600H12V700H352V600H237V0H127Z" />
            <path transform="translate(400.00,301.0) scale(0.07869,-0.12857)" d="M142 298 9 700H126L201 443H203L278 700H385L252 298V0H142Z" />
        </g>
    </svg>
</template>
