<?php

declare(strict_types=1);

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a public review submission from the review form.
 *
 * `authorize()` returns true — this is a public endpoint (anyone can
 * submit a review without an account). Anti-spam is layered separately
 * via route middleware (rate-limit) and the honeypot check below.
 */
final class StoreCompanyReviewRequest extends FormRequest
{
    /**
     * Same nine tags the factory + admin form use. Kept here as the
     * source of truth for validation — Phase 2's frontend form reads
     * this list via the controller, so adding a tag is one code edit.
     *
     * @var array<int, string>
     */
    public const ALLOWED_TAGS = [
        'friendly', 'professional', 'fast', 'on-time', 'reliable',
        'careful', 'fair-pricing', 'communicative', 'value-for-money',
    ];

    /**
     * @var array<int, string>
     */
    public const ALLOWED_SOURCES = ['google', 'referred', 'website', 'social', 'other'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'min:20', 'max:5000'],
            'advantages' => ['nullable', 'array', 'max:9'],
            'advantages.*' => ['string', Rule::in(self::ALLOWED_TAGS)],
            'disadvantages' => ['nullable', 'array', 'max:9'],
            'disadvantages.*' => ['string', Rule::in(self::ALLOWED_TAGS)],
            'source' => ['nullable', Rule::in(self::ALLOWED_SOURCES)],
            'author_name' => ['required', 'string', 'min:2', 'max:120'],
            'author_email' => ['required', 'email', 'max:190'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'proof_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'website_url' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.min' => __('reviews.validation.body_min'),
            'body.max' => __('reviews.validation.body_max'),
            'rating.required' => __('reviews.validation.rating_required'),
            'website_url.max' => __('reviews.validation.spam_rejected'),
        ];
    }
}
