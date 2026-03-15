<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'business_name' => ['required', 'string', 'max:255', 'unique:shops,name'],
            'whatsapp_number' => ['required', 'string', 'max:20'],
            'state' => ['required', 'string', 'max:50'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_description' => ['nullable', 'string'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'product_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ])->validate();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input) {
            // 1. Create the User
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            // Assign Vendor Role
            $user->assignRole('vendor');

            // 2. Create the Shop
            $shop = \App\Models\Shop::create([
                'user_id' => $user->id,
                'name' => $input['business_name'],
                'slug' => \Illuminate\Support\Str::slug($input['business_name']),
                'whatsapp_number' => $input['whatsapp_number'],
                'state' => $input['state'],
                'is_approved' => false,
            ]);

            // 3. Handle Product Image Upload
            $imagePath = null;
            if (request()->hasFile('product_image')) {
                $imagePath = request()->file('product_image')->store('products', 'public');
            }

            // 4. Create the Initial Product
            \App\Models\Product::create([
                'shop_id' => $shop->id,
                'name' => $input['product_name'],
                'slug' => \Illuminate\Support\Str::slug($input['product_name']),
                'description' => $input['product_description'] ?? null,
                'price' => $input['product_price'],
                'image_path' => $imagePath,
                'is_approved' => false,
            ]);

            return $user;
        });
    }
}
