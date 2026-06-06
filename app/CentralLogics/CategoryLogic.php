<?php

namespace App\CentralLogics;

use App\Model\Category;
use App\Model\Product;
use Carbon\Carbon;

class CategoryLogic
{
    public static function parents()
    {
        return Category::where('position', 0)->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    public static function products($categoryId, $request)
    {
        $limit    = $request['limit'] ?? 10;
        $offset   = $request['offset'] ?? 1;
        $sortBy   = $request['sort_by'] ?? 'latest';
        $rating   = $request['rating'] ?? null;
        $minPrice = $request['min_price'] ?? null;
        $maxPrice = $request['max_price'] ?? null;


        $query = Product::active()
            ->withCount(['wishlist', 'active_reviews'])
            ->with('rating')
            ->whereJsonContains('category_ids', ['id' => (string) $categoryId]);

        $query->when($rating !== null, function ($q) use ($rating) {
            $q->whereHas('reviews', function ($r) use ($rating) {
                $r->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        });

        $query
            ->when($minPrice !== null, fn ($q) => $q->where('price', '>=', $minPrice))
            ->when($maxPrice !== null, fn ($q) => $q->where('price', '<=', $maxPrice));

        switch ($sortBy) {
            case 'low_to_high':
                $query->orderBy('price', 'ASC');
                break;

            case 'high_to_low':
                $query->orderBy('price', 'DESC');
                break;

            case 'ascending':
                $query->orderBy('name', 'ASC');
                break;

            case 'descending':
                $query->orderBy('name', 'DESC');
                break;

            default:
                $query->latest();
        }

        $priceQuery = clone $query;

        $paginator = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit'      => $limit,
            'offset'     => $offset,
            'min_price'  => $minPrice,
            'max_price'  => $maxPrice,
            'rating'     => $rating,
            'sort_by'    => $sortBy == 'latest' ? null : $sortBy,
            'products'   => $paginator->items(),
        ];
    }

    public static function all_products($id)
    {
        $categoryIds = [];
        $categoryIds[] = (int)$id;
        foreach (CategoryLogic::child($id) as $child){
            $categoryIds[] = $child['id'];
            foreach (CategoryLogic::child($child['id']) as $ch2){
                $categoryIds[] = $ch2['id'];
            }
        }

        $products = Product::active()->with('rating', 'active_reviews')->get();
        $productIds = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if (in_array($category['id'],$categoryIds)) {
                    $productIds[] = $product['id'];
                }
            }
        }

        return Product::active()->withCount(['wishlist'])->with('rating', 'active_reviews')->whereIn('id', $productIds)->get();
    }
}
