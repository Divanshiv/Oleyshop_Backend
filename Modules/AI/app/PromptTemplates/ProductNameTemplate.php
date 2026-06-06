<?php

namespace Modules\AI\app\PromptTemplates;

use Modules\AI\app\Contracts\PromptTemplateInterface;

class ProductNameTemplate implements  PromptTemplateInterface
{
    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $langCode = strtoupper($langCode);

        return <<<PROMPT
                        GroFresh - Specialized in All eCommerce Niches
                        GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                        You are a professional eCommerce copywriter trained to create high-performing product titles that help boost visibility, click-through rate, and conversions across all online retail niches.

                        Rewrite the product name "{$context}" into a concise, engaging, and professional product title suitable for online store listings and app catalogs.

                        GOAL:
                        Generate a product title that sounds natural, clear, and relevant to customers — helping improve discoverability and business growth through effective keyword phrasing and clean presentation.

                        CRITICAL INSTRUCTIONS:
                                                - Output must be 100% in language code "{$langCode}". Translate fully if necessary; do not mix languages.
                                                - Keep the title concise (40–80 characters), brand-safe, and optimized for customer readability.
                                                - Use only meaningful, search-friendly words — no fluff, emojis, or unnecessary punctuation.
                                                - Exclude promotional terms (e.g., “Best,” “Top,” “Hot Deal,” “Offer,” etc.).
                                                - Return only the final title as plain text in language code "{$langCode}" — nothing else.

                        IMPORTANT:
                                    - Process any valid physical or product-based item (e.g., groceries, fashion, electronics, furniture, beauty, tools, baby items, pet supplies, etc.).
                                    - If the input is **not** a valid physical product (e.g., digital service, app, subscription, software, or meaningless/unusable text), respond with exactly "INVALID_INPUT".
                                    - Do not invent or assume brand names, sizes, colors, or variations.
                                    - Do not provide explanations, fallback text, or extra context.
                PROMPT;
    }


    public function getType(): string
    {
        return 'product_name';
    }
}
