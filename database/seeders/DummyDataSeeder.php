<?php

namespace Database\Seeders;

use App\Model\AddOn;
use App\Model\AdminRole;
use App\Model\Attribute;
use App\Model\Banner;
use App\Model\Branch;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Model\Coupon;
use App\Model\Currency;
use App\Model\CustomerAddress;
use App\Model\DeliveryMan;
use App\Model\FlashDeal;
use App\Model\FlashDealProduct;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Review;
use App\Model\SocialMedia;
use App\Model\Tag;
use App\Model\TimeSlot;
use App\Model\Translation;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding dummy data...');

        // ──────────────────────────────────────────────
        // 1. ADMIN ROLES
        // ──────────────────────────────────────────────
        $this->seedAdminRoles();

        // ──────────────────────────────────────────────
        // 2. ATTRIBUTES
        // ──────────────────────────────────────────────
        $this->seedAttributes();

        // ──────────────────────────────────────────────
        // 3. TAGS
        // ──────────────────────────────────────────────
        $this->seedTags();

        // ──────────────────────────────────────────────
        // 4. TIME SLOTS
        // ──────────────────────────────────────────────
        $this->seedTimeSlots();

        // ──────────────────────────────────────────────
        // 5. BRANCHES
        // ──────────────────────────────────────────────
        $this->seedBranches();

        // ──────────────────────────────────────────────
        // 6. CURRENCIES
        // ──────────────────────────────────────────────
        $this->seedCurrencies();

        // ──────────────────────────────────────────────
        // 7. CATEGORIES
        // ──────────────────────────────────────────────
        $this->seedCategories();

        // ──────────────────────────────────────────────
        // 8. PRODUCTS
        // ──────────────────────────────────────────────
        $this->seedProducts();

        // ──────────────────────────────────────────────
        // 9. ADDONS
        // ──────────────────────────────────────────────
        $this->seedAddOns();

        // ──────────────────────────────────────────────
        // 10. COUPONS
        // ──────────────────────────────────────────────
        $this->seedCoupons();

        // ──────────────────────────────────────────────
        // 11. BANNERS
        // ──────────────────────────────────────────────
        $this->seedBanners();

        // ──────────────────────────────────────────────
        // 12. SOCIAL MEDIA
        // ──────────────────────────────────────────────
        $this->seedSocialMedia();

        // ──────────────────────────────────────────────
        // 13. CUSTOMERS (Users)
        // ──────────────────────────────────────────────
        $this->seedUsers();

        // ──────────────────────────────────────────────
        // 14. CUSTOMER ADDRESSES
        // ──────────────────────────────────────────────
        $this->seedCustomerAddresses();

        // ──────────────────────────────────────────────
        // 15. DELIVERY MEN
        // ──────────────────────────────────────────────
        $this->seedDeliveryMen();

        // ──────────────────────────────────────────────
        // 16. ORDERS & ORDER DETAILS
        // ──────────────────────────────────────────────
        $this->seedOrders();

        // ──────────────────────────────────────────────
        // 17. REVIEWS
        // ──────────────────────────────────────────────
        $this->seedReviews();

        // ──────────────────────────────────────────────
        // 18. FLASH DEALS
        // ──────────────────────────────────────────────
        $this->seedFlashDeals();

        // ──────────────────────────────────────────────
        // 19. TRANSLATIONS (sample)
        // ──────────────────────────────────────────────
        $this->seedTranslations();

        $this->command->info('Dummy data seeded successfully!');
    }

    // ─────────────────────────────────────────────────────
    // 1. ADMIN ROLES
    // ─────────────────────────────────────────────────────
    private function seedAdminRoles(): void
    {
        if (AdminRole::count() > 0) return;

        $modules = ['category', 'product', 'order', 'user', 'promotion', 'system', 'report', 'deliveryman'];

        AdminRole::create([
            'name' => 'Master Admin',
            'module_access' => json_encode($modules),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminRole::create([
            'name' => 'Manager',
            'module_access' => json_encode(['category', 'product', 'order', 'user']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AdminRole::create([
            'name' => 'Delivery Manager',
            'module_access' => json_encode(['order', 'deliveryman']),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ─────────────────────────────────────────────────────
    // 2. ATTRIBUTES
    // ─────────────────────────────────────────────────────
    private function seedAttributes(): void
    {
        if (Attribute::count() > 0) return;

        $names = ['Weight', 'Size', 'Color', 'Brand', 'Organic', 'Taste', 'Pack Size', 'Quality Grade'];
        foreach ($names as $i => $name) {
            Attribute::create([
                'name' => $name,
                'created_at' => now()->subDays(30 - $i),
                'updated_at' => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // 3. TAGS
    // ─────────────────────────────────────────────────────
    private function seedTags(): void
    {
        if (Tag::count() > 0) return;

        $tags = [
            ['tag' => 'organic', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'fresh', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'seasonal', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'imported', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'local', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'best-seller', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'discounted', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'gluten-free', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'vegan', 'created_at' => now(), 'updated_at' => now()],
            ['tag' => 'sugar-free', 'created_at' => now(), 'updated_at' => now()],
        ];
        Tag::insert($tags);
    }

    // ─────────────────────────────────────────────────────
    // 4. TIME SLOTS
    // ─────────────────────────────────────────────────────
    private function seedTimeSlots(): void
    {
        if (TimeSlot::count() > 0) return;

        $slots = [
            ['start_time' => '07:00:00', 'end_time' => '09:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '09:00:00', 'end_time' => '11:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '11:00:00', 'end_time' => '13:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '13:00:00', 'end_time' => '15:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '15:00:00', 'end_time' => '17:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '17:00:00', 'end_time' => '19:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ['start_time' => '19:00:00', 'end_time' => '21:00:00', 'status' => 1, 'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()],
        ];
        TimeSlot::insert($slots);
    }

    // ─────────────────────────────────────────────────────
    // 5. BRANCHES
    // ─────────────────────────────────────────────────────
    private function seedBranches(): void
    {
        if (Branch::count() > 1) return;

        $branches = [
            [
                'name' => 'Main Branch - Mumbai',
                'email' => 'mumbai@grofresh.com',
                'password' => bcrypt('12345678'),
                'latitude' => '19.0760',
                'longitude' => '72.8777',
                'address' => 'Andheri West, Mumbai - 400053',
                'status' => 1,
                'coverage' => 500,
                'phone' => '+91-9876543210',
                'remember_token' => Str::random(10),
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delhi Branch',
                'email' => 'delhi@grofresh.com',
                'password' => bcrypt('12345678'),
                'latitude' => '28.7041',
                'longitude' => '77.1025',
                'address' => 'Connaught Place, New Delhi - 110001',
                'status' => 1,
                'coverage' => 400,
                'phone' => '+91-9876543211',
                'remember_token' => Str::random(10),
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bangalore Branch',
                'email' => 'bangalore@grofresh.com',
                'password' => bcrypt('12345678'),
                'latitude' => '12.9716',
                'longitude' => '77.5946',
                'address' => 'Indiranagar, Bangalore - 560038',
                'status' => 1,
                'coverage' => 350,
                'phone' => '+91-9876543212',
                'remember_token' => Str::random(10),
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }

    // ─────────────────────────────────────────────────────
    // 6. CURRENCIES
    // ─────────────────────────────────────────────────────
    private function seedCurrencies(): void
    {
        if (Currency::count() > 0) return;

        Currency::insert([
            ['country' => 'Indian Rupee', 'currency_code' => 'INR', 'currency_symbol' => '₹', 'exchange_rate' => 1.00, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'US Dollar', 'currency_code' => 'USD', 'currency_symbol' => '$', 'exchange_rate' => 0.012, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'South African Rand', 'currency_code' => 'ZAR', 'currency_symbol' => 'R', 'exchange_rate' => 0.22, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Bangladeshi Taka', 'currency_code' => 'BDT', 'currency_symbol' => '৳', 'exchange_rate' => 1.32, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Euro', 'currency_code' => 'EUR', 'currency_symbol' => '€', 'exchange_rate' => 0.011, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'British Pound', 'currency_code' => 'GBP', 'currency_symbol' => '£', 'exchange_rate' => 0.0095, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'UAE Dirham', 'currency_code' => 'AED', 'currency_symbol' => 'د.إ', 'exchange_rate' => 0.044, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    // ─────────────────────────────────────────────────────
    // 7. CATEGORIES
    // ─────────────────────────────────────────────────────
    private function seedCategories(): void
    {
        if (Category::count() > 0) return;

        $now = now()->subDays(30);

        // Parent categories
        $parents = [
            ['name' => 'Fruits', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 1, 'image' => 'def.png'],
            ['name' => 'Vegetables', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 2, 'image' => 'def.png'],
            ['name' => 'Dairy & Eggs', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 3, 'image' => 'def.png'],
            ['name' => 'Meat & Fish', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 4, 'image' => 'def.png'],
            ['name' => 'Bakery', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 5, 'image' => 'def.png'],
            ['name' => 'Beverages', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 6, 'image' => 'def.png'],
            ['name' => 'Snacks', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 7, 'image' => 'def.png'],
            ['name' => 'Household', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'priority' => 8, 'image' => 'def.png'],
        ];

        $parentIds = [];
        foreach ($parents as $i => $p) {
            $cat = Category::create([
                'name' => $p['name'],
                'parent_id' => $p['parent_id'],
                'position' => $p['position'],
                'status' => $p['status'],
                'priority' => $p['priority'],
                'image' => $p['image'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $parentIds[$p['name']] = $cat->id;
        }

        // Subcategories
        $subs = [
            ['name' => 'Apples & Pears', 'parent' => 'Fruits'],
            ['name' => 'Citrus Fruits', 'parent' => 'Fruits'],
            ['name' => 'Berries', 'parent' => 'Fruits'],
            ['name' => 'Bananas & Plantains', 'parent' => 'Fruits'],
            ['name' => 'Mangoes & Tropical', 'parent' => 'Fruits'],

            ['name' => 'Leafy Greens', 'parent' => 'Vegetables'],
            ['name' => 'Root Vegetables', 'parent' => 'Vegetables'],
            ['name' => 'Tomatoes & Chillies', 'parent' => 'Vegetables'],
            ['name' => 'Cucumbers & Gourds', 'parent' => 'Vegetables'],
            ['name' => 'Onions & Garlic', 'parent' => 'Vegetables'],

            ['name' => 'Milk', 'parent' => 'Dairy & Eggs'],
            ['name' => 'Cheese', 'parent' => 'Dairy & Eggs'],
            ['name' => 'Yogurt', 'parent' => 'Dairy & Eggs'],
            ['name' => 'Eggs', 'parent' => 'Dairy & Eggs'],
            ['name' => 'Butter & Cream', 'parent' => 'Dairy & Eggs'],

            ['name' => 'Chicken', 'parent' => 'Meat & Fish'],
            ['name' => 'Mutton', 'parent' => 'Meat & Fish'],
            ['name' => 'Fish', 'parent' => 'Meat & Fish'],
            ['name' => 'Prawns & Seafood', 'parent' => 'Meat & Fish'],

            ['name' => 'Bread & Buns', 'parent' => 'Bakery'],
            ['name' => 'Cakes & Pastries', 'parent' => 'Bakery'],
            ['name' => 'Cookies & Biscuits', 'parent' => 'Bakery'],

            ['name' => 'Soft Drinks', 'parent' => 'Beverages'],
            ['name' => 'Juices', 'parent' => 'Beverages'],
            ['name' => 'Tea & Coffee', 'parent' => 'Beverages'],
            ['name' => 'Water', 'parent' => 'Beverages'],

            ['name' => 'Chips & Crisps', 'parent' => 'Snacks'],
            ['name' => 'Namkeen', 'parent' => 'Snacks'],
            ['name' => 'Chocolates', 'parent' => 'Snacks'],

            ['name' => 'Cleaning Supplies', 'parent' => 'Household'],
            ['name' => 'Disposables', 'parent' => 'Household'],
        ];

        $subIds = [];
        foreach ($subs as $i => $s) {
            $cat = Category::create([
                'name' => $s['name'],
                'parent_id' => $parentIds[$s['parent']],
                'position' => 1,
                'status' => 1,
                'priority' => ($i % 10) + 1,
                'image' => 'def.png',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $subIds[$s['name']] = $cat->id;
        }
    }

    // ─────────────────────────────────────────────────────
    // 7. PRODUCTS
    // ─────────────────────────────────────────────────────
    private function seedProducts(): void
    {
        if (Product::count() > 0) return;

        $categories = Category::where('position', 1)->pluck('id', 'name')->toArray();

        $products = [
            // Fruits
            ['name' => 'Red Apple', 'sub' => 'Apples & Pears', 'price' => 120, 'discount' => 10, 'stock' => 100],
            ['name' => 'Green Apple', 'sub' => 'Apples & Pears', 'price' => 130, 'discount' => 0, 'stock' => 80],
            ['name' => 'Pear', 'sub' => 'Apples & Pears', 'price' => 150, 'discount' => 5, 'stock' => 60],
            ['name' => 'Orange', 'sub' => 'Citrus Fruits', 'price' => 80, 'discount' => 0, 'stock' => 120],
            ['name' => 'Sweet Lime (Mosambi)', 'sub' => 'Citrus Fruits', 'price' => 70, 'discount' => 0, 'stock' => 90],
            ['name' => 'Lemon', 'sub' => 'Citrus Fruits', 'price' => 10, 'discount' => 0, 'stock' => 500],
            ['name' => 'Strawberry', 'sub' => 'Berries', 'price' => 250, 'discount' => 15, 'stock' => 40],
            ['name' => 'Blueberry', 'sub' => 'Berries', 'price' => 350, 'discount' => 0, 'stock' => 30],
            ['name' => 'Raspberry', 'sub' => 'Berries', 'price' => 300, 'discount' => 0, 'stock' => 25],
            ['name' => 'Banana (Regular)', 'sub' => 'Bananas & Plantains', 'price' => 50, 'discount' => 0, 'stock' => 200],
            ['name' => 'Banana (Elachi)', 'sub' => 'Bananas & Plantains', 'price' => 70, 'discount' => 0, 'stock' => 150],
            ['name' => 'Alphonso Mango', 'sub' => 'Mangoes & Tropical', 'price' => 400, 'discount' => 20, 'stock' => 50],
            ['name' => 'Pineapple', 'sub' => 'Mangoes & Tropical', 'price' => 80, 'discount' => 0, 'stock' => 40],
            ['name' => 'Papaya', 'sub' => 'Mangoes & Tropical', 'price' => 60, 'discount' => 0, 'stock' => 60],

            // Vegetables
            ['name' => 'Spinach (Palak)', 'sub' => 'Leafy Greens', 'price' => 30, 'discount' => 0, 'stock' => 100],
            ['name' => 'Fenugreek (Methi)', 'sub' => 'Leafy Greens', 'price' => 25, 'discount' => 0, 'stock' => 80],
            ['name' => 'Coriander Leaves', 'sub' => 'Leafy Greens', 'price' => 15, 'discount' => 0, 'stock' => 150],
            ['name' => 'Potato', 'sub' => 'Root Vegetables', 'price' => 30, 'discount' => 0, 'stock' => 300],
            ['name' => 'Tomato', 'sub' => 'Tomatoes & Chillies', 'price' => 40, 'discount' => 0, 'stock' => 200],
            ['name' => 'Green Chilli', 'sub' => 'Tomatoes & Chillies', 'price' => 20, 'discount' => 0, 'stock' => 150],
            ['name' => 'Cucumber', 'sub' => 'Cucumbers & Gourds', 'price' => 35, 'discount' => 0, 'stock' => 80],
            ['name' => 'Bottle Gourd (Lauki)', 'sub' => 'Cucumbers & Gourds', 'price' => 30, 'discount' => 0, 'stock' => 60],
            ['name' => 'Onion', 'sub' => 'Onions & Garlic', 'price' => 35, 'discount' => 0, 'stock' => 250],
            ['name' => 'Garlic', 'sub' => 'Onions & Garlic', 'price' => 100, 'discount' => 0, 'stock' => 100],
            ['name' => 'Ginger', 'sub' => 'Root Vegetables', 'price' => 80, 'discount' => 0, 'stock' => 80],
            ['name' => 'Carrot', 'sub' => 'Root Vegetables', 'price' => 60, 'discount' => 0, 'stock' => 90],

            // Dairy & Eggs
            ['name' => 'Full Cream Milk (1L)', 'sub' => 'Milk', 'price' => 64, 'discount' => 0, 'stock' => 100],
            ['name' => 'Toned Milk (1L)', 'sub' => 'Milk', 'price' => 54, 'discount' => 0, 'stock' => 120],
            ['name' => 'Amul Butter (500g)', 'sub' => 'Butter & Cream', 'price' => 260, 'discount' => 5, 'stock' => 50],
            ['name' => 'Fresh Cream (200ml)', 'sub' => 'Butter & Cream', 'price' => 95, 'discount' => 0, 'stock' => 40],
            ['name' => 'Mozzarella Cheese (200g)', 'sub' => 'Cheese', 'price' => 190, 'discount' => 0, 'stock' => 60],
            ['name' => 'Cheddar Cheese (200g)', 'sub' => 'Cheese', 'price' => 210, 'discount' => 10, 'stock' => 45],
            ['name' => 'Greek Yogurt (400g)', 'sub' => 'Yogurt', 'price' => 150, 'discount' => 0, 'stock' => 50],
            ['name' => 'Flavored Yogurt - Strawberry', 'sub' => 'Yogurt', 'price' => 80, 'discount' => 0, 'stock' => 70],
            ['name' => 'Farm Eggs - 6 pcs', 'sub' => 'Eggs', 'price' => 45, 'discount' => 0, 'stock' => 200],
            ['name' => 'Farm Eggs - 12 pcs', 'sub' => 'Eggs', 'price' => 85, 'discount' => 5, 'stock' => 150],

            // Meat & Fish
            ['name' => 'Chicken Breast (500g)', 'sub' => 'Chicken', 'price' => 190, 'discount' => 0, 'stock' => 60],
            ['name' => 'Chicken Curry Cut (500g)', 'sub' => 'Chicken', 'price' => 170, 'discount' => 0, 'stock' => 80],
            ['name' => 'Chicken Drumsticks (500g)', 'sub' => 'Chicken', 'price' => 200, 'discount' => 0, 'stock' => 50],
            ['name' => 'Mutton Curry Cut (500g)', 'sub' => 'Mutton', 'price' => 350, 'discount' => 0, 'stock' => 40],
            ['name' => 'Rohu Fish (500g)', 'sub' => 'Fish', 'price' => 200, 'discount' => 0, 'stock' => 35],
            ['name' => 'Pomfret Fish (500g)', 'sub' => 'Fish', 'price' => 350, 'discount' => 0, 'stock' => 25],
            ['name' => 'Prawns (250g)', 'sub' => 'Prawns & Seafood', 'price' => 280, 'discount' => 0, 'stock' => 40],

            // Bakery
            ['name' => 'Whole Wheat Bread (400g)', 'sub' => 'Bread & Buns', 'price' => 40, 'discount' => 0, 'stock' => 100],
            ['name' => 'White Bread (400g)', 'sub' => 'Bread & Buns', 'price' => 35, 'discount' => 0, 'stock' => 100],
            ['name' => 'Burger Buns - 4 pcs', 'sub' => 'Bread & Buns', 'price' => 50, 'discount' => 0, 'stock' => 60],
            ['name' => 'Chocolate Cake (500g)', 'sub' => 'Cakes & Pastries', 'price' => 450, 'discount' => 10, 'stock' => 20],
            ['name' => 'Fruit Cake (500g)', 'sub' => 'Cakes & Pastries', 'price' => 400, 'discount' => 0, 'stock' => 15],
            ['name' => 'Butter Cookies (200g)', 'sub' => 'Cookies & Biscuits', 'price' => 95, 'discount' => 0, 'stock' => 80],
            ['name' => 'Digestive Biscuits (200g)', 'sub' => 'Cookies & Biscuits', 'price' => 55, 'discount' => 0, 'stock' => 100],

            // Beverages
            ['name' => 'Coca Cola (2L)', 'sub' => 'Soft Drinks', 'price' => 90, 'discount' => 0, 'stock' => 100],
            ['name' => 'Pepsi (2L)', 'sub' => 'Soft Drinks', 'price' => 90, 'discount' => 0, 'stock' => 100],
            ['name' => 'Orange Juice (1L)', 'sub' => 'Juices', 'price' => 120, 'discount' => 0, 'stock' => 60],
            ['name' => 'Apple Juice (1L)', 'sub' => 'Juices', 'price' => 130, 'discount' => 0, 'stock' => 55],
            ['name' => 'Green Tea (25 bags)', 'sub' => 'Tea & Coffee', 'price' => 175, 'discount' => 5, 'stock' => 40],
            ['name' => 'Instant Coffee (200g)', 'sub' => 'Tea & Coffee', 'price' => 350, 'discount' => 0, 'stock' => 30],
            ['name' => 'Mineral Water (1L)', 'sub' => 'Water', 'price' => 20, 'discount' => 0, 'stock' => 200],

            // Snacks
            ['name' => 'Lays Potato Chips (Large)', 'sub' => 'Chips & Crisps', 'price' => 50, 'discount' => 0, 'stock' => 150],
            ['name' => 'Pringles (Original)', 'sub' => 'Chips & Crisps', 'price' => 130, 'discount' => 0, 'stock' => 60],
            ['name' => 'Haldiram\'s Namkeen (200g)', 'sub' => 'Namkeen', 'price' => 60, 'discount' => 0, 'stock' => 100],
            ['name' => 'Dairy Milk Chocolate (150g)', 'sub' => 'Chocolates', 'price' => 130, 'discount' => 0, 'stock' => 80],
            ['name' => 'KitKat (110g)', 'sub' => 'Chocolates', 'price' => 120, 'discount' => 5, 'stock' => 70],

            // Household
            ['name' => 'Dishwashing Liquid (500ml)', 'sub' => 'Cleaning Supplies', 'price' => 95, 'discount' => 0, 'stock' => 60],
            ['name' => 'All Purpose Cleaner (500ml)', 'sub' => 'Cleaning Supplies', 'price' => 120, 'discount' => 0, 'stock' => 50],
            ['name' => 'Paper Napkins (100 pcs)', 'sub' => 'Disposables', 'price' => 65, 'discount' => 0, 'stock' => 80],
            ['name' => 'Aluminum Foil (10m)', 'sub' => 'Disposables', 'price' => 80, 'discount' => 0, 'stock' => 40],
        ];

        $now = now()->subDays(20);
        foreach ($products as $p) {
            $categoryId = $categories[$p['sub']] ?? 1;

            $product = Product::create([
                'name' => $p['name'],
                'description' => 'Fresh and high-quality ' . $p['name'] . ' delivered to your doorstep.',
                'category_ids' => json_encode([['id' => $categoryId, 'position' => 1]]),
                'image' => json_encode(['def.png']),
                'price' => $p['price'],
                'discount' => $p['discount'],
                'discount_type' => $p['discount'] > 0 ? 'percent' : 'flat',
                'total_stock' => $p['stock'],
                'unit' => 'pcs',
                'capacity' => 1,
                'status' => 1,
                'is_featured' => rand(0, 1),
                'popularity_count' => rand(10, 500),
                'point_value' => rand(100, 1000),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // 8. ADDONS (skip if table doesn't exist)
    // ─────────────────────────────────────────────────────
    private function seedAddOns(): void
    {
        try {
            if (AddOn::count() > 0) return;

            $addons = [
                ['name' => 'Extra Cheese', 'price' => 30, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Extra Spicy', 'price' => 0, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Gift Wrap', 'price' => 20, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Ice Pack', 'price' => 15, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Cut & Pack', 'price' => 10, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Greeting Card', 'price' => 25, 'created_at' => now(), 'updated_at' => now()],
            ];
            AddOn::insert($addons);
        } catch (\Exception $e) {
            // table doesn't exist, skip
        }
    }

    // ─────────────────────────────────────────────────────
    // 9. COUPONS
    // ─────────────────────────────────────────────────────
    private function seedCoupons(): void
    {
        if (Coupon::count() > 0) return;

        $coupons = [
            [
                'title' => 'WELCOME20',
                'code' => 'WELCOME20',
                'start_date' => now()->subMonth()->toDateString(),
                'expire_date' => now()->addMonths(2)->toDateString(),
                'min_purchase' => 500,
                'max_discount' => 100,
                'discount' => 20,
                'discount_type' => 'percentage',
                'coupon_type' => 'default',
                'status' => 1,
                'limit' => 100,
                'created_at' => now()->subMonth(),
                'updated_at' => now(),
            ],
            [
                'title' => 'FRESH50',
                'code' => 'FRESH50',
                'start_date' => now()->subDays(10)->toDateString(),
                'expire_date' => now()->addMonth()->toDateString(),
                'min_purchase' => 1000,
                'max_discount' => 150,
                'discount' => 50,
                'discount_type' => 'flat',
                'coupon_type' => 'default',
                'status' => 1,
                'limit' => 50,
                'created_at' => now()->subDays(10),
                'updated_at' => now(),
            ],
            [
                'title' => 'FIRSTORDER',
                'code' => 'FIRSTORDER',
                'start_date' => now()->subMonth()->toDateString(),
                'expire_date' => now()->addMonths(3)->toDateString(),
                'min_purchase' => 300,
                'max_discount' => 75,
                'discount' => 15,
                'discount_type' => 'percentage',
                'coupon_type' => 'default',
                'status' => 1,
                'limit' => 200,
                'created_at' => now()->subMonth(),
                'updated_at' => now(),
            ],
            [
                'title' => 'FESTIVE100',
                'code' => 'FESTIVE100',
                'start_date' => now()->toDateString(),
                'expire_date' => now()->addDays(15)->toDateString(),
                'min_purchase' => 800,
                'max_discount' => 100,
                'discount' => 100,
                'discount_type' => 'flat',
                'coupon_type' => 'default',
                'status' => 1,
                'limit' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Coupon::insert($coupons);
    }

    // ─────────────────────────────────────────────────────
    // 10. BANNERS
    // ─────────────────────────────────────────────────────
    private function seedBanners(): void
    {
        if (Banner::count() > 0) return;

        $banners = [
            ['title' => 'Fresh Fruits Sale', 'image' => 'def.png', 'product_id' => null, 'category_id' => 1, 'status' => 1, 'created_at' => now()->subDays(20), 'updated_at' => now()],
            ['title' => 'Vegetable Bonanza', 'image' => 'def.png', 'product_id' => null, 'category_id' => 2, 'status' => 1, 'created_at' => now()->subDays(19), 'updated_at' => now()],
            ['title' => 'Dairy Products - Fresh Daily', 'image' => 'def.png', 'product_id' => null, 'category_id' => 3, 'status' => 1, 'created_at' => now()->subDays(18), 'updated_at' => now()],
            ['title' => 'Premium Meats', 'image' => 'def.png', 'product_id' => null, 'category_id' => 4, 'status' => 1, 'created_at' => now()->subDays(17), 'updated_at' => now()],
            ['title' => 'Bakery Delights', 'image' => 'def.png', 'product_id' => null, 'category_id' => 5, 'status' => 1, 'created_at' => now()->subDays(16), 'updated_at' => now()],
        ];
        Banner::insert($banners);
    }

    // ─────────────────────────────────────────────────────
    // 11. SOCIAL MEDIA
    // ─────────────────────────────────────────────────────
    private function seedSocialMedia(): void
    {
        if (SocialMedia::count() > 0) return;

        SocialMedia::insert([
            ['name' => 'facebook', 'link' => 'https://facebook.com/grofresh', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'instagram', 'link' => 'https://instagram.com/grofresh', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'twitter', 'link' => 'https://twitter.com/grofresh', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'youtube', 'link' => 'https://youtube.com/@grofresh', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    // ─────────────────────────────────────────────────────
    // 12. CUSTOMERS (Users)
    // ─────────────────────────────────────────────────────
    private function seedUsers(): void
    {
        if (User::count() > 1) return;

        $now = now()->subMonths(3);

        $customers = [
            [
                'f_name' => 'Rahul', 'l_name' => 'Sharma', 'email' => 'rahul@example.com', 'phone' => '+91-9876500001',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF001', 'wallet_balance' => 500,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Priya', 'l_name' => 'Patel', 'email' => 'priya@example.com', 'phone' => '+91-9876500002',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF002', 'wallet_balance' => 200,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Amit', 'l_name' => 'Verma', 'email' => 'amit@example.com', 'phone' => '+91-9876500003',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF003', 'wallet_balance' => 1000,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Sneha', 'l_name' => 'Reddy', 'email' => 'sneha@example.com', 'phone' => '+91-9876500004',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF004', 'wallet_balance' => 0,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Vikram', 'l_name' => 'Singh', 'email' => 'vikram@example.com', 'phone' => '+91-9876500005',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF005', 'wallet_balance' => 750,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Neha', 'l_name' => 'Gupta', 'email' => 'neha@example.com', 'phone' => '+91-9876500006',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF006', 'wallet_balance' => 150,
                'is_phone_verified' => 1, 'language_code' => 'hi',
            ],
            [
                'f_name' => 'Rajesh', 'l_name' => 'Kumar', 'email' => 'rajesh@example.com', 'phone' => '+91-9876500007',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF007', 'wallet_balance' => 0,
                'is_phone_verified' => 0, 'language_code' => 'en',
            ],
            [
                'f_name' => 'Ananya', 'l_name' => 'Joshi', 'email' => 'ananya@example.com', 'phone' => '+91-9876500008',
                'password' => bcrypt('12345678'), 'referral_code' => 'REF008', 'wallet_balance' => 1200,
                'is_phone_verified' => 1, 'language_code' => 'en',
            ],
        ];

        foreach ($customers as $i => $c) {
            $user = User::create(array_merge($c, [
                'image' => 'def.png',
                'is_block' => 0,
                'login_medium' => 'email',
                'created_at' => $now->addDays($i * 5),
                'updated_at' => now(),
            ]));
        }
    }

    // ─────────────────────────────────────────────────────
    // 13. CUSTOMER ADDRESSES
    // ─────────────────────────────────────────────────────
    private function seedCustomerAddresses(): void
    {
        if (CustomerAddress::count() > 0) return;

        $users = User::whereNotNull('f_name')->pluck('id');

        $addresses = [
            ['address_type' => 'home', 'contact_person_name' => 'Rahul Sharma', 'contact_person_number' => '+91-9876500001', 'address' => '42, Lake View Apartments, Andheri West', 'road' => 'Lake View Road', 'house' => 'A-42', 'floor' => '3rd Floor', 'latitude' => '19.1136', 'longitude' => '72.8697'],
            ['address_type' => 'office', 'contact_person_name' => 'Rahul Sharma', 'contact_person_number' => '+91-9876500001', 'address' => 'Suite 201, Tech Park, BKC', 'road' => 'BKC Road', 'house' => '201', 'floor' => '2nd Floor', 'latitude' => '19.0760', 'longitude' => '72.8777'],
            ['address_type' => 'home', 'contact_person_name' => 'Priya Patel', 'contact_person_number' => '+91-9876500002', 'address' => '7, Green Valley Colony, Ahmedabad', 'road' => 'SG Highway', 'house' => '7', 'floor' => 'Ground', 'latitude' => '23.0225', 'longitude' => '72.5714'],
            ['address_type' => 'home', 'contact_person_name' => 'Amit Verma', 'contact_person_number' => '+91-9876500003', 'address' => '15, Sunshine Residency, Whitefield', 'road' => 'Whitefield Main Road', 'house' => '15', 'floor' => '1st Floor', 'latitude' => '12.9698', 'longitude' => '77.7500'],
            ['address_type' => 'home', 'contact_person_name' => 'Sneha Reddy', 'contact_person_number' => '+91-9876500004', 'address' => 'Plot 8, Jubilee Hills, Hyderabad', 'road' => 'Road No 12', 'house' => '8', 'floor' => '2nd Floor', 'latitude' => '17.4319', 'longitude' => '78.4095'],
        ];

        $i = 0;
        foreach ($users as $uid) {
            if ($i >= count($addresses)) break;
            CustomerAddress::create(array_merge($addresses[$i], [
                'user_id' => $uid,
                'is_guest' => 0,
                'created_at' => now()->subDays(60 - $i * 10),
                'updated_at' => now(),
            ]));
            $i++;
        }
    }

    // ─────────────────────────────────────────────────────
    // 14. DELIVERY MEN
    // ─────────────────────────────────────────────────────
    private function seedDeliveryMen(): void
    {
        if (DeliveryMan::count() > 0) return;

        $dms = [
            ['f_name' => 'Suresh', 'l_name' => 'Rathod', 'phone' => '+91-9876000001', 'email' => 'suresh@delivery.com', 'password' => bcrypt('12345678'), 'branch_id' => 1, 'identity_type' => 'driving_license', 'identity_number' => 'DL-123456', 'image' => 'def.png', 'is_active' => 1, 'application_status' => 'approved'],
            ['f_name' => 'Manoj', 'l_name' => 'Tiwari', 'phone' => '+91-9876000002', 'email' => 'manoj@delivery.com', 'password' => bcrypt('12345678'), 'branch_id' => 1, 'identity_type' => 'aadhar', 'identity_number' => 'A-789012', 'image' => 'def.png', 'is_active' => 1, 'application_status' => 'approved'],
            ['f_name' => 'Deepak', 'l_name' => 'Yadav', 'phone' => '+91-9876000003', 'email' => 'deepak@delivery.com', 'password' => bcrypt('12345678'), 'branch_id' => 2, 'identity_type' => 'driving_license', 'identity_number' => 'DL-789012', 'image' => 'def.png', 'is_active' => 1, 'application_status' => 'approved'],
            ['f_name' => 'Ravi', 'l_name' => 'Kumar', 'phone' => '+91-9876000004', 'email' => 'ravi@delivery.com', 'password' => bcrypt('12345678'), 'branch_id' => 2, 'identity_type' => 'aadhar', 'identity_number' => 'A-345678', 'image' => 'def.png', 'is_active' => 1, 'application_status' => 'approved'],
            ['f_name' => 'Vijay', 'l_name' => 'Naik', 'phone' => '+91-9876000005', 'email' => 'vijay@delivery.com', 'password' => bcrypt('12345678'), 'branch_id' => 3, 'identity_type' => 'driving_license', 'identity_number' => 'DL-901234', 'image' => 'def.png', 'is_active' => 1, 'application_status' => 'approved'],
        ];

        $now = now()->subDays(45);
        foreach ($dms as $i => $dm) {
            DeliveryMan::create(array_merge($dm, [
                'language_code' => 'en',
                'created_at' => $now->addDays($i * 3),
                'updated_at' => now(),
            ]));
        }
    }

    // ─────────────────────────────────────────────────────
    // 15. ORDERS & ORDER DETAILS
    // ─────────────────────────────────────────────────────
    private function seedOrders(): void
    {
        if (Order::count() > 0) return;

        $users = User::pluck('id')->toArray();
        $branches = Branch::pluck('id')->toArray();
        $deliveryMen = DeliveryMan::pluck('id')->toArray();
        $timeSlots = TimeSlot::pluck('id')->toArray();
        $products = Product::pluck('id', 'name')->toArray();

        $orderStatuses = ['pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'delivered', 'delivered', 'delivered'];
        $paymentMethods = ['cash_on_delivery', 'digital_payment', 'wallet'];
        $paymentStatuses = ['paid', 'unpaid'];

        $now = now();

        for ($i = 1; $i <= 15; $i++) {
            $userId = $users[array_rand($users)];
            $branchId = $branches[array_rand($branches)];
            $deliveryManId = $deliveryMen[array_rand($deliveryMen)];
            $timeSlotId = $timeSlots[array_rand($timeSlots)];
            $status = $orderStatuses[array_rand($orderStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            // Pick 1-4 random products for this order
            $allProductIds = Product::pluck('id')->toArray();
            $orderProductIds = (array) array_rand(array_flip($allProductIds), rand(1, 4));

            $totalAmount = 0;
            $totalTax = 0;
            $totalDiscount = 0;
            $totalPointValue = 0;
            $orderDetailsData = [];

            foreach ($orderProductIds as $productId) {
                $product = Product::find($productId);
                if (!$product) continue;

                $qty = rand(1, 3);
                $price = $product->price;
                $discount = $product->discount ?? 0;
                $taxAmt = round($price * 0.05 * $qty, 2); // 5% tax
                $discountAmt = $discount > 0 ? round(($price * $discount / 100) * $qty, 2) : 0;
                $subtotal = ($price * $qty) - $discountAmt;
                $totalAmount += $subtotal + $taxAmt;
                $totalTax += $taxAmt;
                $totalDiscount += $discountAmt;
                $totalPointValue += ($product->point_value ?? 500) * $qty;

                $orderDetailsData[] = [
                    'product_id' => $productId,
                    'price' => $price,
                    'discount_on_product' => $discountAmt,
                    'quantity' => $qty,
                    'tax_amount' => $taxAmt,
                    'vat_status' => 'excluded',
                    'point_value' => ($product->point_value ?? 500) * $qty,
                    'created_at' => $now->subDays(15 - $i),
                    'updated_at' => $now->subDays(15 - $i),
                ];
            }

            $deliveryCharge = rand(0, 1) ? 50 : 0;
            $orderAmount = $totalAmount + $deliveryCharge;

            $order = Order::create([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'delivery_man_id' => $deliveryManId,
                'time_slot_id' => $timeSlotId,
                'order_amount' => $orderAmount,
                'order_status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'order_type' => 'delivery',
                'delivery_charge' => $deliveryCharge,
                'total_tax_amount' => $totalTax,
                'coupon_discount_amount' => 0,
                'extra_discount' => 0,
                'coupon_code' => null,
                'delivery_address_id' => 1,
                'delivery_date' => $now->subDays(15 - $i)->toDateString(),
                'delivery_address' => json_encode(['address' => 'Test Address', 'contact_person_name' => 'Test User']),
                'checked' => 1,
                'free_delivery_amount' => 0,
                'point_value' => $totalPointValue,
                'created_at' => $now->subDays(15 - $i)->addHours(rand(6, 20)),
                'updated_at' => $now->subDays(15 - $i)->addHours(rand(6, 20)),
            ]);

            foreach ($orderDetailsData as $detail) {
                OrderDetail::create(array_merge($detail, ['order_id' => $order->id]));
            }
        }
    }

    // ─────────────────────────────────────────────────────
    // 16. REVIEWS
    // ─────────────────────────────────────────────────────
    private function seedReviews(): void
    {
        if (Review::count() > 0) return;

        $users = User::pluck('id')->toArray();
        $products = Product::pluck('id')->toArray();
        $orders = Order::pluck('id')->toArray();

        $comments = [
            'Excellent quality! Will order again.',
            'Very fresh produce. Highly recommend.',
            'Good packaging and timely delivery.',
            'Average quality this time.',
            'Great value for money.',
            'Fresh and delicious as always.',
            'Slightly damaged packaging but product was fine.',
            'Best online grocery store!',
            'Could be better. Expected fresher items.',
            'Perfect! Just what I needed.',
        ];

        $ratings4 = [4, 5, 5, 4, 5, 4, 5, 5];
        $ratingsAll = [1, 2, 3, 4, 5, 4, 5, 3, 4, 5];

        for ($i = 0; $i < 20; $i++) {
            Review::create([
                'product_id' => $products[array_rand($products)],
                'user_id' => $users[array_rand($users)],
                'order_id' => $orders[array_rand($orders)],
                'comment' => $comments[array_rand($comments)],
                'rating' => $ratingsAll[array_rand($ratingsAll)],
                'is_active' => 1,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // 17. FLASH DEALS
    // ─────────────────────────────────────────────────────
    private function seedFlashDeals(): void
    {
        if (FlashDeal::count() > 0) return;

        $deals = [
            [
                'title' => 'Mega Monday Sale',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
                'deal_type' => 'flash_deal',
                'status' => 1,
                'featured' => 1,
                'image' => 'def.png',
                'created_at' => now()->subDays(6),
                'updated_at' => now(),
            ],
            [
                'title' => 'Weekend Special',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(5),
                'deal_type' => 'flash_deal',
                'status' => 1,
                'featured' => 0,
                'image' => 'def.png',
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
            [
                'title' => 'Summer Cool Treats',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(15),
                'deal_type' => 'flash_deal',
                'status' => 1,
                'featured' => 1,
                'image' => 'def.png',
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
        ];

        $products = Product::pluck('id')->toArray();

        foreach ($deals as $d) {
            $deal = FlashDeal::create($d);

            // Attach 3-5 random products
            $allProductIds = Product::pluck('id')->toArray();
            $dealProductIds = (array) array_rand(array_flip($allProductIds), rand(3, 5));
            foreach ($dealProductIds as $prodId) {
                FlashDealProduct::create([
                    'flash_deal_id' => $deal->id,
                    'product_id' => $prodId,
                    'discount' => rand(10, 30),
                    'discount_type' => 'percent',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────
    // 18. TRANSLATIONS (sample)
    // ─────────────────────────────────────────────────────
    private function seedTranslations(): void
    {
        if (Translation::count() > 0) return;

        $categories = Category::all();

        foreach ($categories as $cat) {
            Translation::create([
                'translationable_type' => 'App\Model\Category',
                'translationable_id' => $cat->id,
                'locale' => 'en',
                'key' => 'name',
                'value' => $cat->name,
            ]);
        }
    }
}
