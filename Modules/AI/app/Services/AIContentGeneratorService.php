<?php

namespace Modules\AI\app\Services;

use App\CentralLogics\Helpers;
use App\Traits\FileManagerTrait;
use Modules\AI\AIProviders\AIProviderManager;
use Modules\AI\AIProviders\ClaudeProvider;
use Modules\AI\AIProviders\OpenAIProvider;
use Modules\AI\app\Exceptions\AIProviderException;
use Modules\AI\app\Exceptions\ImageValidationException;
use Modules\AI\app\Exceptions\UsageLimitException;
use Modules\AI\app\Exceptions\ValidationException;
use Modules\AI\app\PromptTemplates\CategorySetupTemplates;
use Modules\AI\app\PromptTemplates\VariationTagSetupTemplates;

class AIContentGeneratorService
{

    protected array $templates = [];
    protected array $providers;

    public function __construct()
    {
        $this->loadTemplates();
        $this->providers = [new OpenAIProvider(), new ClaudeProvider()];
    }

    protected function loadTemplates(): void
    {
        $templateClasses = [
            'product_name' => \Modules\AI\app\PromptTemplates\ProductNameTemplate::class,
            'product_description' => \Modules\AI\app\PromptTemplates\ProductDescriptionTemplate::class,
            'pricing_and_others' => \Modules\AI\app\PromptTemplates\PricingTemplate::class,
            'generate_product_title_suggestion' => \Modules\AI\app\PromptTemplates\GenerateProductTitleSuggestionTemplate::class,
            'generate_title_from_image' => \Modules\AI\app\PromptTemplates\GenerateTitleFromImageTemplate::class,
            'category_setup' => CategorySetupTemplates::class,
            'variation_tag_setup' => VariationTagSetupTemplates::class,
        ];
        foreach ($templateClasses as $type => $class) {
            if (class_exists($class)) {
                $this->templates[$type] = new $class();
            }
        }
    }

    /**
     * @throws ImageValidationException
     * @throws AIProviderException
     * @throws ValidationException
     * @throws UsageLimitException
     */
    public function generateContent(string $contentType, mixed $context = null, string $langCode = 'en', ?string $description = null, ?string $imageUrl = null): string
    {
        $template = $this->templates[$contentType];
        $prompt = $template->build(context: $context, langCode: $langCode, description: $description);
        $providerManager = new AIProviderManager($this->providers);

        return $providerManager->generate(prompt: $prompt, imageUrl: $imageUrl, options: ['section' => $contentType, 'context' => $context, 'description' => $description]);
    }

    public function getAnalyizeImagePath($image): array
    {
        $extension = $image->getClientOriginalExtension();
        $imageName = Helpers::upload(dir: 'product/ai_product_image/', format: $extension, image: $image);
        return $this->aiProductImageFullPath($imageName);

    }

    public function aiProductImageFullPath($image_name): array
    {
        //local
        if (in_array(request()->ip(), ['127.0.0.1', '::1'])) {
            return [
                'imageName' => $image_name,
                'imageFullPath' => "https://rokbucket.rokomari.io/ProductNew20190903/260X372/Diploma_Instant_Full_Cream_Milk_Powder_1-Diploma-f18ce-440679.png",
            ];
        }
        // live
        return [
            'imageName' => $image_name,
            'imageFullPath' => asset(path: 'storage/app/public/product/ai_product_image/' . $image_name)
        ];
    }

    public function deleteAiImage($imageName): void
    {
        Helpers::delete('product/ai_product_image/' . $imageName);
    }

    public function getAvailableContentTypes(): array
    {
        return array_keys($this->templates);
    }
}
