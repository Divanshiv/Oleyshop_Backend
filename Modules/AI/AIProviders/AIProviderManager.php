<?php

namespace Modules\AI\AIProviders;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Modules\AI\app\Exceptions\AIProviderException;
use Modules\AI\app\Exceptions\ImageValidationException;
use Modules\AI\app\Exceptions\UsageLimitException;
use Modules\AI\app\Exceptions\ValidationException;
use Modules\AI\app\Models\AISetting;
use Modules\AI\app\Services\AIResponseValidatorService;
use Modules\AI\app\Traits\AIModuleManager;

class AIProviderManager
{
    use AIModuleManager;
    protected array $providers;

    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * @throws AIProviderException
     */
    public function getAvailableProviderObject()
    {
        $activeAiProvider = $this->getActiveAIProvider();
        foreach ($this->providers as $provider) {
            if ($activeAiProvider->ai_name === $provider->getName()) {
                $provider->setApiKey($activeAiProvider->api_key);
                $provider->setOrganization($activeAiProvider->organization_id);
                return $provider;
            }
        }

        throw new AIProviderException('No matching AI provider found.');
    }

    public function getActiveAIProvider(): AISetting
    {
        $provider = $this->getActiveAIProviderConfig();
        if (!$provider) {
            throw new AIProviderException('No active AI provider available at this moment.');
        }
        return $provider;
    }

    /**
     * @throws ImageValidationException
     * @throws AIProviderException
     * @throws ValidationException
     * @throws UsageLimitException
     */
    public function generate(string $prompt, ?string $imageUrl = null, array $options = []): string
    {
        $providerObject = $this->getAvailableProviderObject();
        $aiValidator = new AIResponseValidatorService();
        $appMode = env('APP_MODE');
        $section = $options['section'] ?? '';

        if ($appMode === 'demo') {
            $ip = request()->header('x-forwarded-for');
            $cacheKey = 'demo_ip_usage_' . $ip;
            $count = Cache::get($cacheKey, 0);
            if ($count >= 10) {
                throw new ValidationException("Demo limit reached: You can only generate 10 times.");
            }
            Cache::forever($cacheKey, $count + 1);
        }
        $response = $providerObject->generate($prompt, $imageUrl);

        $validatorMap = [
            'product_name' => 'validateProductTitle',
            'product_description' => 'validateProductDescription',
            'generate_product_title_suggestion' => 'validateProductKeyword',
            'pricing_and_others' => 'validateProductPricingAndOthers',
            'generate_title_from_image' => 'validateImageResponse',
            'category_setup' => 'validateProductCategorySetup',
            'variation_tag_setup' => 'validateProductVariationTagSetup'

        ];

        if ($section && isset($validatorMap[$section])) {
            $aiValidator->{$validatorMap[$section]}($response, $options['context'] ?? null);
        }

        return $response;
    }

}
