<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class FoodItemSeeder extends Seeder
{
    public function run(): void
    {
        $bySlug = Category::query()->pluck('id', 'slug');

        $items = [
            [
                'category' => 'starters',
                'name' => 'Paneer Tikka',
                'description' => 'Charred cottage cheese, hung curd marinade, peppers.',
                'price' => 249,
                'is_veg' => true,
                'is_popular' => true,
                'is_special' => true,
                'image' => 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'starters',
                'name' => 'Chicken Tikka',
                'description' => 'Boneless chicken, Kashmiri chilli, smoked over charcoal.',
                'price' => 279,
                'is_veg' => false,
                'is_popular' => true,
                'is_bestseller' => true,
                'image' => 'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'starters',
                'name' => 'Veg Spring Rolls',
                'description' => 'Crisp rolls stuffed with garden vegetables and glass noodles.',
                'price' => 179,
                'is_veg' => true,
                'image' => 'https://images.unsplash.com/photo-1544025162-d76690232da0?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'starters',
                'name' => 'Chilli Chicken',
                'description' => 'Indo-Chinese wok toss with peppers, garlic, and chilli.',
                'price' => 289,
                'is_veg' => false,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'mains',
                'name' => 'Butter Chicken',
                'description' => 'Tandoori chicken in a silky tomato-cashew makhani.',
                'price' => 349,
                'is_veg' => false,
                'is_bestseller' => true,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'mains',
                'name' => 'Palak Paneer',
                'description' => 'Spinach gravy, homemade paneer, a hint of garlic.',
                'price' => 299,
                'is_veg' => true,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'mains',
                'name' => 'Dal Makhani',
                'description' => 'Black lentils simmered overnight with butter and cream.',
                'price' => 269,
                'is_veg' => true,
                'is_bestseller' => true,
                'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'mains',
                'name' => 'Fish Curry',
                'description' => 'Coastal coconut gravy, tamarind, curry leaves, seer fish.',
                'price' => 379,
                'is_veg' => false,
                'is_special' => true,
                'image' => 'https://images.unsplash.com/photo-1626200419199-391ae4be7a41?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'biryani',
                'name' => 'Chicken Dum Biryani',
                'description' => 'Hyderabadi dum, saffron milk, fried onions, raita.',
                'price' => 329,
                'is_veg' => false,
                'is_bestseller' => true,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'biryani',
                'name' => 'Veg Dum Biryani',
                'description' => 'Seasonal vegetables, basmati, whole spices, mint.',
                'price' => 279,
                'is_veg' => true,
                'is_special' => true,
                'image' => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'breads',
                'name' => 'Garlic Naan',
                'description' => 'Tandoor-baked, buttered, fresh garlic and coriander.',
                'price' => 69,
                'is_veg' => true,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1617692855027-33b14f061079?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'breads',
                'name' => 'Butter Naan',
                'description' => 'Soft leavened bread finished with white butter.',
                'price' => 59,
                'is_veg' => true,
                'image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'desserts',
                'name' => 'Gulab Jamun',
                'description' => 'Warm milk dumplings in cardamom syrup. Two pieces.',
                'price' => 119,
                'is_veg' => true,
                'is_popular' => true,
                'image' => 'https://images.unsplash.com/photo-1666190027924-4fbec9352f2a?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'desserts',
                'name' => 'Rasmalai',
                'description' => 'Poached chenna, saffron milk, pistachios. Two pieces.',
                'price' => 139,
                'is_veg' => true,
                'is_special' => true,
                'image' => 'https://images.unsplash.com/photo-1864663248-c76bd2e2c0b3?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'beverages',
                'name' => 'Mango Lassi',
                'description' => 'Alphonso mango, chilled yogurt, a touch of cardamom.',
                'price' => 129,
                'is_veg' => true,
                'is_bestseller' => true,
                'image' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'beverages',
                'name' => 'Masala Chai',
                'description' => 'Assam tea, ginger, crushed spices, full-fat milk.',
                'price' => 49,
                'is_veg' => true,
                'image' => 'https://images.unsplash.com/photo-1571934811356-5cc061b6821f?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'starters',
                'name' => 'Tandoori Prawns',
                'description' => 'Jumbo prawns, mustard oil marinade, lemon and chaat masala.',
                'price' => 429,
                'is_veg' => false,
                'is_special' => true,
                'image' => 'https://images.unsplash.com/photo-1559737558-2f5a35f4523b?auto=format&fit=crop&w=900&q=80',
            ],
        ];

        foreach ($items as $index => $item) {
            $categoryId = $bySlug[$item['category']];
            unset($item['category']);

            FoodItem::query()->updateOrCreate(
                ['name' => $item['name']],
                $item + [
                    'category_id' => $categoryId,
                    'is_available' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
