<?php

namespace Modules\AI\app\PromptTemplates;

use Modules\AI\app\Contracts\PromptTemplateInterface;

class GenerateProductTitleSuggestionTemplate implements PromptTemplateInterface
{

    public function build(mixed $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $langCode = strtoupper($langCode);
        $keywordsText = $context;
        if (is_array($context)) {
            $keywordsText = implode(' ', $context);
        }
        return <<<PROMPT
                        GroFresh - Specialized in All eCommerce Niches
                        GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                        You are a professional eCommerce copywriter trained to create high-performing product titles that help boost visibility, click-through rate, and conversions across all online retail niches.

                        Using the keywords "{$keywordsText}", generate 4 professional, clean, and concise product titles for online stores.

               CRITICAL INSTRUCTIONS:
               - Output must be 100% in language code "{$langCode}". Translate fully if necessary; do not mix languages.
               - Titles must use the keywords naturally.
               - Keep the title concise (40–80 characters), brand-safe, and optimized for customer readability.
               - Use only meaningful, search-friendly words — no fluff, emojis, or unnecessary punctuation.
               - Exclude promotional terms (e.g., “Best,” “Top,” “Hot Deal,” “Offer,” etc.).
               - Do not add specifications in the title
               - Return exactly 4 titles in **plain JSON** format as shown below (do not include ```json``` or any extra markdown):

               {
                 "titles": [
                   "Title 1",
                   "Title 2",
                   "Title 3",
                   "Title 4"
                 ]
               }


                IMPORTANT:
                - If the keywords are not relevant to e-commerce products or is meaningless, respond with only the word "INVALID_INPUT".
                - Do not invent or assume brand names, sizes, colors, or variations.
                - Do not provide explanations, fallback text, or extra context.
               PROMPT;
    }
    public function getType(): string
    {
        return "generate_product_title_suggestion";
    }

}
