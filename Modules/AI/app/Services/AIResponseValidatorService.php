<?php

namespace Modules\AI\app\Services;

use Illuminate\Support\Facades\Log;
use Modules\AI\app\Exceptions\ImageValidationException;
use Modules\AI\app\Exceptions\ValidationException;

class AIResponseValidatorService
{
    /**
     * @throws ValidationException
     */
    public function validateProductTitle(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating a product title. Please provide a meaningful product name.');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateProductDescription(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating a product description. Please provide a meaningful name or description.');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateProductGeneralSetup(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for general setup. Please provide meaningful data.');
        }
    }

    public function validateProductCategorySetup(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for category setup. Please provide meaningful data.');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateProductPricingAndOthers(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating pricing setup. Please provide a meaningful name or description.');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateProductVariationSetup(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating a product description. Please provide a meaningful name or description.');
        }
    }

    public function validateProductVariationTagSetup(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductTitle($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating product variation and tag setup. Please provide a meaningful name or description.');
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateProductKeyword(string $response, ?string $context = null): void
    {
        if ($this->isInvalidProductKeyword($response, $context)) {
            throw new ValidationException('The provided input is not valid for generating a product title. Please provide a meaningful keyword.');
        }
    }

    /**
     * @throws ImageValidationException
     */
    public function validateImageResponse(string $response): void
    {
        if ($this->isInvalidImageResponse($response)) {
            throw new ImageValidationException('The uploaded image is not valid for generating product content. Please provide a meaningful image.');
        }
    }

    private function isInvalidProductTitle(string $response, ?string $context = null): bool
    {
        return $this->phraseCheck($response, $context);
    }

    private function isInvalidProductDescription(string $response, ?string $context = null): bool
    {
        return $this->phraseCheck($response, $context);
    }

    private function isInvalidProductKeyword(string $response, ?string $context = null): bool
    {
        return $this->phraseCheck($response, $context);
    }

    private function isInvalidImageResponse(string $response): bool
    {
        return $this->phraseCheck($response, null);
    }

    public function phraseCheck(string $response, ?string $context): bool
    {
        $invalidPhrases = [
            'INVALID_INPUT',
        ];
        foreach ($invalidPhrases as $phrase) {
            if (stripos($response, $phrase) !== false) {
                Log::warning('Invalid phrase detected', ['phrase' => $phrase, 'response' => $response, 'context' => $context]);
                return true;
            }
        }
        return false;
    }
}
