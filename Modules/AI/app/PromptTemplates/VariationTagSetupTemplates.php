<?php

namespace Modules\AI\app\PromptTemplates;

use App\CentralLogics\Helpers;
use Modules\AI\app\Contracts\PromptTemplateInterface;
use Modules\AI\app\Services\ProductResourceService;

class VariationTagSetupTemplates implements PromptTemplateInterface
{
    protected ProductResourceService $productResource;

    public function __construct()
    {
        $this->productResource = new ProductResourceService();
    }

    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $resource = $this->productResource->getVariationData();

        $attributes = collect($resource['attributes'])->map(fn($id, $name) => [
            'id' => (string)$id,
            'name' => $name,
        ])->values()->all();

        $attributesString = collect($attributes)
            ->map(fn($attr) => "{$attr['name']} (ID: {$attr['id']})")
            ->implode(', ');

        $contextSafe = addslashes($context ?? '');
        $descriptionSafe = addslashes($description ?? '');
        $currencySymbol = Helpers::currency_symbol();

        return <<<PROMPT
                GroFresh - Specialized in All eCommerce Niches
                GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                You are a professional eCommerce copywriter trained to analyze products and generate structured eCommerce metadata and variations.

                Analyze the following product:
                - Name: "{$contextSafe}"
                - Description: "{$descriptionSafe}"

                Generate a valid **JSON object** starting with `{` and ending with `}` containing the following keys:  `choice_attributes`, `search_tags`, `generate_variation`.


                                        -

                ### AVAILABLE ATTRIBUTES
                {$attributesString}
                Always use these **exact attribute names and IDs** — do not invent new ones.

                ---

                CRITICAL INSTRUCTIONS:
                                        - Identify which attributes are relevant.
                                        - Suggest realistic and concise variation options for each attribute.
                                        - Generate 2–5 meaningful search tags.
                                        - Create possible combinations under `"generate_variation"` for each unique combination of attribute values.
                                        - Each generated variation must include:
                                           - `"option"` → combined attribute values (e.g. `"Red-M"`, `"500ml"`, `"Chocolate-1L"`)
                                           - `"price"` → assign a realistic numeric value (e.g. 100, 250)
                                           - `"stock"` → assign a realistic integer (e.g. 50, 100)
                                        - Always output a **valid JSON object only** — no explanations, no markdown, no extra text.

                ### OUTPUT FORMAT (strict)
                {
                  "choice_attributes": [
                    {
                      "id": "attribute_id",
                      "name": "attribute_name",
                      "variation": ["option1", "option2"]
                    }
                  ],
                  "search_tags": ["keyword1", "keyword2"],
                  "generate_variation": [
                    {
                      "option": "attribute_combination",
                      "price": 0,
                      "stock": 0
                    }
                  ]
                }

                ---

                ### RULES
                    1. Use only the provided attributes from the list above when generating "choice_attributes". Pick all the possible attributes for choice_attributes.
                    2. If none of the listed attributes match this product, return:
                       "choice_attributes": [] and "generate_variation": [].
                    3. Never request clarification or output any text other than the JSON.
                    4. Always include "search_tags" — even if no variation exists.
                    5. All output must start with { and end with } — no text before or after.
                    6. Do not invent new attributes, but if a product clearly fits one of the listed ones, map it confidently.
                    7. "generate_variation" should contain one entry for each unique combination of variation options.
                    8. Ensure that each generated price in "generate_variation" is expressed in the currency "{$currencySymbol}".
                    9. If there are no choice attributes, return an empty array for both "choice_attributes" and "generate_variation".
                    10. Never output an error message, apology, or “cannot generate.” Simply return the correct JSON.

                ### GOOD EXAMPLES

                ✅ For product: “xyz”
                {
                  "choice_attributes": [
                    { "id": "5", "name": "Variety", "variation": ["long", "small"] }
                    { "id": "2", "name": "Weight", "variation": ["500ml", "1L", "2L"] },
                  ],
                  "search_tags": ["soybean", "oil", "cooking", "edible", "teer"],
                  "generate_variation": [
                    { "option": "500ml", "price": 120, "stock": 80 },
                    { "option": "1L", "price": 230, "stock": 60 },
                    { "option": "2L", "price": 430, "stock": 40 }
                  ]
                }

                ✅ For product: “abc”
                {
                  "choice_attributes": [
                    { "id": "8", "name": "Flavor", "variation": ["Apple", "Mango", "Orange"] },
                    { "id": "5", "name": "Volume", "variation": ["250ml", "500ml", "1L"] }
                  ],
                  "search_tags": ["juice", "drink", "fruit", "refreshing"],
                  "generate_variation": [
                    { "option": "Apple-250ml", "price": 80, "stock": 100 },
                    { "option": "Mango-500ml", "price": 120, "stock": 80 },
                    { "option": "Orange-1L", "price": 200, "stock": 50 }
                  ]
                }

                ✅ For product: “efg”
                {
                  "choice_attributes": [],
                  "search_tags": ["tomato", "vegetable", "fresh", "produce"],
                  "generate_variation": []
                }


                IMPORTANT:
                            - Follow these examples only. Do not return the same example for matched example product name. Respond **only** with the JSON.
                PROMPT;
    }


    public function getType(): string
    {
        return 'variation_tag_setup';
    }
}
