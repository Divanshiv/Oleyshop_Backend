<?php

namespace Modules\AI\app\PromptTemplates;

use App\CentralLogics\Helpers;
use Modules\AI\app\Contracts\PromptTemplateInterface;
use Modules\AI\app\Services\ProductResourceService;

class CategorySetupTemplates implements PromptTemplateInterface
{
    protected ProductResourceService $productResource;
    public function __construct()
    {
        $this->productResource = new ProductResourceService();
    }

    public function build(?string $context = null, ?string $langCode = null, ?string $description = null): string
    {
        $resource = $this->productResource->productCategorySetupData();

        $categories = array_keys($resource['categories']);
        $subCategories = array_keys($resource['sub_categories']);
        $units = $resource['units'];

        $categoriesList = implode("\n- ", $categories);
        $subCategoriesList = implode("\n- ", $subCategories);
        $unitsList = implode("\n- ", $units);
        $contextSafe = addslashes($context ?? '');
        $descriptionSafe = addslashes($description ?? '');

        return <<<PROMPT
                GroFresh - Specialized in All eCommerce Niches
                GroFresh is the ultimate single-vendor eCommerce solution (with multi-branch support) designed to take your grocery, pharmacy, fashion, electronics, or any other product-based business online — developed with all product-based eCommerce industries in mind.

                You are a professional eCommerce copywriter trained to analyze products and generate structured eCommerce metadata.


                Analyze the following product:
                - Name: "{$contextSafe}"
                - Description: "{$descriptionSafe}"

                Generate a valid **JSON object** starting with `{` and ending with `}` containing the following keys:  `category_name`, `sub_category_name`, `unit_name`, `quantity`, `maximum_order_quantity`, `weight`.

                CRITICAL INSTRUCTIONS:
                                        - Select the best matching `category_name` from "{$categoriesList}.
                                        - Select the best matching `sub_category_name` from "{$subCategoriesList}. Omit this key if no suitable match is found.
                                        - Select the best matching `unit_name` from "{$unitsList}.
                                        - If multiple matches are possible, pick the most specific or accurate.
                                        - All numeric fields (`quantity`, `maximum_order_quantity`, `weight`) must be plain numbers, not strings.
                                        - For weight, if weight-related information exists in the product description, determine the largest valid weight or volume mentioned and use that as the value.
                                          - Example: if the description mentions “500 ml” and “1 ltr”, the weight should be 1 (representing the maximum, i.e., 1 ltr).
                                          - if no weight information exists, set it to 0 or omit the field if contextually irrelevant.
                                        - For `quantity` and `maximum_order_quantity`, pick a random positive number greater than 0.
                                        - For `weight`, pick a random positive number from 0 to 5.
                                        - Ensure the JSON is directly usable by `json_decode()` in PHP without modification.
                                        - Output must be a single valid JSON object without any extra characters, comments, or formatting.


                IMPORTANT:
                            - **DO NOT** include markdown syntax, code fences, language tags, triple backticks, explanations, or commentary.
                            - If the product is irrelevant or meaningless, respond with exactly `INVALID_INPUT`.
                PROMPT;
    }



    public function getType(): string
    {
        return 'category_setup';
    }
}
