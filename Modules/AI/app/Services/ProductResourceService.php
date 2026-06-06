<?php

namespace Modules\AI\app\Services;

use App\Model\Category;
use App\Model\Attribute;

class ProductResourceService
{
protected Category $category;
protected Attribute $attribute;

    public function __construct()
    {
        $this->category = new Category();
        $this->attribute = new Attribute();
    }

    private function getCategoryEntityData($position = 0)
    {
        return $this->category
            ->where(['position' => $position])
            ->get(['id', 'name'])
            ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
            ->toArray();
    }

    public function productGeneralSetupData(): array
    {
        $data = [
            'categories' => $this->getCategoryEntityData(0),
            'sub_categories' => $this->getCategoryEntityData(1),
            'units' => $this->units(),
        ];
        return $data;
    }

    public function getVariationData(): array
    {
        $data = [
            'attributes' => $this->attribute
                ->get(['id', 'name'])
                ->mapWithKeys(fn($item) => [strtolower($item->name) => $item->id])
                ->toArray(),
        ];
        return $data;
    }
    public function units(): array
    {
        return ['kg', 'pc', 'gm', 'ltr', 'ml'];
    }

    public function productCategorySetupData(): array
    {
        $data = [
            'categories' => $this->getCategoryEntityData(0),
            'sub_categories' => $this->getCategoryEntityData(1),
            'units' => $this->units(),
        ];
        return $data;
    }
}
