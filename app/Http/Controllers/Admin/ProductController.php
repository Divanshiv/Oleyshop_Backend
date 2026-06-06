<?php

namespace App\Http\Controllers\Admin;

use App\Model\Tag;
use App\Model\Review;
use App\Model\Product;
use App\Model\Category;
use App\Model\Translation;
use App\Traits\UploadSizeHelper;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Model\BusinessSetting;
use App\Model\FlashDealProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Modules\AI\app\Models\AISetting;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Box\Spout\Common\Exception\IOException;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;


class ProductController extends Controller
{
    use UploadSizeHelper;
    public function __construct(
        private BusinessSetting $business_setting,
        private Category $category,
        private Product $product,
        private Review $review,
        private Tag $tag,
        private Translation $translation
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function variantCombination(Request $request): JsonResponse
    {
        $options = [];
        $price = $request->price;
        $productVariations = [];
        if ($request->filled('product_id'))
        {
            $productVariations = json_decode(Product::firstWhere('id', $request->product_id)?->variations ?? '[]', true);
        }
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;

        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'productVariations'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $request->parent_id])->get();
        $result = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($categories as $row) {
            if ($row->id == $request->sub_category) {
                $result .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $result .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $result,
        ]);
    }

    /**
     * @return Factory|View|Application
     */
    public function index(): View|Factory|Application
    {
        $categories = $this->category->where(['position' => 0])->pluck('name', 'id');
        $aIStatus = AISetting::first()?->status;

        return view('admin-views.product.index', compact('categories', 'aIStatus'));
    }

    /**
     * Summary of list
     * @param Request $request
     * @return View
     */
    public function list(Request $request)
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());

        $queryParam = ['per_page' => $perPage];

        $search = $request->query('search');
        $queryParam['search'] = $search;

        $products = $this->product->with('order_details.order')
            ->when($search, function ($q) use ($search) {
                $key = explode(' ', $search);

                $q->where(function ($sub) use ($key) {
                    foreach ($key as $value) {
                        $sub->orWhere('id', 'like', "%{$value}%")
                            ->orWhere('name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()
            ->paginate($perPage)
            ->appends($queryParam);

        foreach ($products as $product) {
            $totalSold = 0;

            foreach ($product->order_details as $detail) {
                if ($detail?->order?->order_status == 'delivered') {
                    $totalSold += $detail->quantity;
                }
            }

            $product->total_sold = $totalSold;
        }

        return view('admin-views.product.list', compact('products', 'search', 'perPage'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function view($id): View|Factory|RedirectResponse|Application
    {
        $product = $this->product->where(['id' => $id])->first();

        if (!$product) {
            Toastr::error(translate('product not found'));
            return back();
        }

        $reviews = $this->review->where(['product_id' => $id])->latest()->paginate(20);
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images'], 'image');
        if ($check !== true) {
            return $check;
        }
        $price = $request->input('price');
        $discountType = $request->input('discount_type');
        $discount = $request->input('discount') ?? 0;

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'images'   => 'required|array|min:1',
            'images.*' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'price' => 'required|numeric|min:0',
            'total_stock' => 'required|numeric|min:1',
        ], [
            'name.0.required' => 'Product name is required',
            'name.0.unique' => 'Product name has already been taken',
            'category_id.required' => 'Category  is required',
            'images.required' => 'Product image is required',
            'images.*.image' => 'The image must be an image',
            'images.*.max' => 'Each image must not exceed 5 MB',
            'images.*.mimes' => 'The image must be a file of type: jpg, jpeg, png',
            'price.required' => 'Product price is required',
            'price.min' => 'Product price must be greater than or equal to 0',
            'total_stock.required' => 'Product stock is required',
            'total_stock.min' => 'Product stock must be greater than or equal to 1',
        ], [
            'images.*' => 'image'
        ]);

        if ($discountType == 'percent') {
            $discountAmount = ($price / 100) * $discount;
        } else {
            $discountAmount = $discount;
        }

        if ($discountType === 'percent' && $discount > 100) {
            $validator->getMessageBag()->add('discount', 'Discount percentage cannot exceed 100%');
        }

        if ($price != 0 && $price < $discountAmount) {
            $validator->getMessageBag()->add('discount', 'Discount cannot be more than the price');
        }

        $imageNames = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $imageNames[] = $imageData;
            }
            $imageData = json_encode($imageNames);
        } else {
            $imageData = json_encode([]);
        }

        $tagIds = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tagIds[] = $tag->id;
            }
        }

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }
        $combinations = Helpers::combinations($options);

        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount']) {
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant ' . $item['type'] . ' price or change discount amount!');
                }

                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (int)$request['total_stock'];
        }

        if ((int)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product = $this->product;
        $product->name = $request->name[array_search('en', $request->lang)];
        $product->category_ids = json_encode($category);
        $product->description = $request->description[array_search('en', $request->lang)];
        $product->choice_options = json_encode($choiceOptions);
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->image = $imageData;
        $product->capacity = $request->capacity;
        $product->maximum_order_quantity = $request->maximum_order_quantity;
        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;
        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->status = $request->status ? $request->status : 0;
        $product->weight = $request->weight ?? 0;
        $product->save();

        $product->tags()->sync($tagIds);

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $data[] = array(
                    'translationable_type' => 'App\Model\Product',
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                );
            }
        }

        $this->translation->insert($data);

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $product = $this->product->withoutGlobalScopes()->with('translations')->find($id);
        $productCategory = json_decode($product->category_ids);
        $categories = $this->category->where(['parent_id' => 0])->get();
        $aIStatus = AISetting::first()?->status;
        return view('admin-views.product.edit', compact('product', 'productCategory', 'categories', 'aIStatus'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images'], 'image');
        if ($check !== true) {
            return $check;
        }
        $product = $this->product->find($id);
        $price = $request->input('price');
        $discountType = $request->input('discount_type');
        $discount = $request->input('discount') ?? 0;

        $validator = Validator::make($request->all(), [
            'name.0' => 'required|unique:products,name,' . $id,
            'images'   => 'nullable|array|min:1',
            'images.*' => 'image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'price' => 'required|numeric|min:0',
            'total_stock' => 'required|numeric|min:1',
        ], [
            'name.0.required' => 'Product name is required',
            'name.0.unique' => 'Product name has already been taken',
            'images.required' => 'Product image is required',
            'images.*.image' => 'The image must be an image',
            'images.*.max' => 'Each image must not exceed 5 MB',
            'images.*.mimes' => 'Only JPG, JPEG, and PNG images are allowed',
            'price.required' => 'Product price is required',
            'price.min' => 'Product price must be greater than or equal to 0',
            'total_stock.required' => 'Product stock is required',
            'total_stock.min' => 'Product stock must be greater than or equal to 1',
        ], [
            'images.*' => 'image'
        ]);

        if ($discountType == 'percent') {
            $discountAmount = ($price / 100) * $discount;
        } else {
            $discountAmount = $discount;
        }

        if ($discountType === 'percent' && $discount > 100) {
            $validator->getMessageBag()->add('discount', 'Discount percentage cannot exceed 100%');
        }

        if ($price != 0 && $price < $discountAmount) {
            $validator->getMessageBag()->add('discount', 'Discount cannot be more than the price');
        }

        $tagIds = [];
        if ($request->tags != null) {
            $tags = explode(",", $request->tags);
        }
        if (isset($tags)) {
            foreach ($tags as $key => $value) {
                $tag = $this->tag->firstOrNew(
                    ['tag' => $value]
                );
                $tag->save();
                $tagIds[] = $tag->id;
            }
        }

        $images = json_decode($product->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $images[] = $imageData;
            }
        }

        if (is_null($images)) {
            $validator->getMessageBag()->add('images', 'Product image can not be empty');
        }

        $product->name = $request->name[array_search('en', $request->lang)];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $combinations = Helpers::combinations($options);
        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount']) {
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant ' . $item['type'] . ' price or change discount amount!');
                }

                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (int)$request['total_stock'];
        }

        if ((int)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product->category_ids = json_encode($category);
        $product->description = $request->description[array_search('en', $request->lang)];
        $product->choice_options = json_encode($choiceOptions);
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->capacity = $request->capacity;
        $product->unit = $request->unit;
        $product->maximum_order_quantity = $request->maximum_order_quantity;
        $product->image = json_encode($images);
        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;
        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->status = $request->status ? $request->status : 0;
        $product->weight = $request->weight;
        $product->save();

        $product->tags()->sync($tagIds);

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    [
                        'translationable_type'  => 'App\Model\Product',
                        'translationable_id'    => $product->id,
                        'locale'                => $key,
                        'key'                   => 'name'
                    ],
                    ['value'                 => $request->name[$index]]
                );
            }
            if ($request->description[$index] && $key != 'en') {
                $this->translation->updateOrInsert(
                    [
                        'translationable_type'  => 'App\Model\Product',
                        'translationable_id'    => $product->id,
                        'locale'                => $key,
                        'key'                   => 'description'
                    ],
                    ['value'                 => $request->description[$index]]
                );
            }
        }
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();

        Toastr::success(translate('Product status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function feature(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->is_featured = $request->is_featured;
        $product->save();
        Toastr::success(translate('product feature status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function dailyNeeds(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);
        $product->daily_needs = $request->status;
        $product->save();
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);

        if (!is_null($product['image'])) {
            $images = json_decode($product['image'], true);

            if (is_array($images)) {
                foreach ($images as $img) {
                    if (Storage::disk('public')->exists('product/' . $img)) {
                        Storage::disk('public')->delete('product/' . $img);
                    }
                }
            }
        }

        $flashDealProducts = FlashDealProduct::where('product_id', $product->id)->get();
        foreach ($flashDealProducts as $flashDealProduct) {
            $flashDealProduct->delete();
        }
        $product->delete();
        Toastr::success(translate('Product removed!'));
        return back();
    }

    /**
     * @param mixed $id
     * @param mixed $index
     * @return RedirectResponse
     */
    public function removeImage($id, $index): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $images = json_decode($product->image, true) ?? [];

        if (isset($images[$index])) {
            $file = $images[$index];

            Storage::disk('public')->delete('product/' . $file);

            unset($images[$index]);

            $product->image = json_encode(array_values($images));
            $product->save();
        }

        Toastr::success(translate('Image removed successfully!'));
        return back();
    }

    /**
     * @return Factory|View|Application
     */
    public function bulkImportIndex(): View|Factory|Application
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkImportProduct(Request $request): RedirectResponse
    {
        $this->initUploadLimits('file');
        $check = $this->validateUploadedFile($request, ['products_file'], 'file');
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'products_file' => 'required|mimes:xlsx|max:' . $this->maxImageSizeKB
        ],
            [
                'products_file.required' => 'The Product file field is empty',
                'products_file.mimes' => 'File type must be xlsx',
                'products_file.max' => 'File size must be less than ' . $this->maxImageSizeReadable
            ]
        );

        try {
            $collections = (new FastExcel)->import($request->file('products_file'));

            if ($collections->isEmpty()) {
                Toastr::error(translate('No product data found in the file.'));
                return back();
            }

        } catch (\Exception $exception) {
            Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }

        $columnKey = ['name', 'description', 'price', 'tax', 'category_id', 'sub_category_id', 'discount', 'discount_type', 'tax_type', 'unit', 'total_stock', 'capacity', 'daily_needs'];
        foreach ($collections as $collectionKey => $collection) {
            foreach ($collection as $key => $value) {
                if ($key != "" && !in_array($key, $columnKey)) {
                    Toastr::error('Please upload the correct format file.');
                    return back();
                }
            }
        }

        $existingNames = DB::table('products')
            ->pluck('name')
            ->map(fn ($name) => strtolower(trim($name)))
            ->flip()
            ->toArray();

        $seenNames = [];
        $data = [];
        $skipped = 0;

        foreach ($collections as $collection) {

            $name = trim($collection['name'] ?? '');

            // Empty name
            if ($name === '') {
                $skipped++;
                continue;
            }

            $normalizedName = strtolower($name);

            // Duplicate in Excel
            if (isset($seenNames[$normalizedName])) {
                $skipped++;
                continue;
            }

            // Already exists in DB
            if (isset($existingNames[$normalizedName])) {
                $skipped++;
                continue;
            }

            $seenNames[$normalizedName] = true;

            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => null,
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 0], ['id' => (string)$collection['sub_category_id'], 'position' => 1]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'unit' => $collection['unit'],
                'total_stock' => $collection['total_stock'],
                'capacity' => $collection['capacity'],
                'daily_needs' => $collection['daily_needs'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if (empty($data)) {
            Toastr::error(translate('No valid products found to import.'));
            return back();
        }

        DB::table('products')->insert($data);

        Toastr::success(count($data) . (translate(' - Products imported successfully!')));
        return back();
    }

    /**
     * @return Factory|View|Application
     */
    public function bulkExportIndex(): View|Factory|Application
    {
        return view('admin-views.product.bulk-export-index');
    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulkExportProduct(Request $request): StreamedResponse|string
    {
        $search = $request->search;

        $startDate = $request->type == 'date_wise' ? $request['start_date'] : null;
        $endDate = $request->type == 'date_wise' ? $request['end_date'] : null;

        $products = $this->product
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->when((!is_null($search)), function ($query) use ($search) {
                $searchKeys = explode(' ', $search);
                foreach ($searchKeys as $value) {
                    return $query->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })
            ->get();

        $storage = [];
        foreach ($products as $item) {
            $categoryId = 0;
            $subCategoryId = 0;

            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $categoryId = $category['id'];
                } else if ($category['position'] == 2) {
                    $subCategoryId = $category['id'];
                }
            }

            if (!isset($item['description'])) {
                $item['description'] = 'No description available';
            }

            if (!isset($item['capacity'])) {
                $item['capacity'] = 0;
            }

            $storage[] = [
                'name' => $item['name'],
                'description' => $item['description'],
                'price' => $item['price'],
                'tax' => $item['tax'],
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'discount' => $item['discount'],
                'discount_type' => $item['discount_type'],
                'tax_type' => $item['tax_type'],
                'unit' => $item['unit'],
                'total_stock' => $item['total_stock'],
                'capacity' => $item['capacity'],
                'daily_needs' => $item['daily_needs'],
            ];
        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function limitedStock(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());

        $queryParam = ['per_page' => $perPage];
        $stockLimit = $this->business_setting->where('key', 'minimum_stock_limit')->first()->value;
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->where('total_stock', '<', $stockLimit)->latest();

            $queryParam['search'] = $request->search;;
        } else {
            $query = $this->product->where('total_stock', '<', $stockLimit)->latest();
        }

        $products = $query->paginate($perPage)
            ->appends($queryParam);

        return view('admin-views.product.limited-stock', compact('products', 'search', 'stockLimit', 'perPage'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getVariations(Request $request): JsonResponse
    {
        $product = $this->product->find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateQuantity(Request $request): RedirectResponse
    {
        $variations = [];
        $stockCount = $request['total_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = (abs($request['price_' . str_replace('.', '_', $str)]));
                $item['stock'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
            }
        }

        $product = $this->product->find($request['product_id']);

        if ($stockCount >= 0) {
            $product->total_stock = $stockCount;
            $product->variations = json_encode($variations);
            $product->save();
            Toastr::success(translate('product_quantity_updated_successfully!'));
        } else {
            Toastr::warning(translate('product_quantity_can_not_be_less_than_0_!'));
        }
        return back();
    }
}
