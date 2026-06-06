<?php

namespace Modules\AI\app\PromptTemplates;

use App\CentralLogics\Helpers;
use Modules\AI\app\Contracts\PromptTemplateInterface;

class PricingTemplate implements  PromptTemplateInterface
{
    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $currencySymbol = Helpers::currency_symbol();
        $contextSafe = addslashes($context ?? '');
        $descriptionSafe = addslashes($description ?? '');

        return <<<PROMPT
               GroFresh - Specialized in All eCommerce Niches
                GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                You are You are an expert pricing analyst trained to analyze products and generate structured eCommerce metadata.


                Analyze the following product:
                - Name: "{$contextSafe}"
                - Description: "{$descriptionSafe}"


              Generate a valid **JSON object** starting with `{` and ending with `}` containing the following keys:  `unit_price`, `current_stock`, `discount_type`, `discount_amount`, `tax_type`, `tax`.

              CRITICAL INSTRUCTIONS:
                                        - All numeric fields (`unit_price`, `current_stock`, `discount_amount`, `tax`) must be plain numbers, not strings, and must follow the "{$currencySymbol}" context where relevant.
                                        - `discount_type` must be either `"flat"` or `"percent"`.
                                        - `tax_type` must be either `"percent"` or `"amount"`.
                                        - `current_stock` must be a positive integer greater than 0.
                                        - `unit_price`, `discount_amount`, and `tax` must be realistic based on the product description.
                                        - Ensure the JSON is directly usable by `json_decode()` in PHP without any modification.
                                        - Output must be a single, clean JSON object without any extra characters, comments, formatting, or explanations.

              IMPORTANT:
                            - **DO NOT** include markdown syntax, code fences, language tags, triple backticks, explanations, or commentary.
                            - If the product is irrelevant or meaningless, respond with exactly `INVALID_INPUT`.
              PROMPT;
    }

    public function getType(): string
    {
        return 'pricing_and_others';
    }
}
