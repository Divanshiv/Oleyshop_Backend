<?php

namespace Modules\AI\app\Response;

use App\Model\Category;
use Modules\AI\app\Exceptions\ValidationException;
use Modules\AI\app\Services\ProductResourceService;

class ProductResponse
{
//    use VatTaxManagement;
    protected Category $category;
    protected ProductResourceService $productResource;

    public function __construct()
    {
        $this->productResource = new ProductResourceService();
        $this->category = new Category();
    }

    public function productCategorySetupAutoFillFormat(string $result): array
    {
        $result = trim($result);
        $result = preg_replace('/^```(?:json)?\s*/i', '', $result);
        $result = preg_replace('/```$/', '', $result);
        $result = trim($result);
        $resource = $this->productResource->productCategorySetupData();
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        if (empty($data['category_name']) || !is_string($data['category_name'])) {
            throw new \InvalidArgumentException('The "category_name" field is required and must be a non-empty string.');
        }
        if (empty($data['unit_name']) || !is_string($data['unit_name'])) {
            throw new \InvalidArgumentException('The "unit_name" field is required and must be a non-empty string.');
        }

        $processedData = $this->productGeneralSetConvertNamesToIds($data, $resource);
        if (!$processedData['success']) {
            return $processedData;
        }

        $data = $processedData['data'];

        if (!array_key_exists('sub_category_name', $data)) {
            $data['sub_category_name'] = null;
        }


        return $data;
    }

    public function productPriceAndOthersAutoFill($result): array|\Illuminate\Http\JsonResponse
    {
        $result = trim($result);
        $result = preg_replace('/^```(?:json)?\s*/i', '', $result);
        $result = preg_replace('/```$/', '', $result);
        $result = trim($result);
        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }
        $fields = [
            'unit_price',
            'current_stock',
            'discount_type',
            'discount_amount',
            'tax_type',
            'tax'
        ];

        $errors = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $errors[$field] = "$field is required.";
            }
        }

        if (!empty($errors)) {
            return response()->json(
                $this->formatAIGenerationValidationErrors($errors),
                422
            );
        }
        $data['unit_price'] = round($data['unit_price']);
        return $data;
    }

    private function formatAIGenerationValidationErrors(array $errors): string
    {
        $messages = [];

        foreach ($errors as $field => $message) {
            $messages[] = $message;
        }

        return 'AI couldn’t generate product ' . implode(' ', $messages);
    }

    public function variationSetupAutoFill(string $result)
    {
        $result = trim($result);
        $result = preg_replace('/^```(?:json)?\s*/i', '', $result);
        $result = preg_replace('/```$/', '', $result);
        $result = trim($result);
        $data = json_decode($result, true);
        $errors = [];

        if(isset($data['generate_variation']) && is_array($data['generate_variation'])) {
            foreach ($data['generate_variation'] as &$variation) {
                $variation['price'] = $variation['price'] ?? 0;
            }
        }
        $response = [
            'data' => $data,
        ];

        if (!empty($errors)) {
            throw new ValidationException($this->formatAIGenerationValidationErrors($errors));
        }

        $response['status'] = 'success';
        return $response;
    }

    public function generateTitleSuggestions(string $result)
    {
        return json_decode($result, true);

    }

    public function productGeneralSetConvertNamesToIds(array $data, array $resources): array
    {
        if (isset($data['category_name'])) {
            $categoryName = strtolower(trim($data['category_name']));
            if (isset($resources['categories'][$categoryName])) {
                $data['category_id'] = $resources['categories'][$categoryName];
            } else {
                $errors[] = "Invalid category name: {$data['category_name']}";
            }
        }

        if (isset($data['sub_category_name'])) {
            $subCategoryName = strtolower(trim($data['sub_category_name']));
            if (isset($resources['sub_categories'][$subCategoryName])) {
                $data['sub_category_id'] =  $this->category->where(['parent_id' => $data['category_id'], 'name' => $subCategoryName])->first()?->id ?? 0;
            }
        }

        if (!empty($errors)) {
            throw new \RuntimeException($this->formatAIGenerationValidationErrors($errors));
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }
}
