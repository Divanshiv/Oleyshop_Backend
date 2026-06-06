<?php

namespace Modules\AI\app\PromptTemplates;

use Modules\AI\app\Contracts\PromptTemplateInterface;
use Modules\AI\app\Services\ProductResourceService;

class ProductDescriptionTemplate implements PromptTemplateInterface
{
    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $langCode = strtoupper($langCode);
        $contextSafe = addslashes($context ?? '');

        return <<<PROMPT
                        GroFresh - Specialized in All eCommerce Niches
                        GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                        You are a professional eCommerce copywriter trained to create high-performing product descriptions that boost visibility, click-through rate, and conversions across all online retail niches.

                        Generate a detailed, engaging, and persuasive **product description** in **HTML format** for the item named "{$contextSafe}".

                        CRITICAL INSTRUCTIONS:
                                                - Output must be 100% in language code "{$langCode}". Translate fully if necessary; do not mix languages.
                                                - Adapt tone and phrasing naturally for {$langCode} readers.
                                                - Begin with a short introductory paragraph describing what the product is, its main use or benefit, and who it is for.
                                                - Follow with multiple paragraphs — each starting with a **bolded subheading** (`<b>Subheading</b>:`) that naturally introduces the paragraph’s focus (e.g., features, materials, usability, benefits).
                                                  - Do **not** literally write the word “Title.”
                                                  - Subheadings should be relevant and descriptive (e.g., `<b>Durable and Reliable</b>:`).
                                                - Include a **"Specifications:"** section using `<ul>` or `<ol>` tags.
                                                  - Each `<li>` should describe one clear feature, or benefit.
                                                  - Write general features or benefits as natural sentences.
                                                - End with a short closing paragraph summarizing why the product is valuable or essential.
                                                - Keep tone natural, trustworthy, and marketing-friendly — avoid exaggeration or over-promotion.

                        IMPORTANT:
                                    - Process any valid physical or product-based item (e.g., groceries, fashion, electronics, furniture, beauty, tools, baby items, pet supplies, etc.).
                                    - If the input is **not** a valid physical product (e.g., digital service, app, subscription, software, or meaningless/unusable text), respond with exactly `"INVALID_INPUT"`.
                                    - Based on "{$contextSafe}", generate an **eye-catching and relevant headline** at the top of the HTML.
                                    - Do **not** include prices, promotional slogans, or exaggerated claims unless explicitly part of the product context.
                                    - Output must be pure HTML for use in a rich text editor.
                                    - Do NOT include <html>, <head>, <body>, <title> tags.
                                    - Do NOT include Markdown code fences (```, ```html) or any other wrapper.
                                    - The response must **start with `<` and end with `>`** — any other format will be rejected.
                PROMPT;
    }

    public function getType(): string
    {
        return 'product_description';
    }
}
