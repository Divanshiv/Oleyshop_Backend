<?php

namespace Modules\AI\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\AI\app\Http\Requests\CategorySetupRequest;
use Modules\AI\app\Http\Requests\GenerateProductTitleSuggestionRequest;
use Modules\AI\app\Http\Requests\GenerateTitleFromImageRequest;
use Modules\AI\app\Http\Requests\ProductDescriptionAutoFillRequest;
use Modules\AI\app\Http\Requests\ProductPricingRequest;
use Modules\AI\app\Http\Requests\ProductTitleAutoFillRequest;
use Modules\AI\app\Http\Requests\ProductVariationSetupAutoFillRequest;
use Modules\AI\app\Response\ProductResponse;
use Modules\AI\app\Services\AIContentGeneratorService;

class AIProductController extends Controller
{

    protected AIContentGeneratorService $aiContentGeneratorService;
    protected ProductResponse $productResponse;

    public function __construct(AIContentGeneratorService $AIContentGeneratorService, ProductResponse $productResponse)
    {
        $this->aiContentGeneratorService = $AIContentGeneratorService;
        $this->productResponse = $productResponse;
    }

    public function titleAutoFill(ProductTitleAutoFillRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "product_name", context: $request['name'], langCode: $request['langCode']);
            return $this->successResponse(data: $result, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }
    }

    public function descriptionAutoFill(ProductDescriptionAutoFillRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "product_description", context: $request['name'], langCode: $request['langCode']);
            $cleanHtml = preg_replace('/<\/?(html|head|body|article)[^>]*>/', '', $result);
            $cleanHtml = preg_replace('/^```(?:html)?|```$/m', '', $cleanHtml);
            $cleanHtml = trim($cleanHtml);
            return $this->successResponse(data: $cleanHtml, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }
    }

    public function categorySetupAutoFill(CategorySetupRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "category_setup", context: $request['name'], description: $request['description']);
            $data = $this->productResponse->productCategorySetupAutoFillFormat(result: $result);

            return $this->successResponse(data: $data, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }

    }

    public function pricingAndOthersAutoFill(ProductPricingRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "pricing_and_others", context: $request['name'], description: $request['description']);
            $data = $this->productResponse->productPriceAndOthersAutoFill(result: $result);
            return $this->successResponse(data: $data, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }

    }

    public function productVariationTagSetupAutoFill(ProductVariationSetupAutoFillRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "variation_tag_setup", context: $request['name'], description: $request['description']);
            $response = $this->productResponse->variationSetupAutoFill(result: $result);
            return $this->successResponse(data: $response['data'], status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }
    }

    public function generateProductTitleSuggestion(GenerateProductTitleSuggestionRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $result = $this->aiContentGeneratorService->generateContent(contentType: "generate_product_title_suggestion", context: $request['keywords'], description: $request['description']);
            $response = $this->productResponse->generateTitleSuggestions(result: $result);
            return $this->successResponse(data: $response, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }
    }

    public function generateTitleFromImages(GenerateTitleFromImageRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $imageFile = $request->file('image');
            $imagePath = $this->aiContentGeneratorService->getAnalyizeImagePath($imageFile);
            $result = $this->aiContentGeneratorService->generateContent(contentType: "generate_title_from_image", imageUrl: $imagePath['imageFullPath']);
            $this->aiContentGeneratorService->deleteAiImage($imagePath['imageName']);
            return $this->successResponse(data: $result, status: 200);
        } catch (\Exception $e) {
            $status = $e->getCode() > 0 ? $e->getCode() : 500;
            return $this->errorResponse(message: $e->getMessage(), status: $status);
        }
    }
}
