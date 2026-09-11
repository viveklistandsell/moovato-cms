<?php

declare(strict_types=1);

/**
 * Partner plan tiers — single source of truth.
 *
 * A `feature` flag is a boolean on/off gate for one portal
 * section (e.g. can this tier edit the About text at all?).
 * A `cap` is a numeric ceiling for something the tier CAN edit
 * but only up to N items — `null` means unlimited.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Currency + billing period
    |--------------------------------------------------------------------------
    | Displayed under the price in the pricing cards.
    */
    'currency' => '€',
    'period' => 'month',

    /*
    |--------------------------------------------------------------------------
    | Every gated feature slug we reference from the Policy + Vue.
    |--------------------------------------------------------------------------
    | Keeping the list here (a) makes typos in a controller call
    | fail fast and (b) documents the entire surface area at one
    | glance. Each per-tier `features` array below MUST use these
    | exact keys.
    */
    'features' => [
        'short_description',
        'about',
        'founded',
        'employees',
        'faqs',
        'cover',
        'trust',
        'google',
        'lead',
        'opening_hours',
        'reply_reviews',
    ],

    /*
    |--------------------------------------------------------------------------
    | The three tiers.
    |--------------------------------------------------------------------------
    | Order matters — it drives the order of pricing cards and the
    | "less-restrictive-first" comparison logic used for upgrade /
    | downgrade detection.
    */
    'tiers' => [
        'basic' => [
            'label' => 'Basic',
            'price' => 0,
            'positioning' => 'Free listing so every partner has a presence.',
            'features' => [
                'short_description' => false,
                'about' => false,
                'founded' => false,
                'employees' => false,
                'faqs' => false,
                'cover' => false,
                'trust' => false,
                'google' => false,
                'lead' => false,
                'opening_hours' => false,
                'reply_reviews' => false,
            ],
            'caps' => [
                'contacts' => 1,
                'services' => 1,
                'areas' => 3,
                'gallery' => 3,
                'faqs' => 0,
            ],
            'placement' => 'standard',
        ],

        'premium' => [
            'label' => 'Premium',
            'price' => 19,
            'positioning' => 'Standard paid tier for serious operators.',
            'features' => [
                'short_description' => true,
                'about' => true,
                'founded' => true,
                'employees' => true,
                'faqs' => true,
                'cover' => true,
                'trust' => false,
                'google' => true,
                'lead' => false,
                'opening_hours' => true,
                'reply_reviews' => true,
            ],
            'caps' => [
                'contacts' => 5,
                'services' => 5,
                'areas' => 10,
                'gallery' => 10,
                'faqs' => 10,
            ],
            'placement' => 'boosted',
        ],

        'gold' => [
            'label' => 'Gold',
            'price' => 49,
            'positioning' => 'Everything on, boosted placement, trust badges.',
            'features' => [
                'short_description' => true,
                'about' => true,
                'founded' => true,
                'employees' => true,
                'faqs' => true,
                'cover' => true,
                'trust' => true,
                'google' => true,
                'lead' => true,
                'opening_hours' => true,
                'reply_reviews' => true,
            ],
            'caps' => [
                'contacts' => null,
                'services' => null,
                'areas' => null,
                'gallery' => null,
                'faqs' => null,
            ],
            'placement' => 'featured',
        ],
    ],
];
