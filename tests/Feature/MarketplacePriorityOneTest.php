<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->seller = User::factory()->create([
        'role'              => 'seller',
        'password'          => Hash::make('password'),
        'email_verified_at' => now(),
    ]);

    $this->store = Store::create([
        'user_id'     => $this->seller->id,
        'name'        => 'Official Tech Store',
        'slug'        => 'official-tech-store',
        'description' => 'Toko resmi gadget',
        'status'      => 'approved',
    ]);

    $this->category = Category::create([
        'name' => 'Gadget & Tech',
        'slug' => 'gadget-tech',
        'icon' => 'fa-solid fa-laptop',
    ]);

    $this->product = Product::create([
        'store_id'            => $this->store->id,
        'category_id'         => $this->category->id,
        'name'                => 'Headphone Wireless ANC Pro',
        'slug'                => 'headphone-wireless-anc-pro-' . Str::random(5),
        'description'         => 'Headphone bluetooth dengan noise cancelling aktif',
        'price'               => 500000,
        'discount_percentage' => 10,
        'stock'               => 50,
        'is_active'           => true,
    ]);

    $this->customer = User::factory()->create([
        'role'              => 'customer',
        'password'          => Hash::make('password'),
        'email_verified_at' => now(),
    ]);
});

test('customer can create, update, and manage address book', function () {
    // 1. Create primary address
    $response = $this->actingAs($this->customer)->post(route('customer.addresses.store'), [
        'label'          => 'Rumah',
        'recipient_name' => 'John Doe',
        'phone'          => '081234567890',
        'full_address'   => 'Jl. Sudirman No. 45',
        'city'           => 'Jakarta Pusat',
        'province'       => 'DKI Jakarta',
        'postal_code'    => '10220',
        'is_default'     => true,
    ]);

    $response->assertSessionHas('success');
    expect(UserAddress::where('user_id', $this->customer->id)->count())->toBe(1);

    $address1 = UserAddress::where('user_id', $this->customer->id)->first();
    expect($address1->is_default)->toBeTrue();

    // 2. Create second address (Office)
    $this->actingAs($this->customer)->post(route('customer.addresses.store'), [
        'label'          => 'Kantor',
        'recipient_name' => 'John Doe Kantor',
        'phone'          => '081234567891',
        'full_address'   => 'Gedung Menara Tower Lt. 15',
        'city'           => 'Jakarta Selatan',
        'province'       => 'DKI Jakarta',
        'postal_code'    => '12190',
        'is_default'     => true, // set as new default
    ]);

    expect(UserAddress::where('user_id', $this->customer->id)->count())->toBe(2);
    $address1->refresh();
    expect($address1->is_default)->toBeFalse(); // previous default unset
});

test('customer can toggle wishlist for a product', function () {
    // Add to wishlist
    $response = $this->actingAs($this->customer)->postJson(route('customer.wishlist.toggle', $this->product));
    $response->assertJson([
        'status'        => 'added',
        'is_wishlisted' => true,
    ]);
    expect($this->product->isWishlistedBy($this->customer))->toBeTrue();

    // Remove from wishlist
    $response2 = $this->actingAs($this->customer)->postJson(route('customer.wishlist.toggle', $this->product));
    $response2->assertJson([
        'status'        => 'removed',
        'is_wishlisted' => false,
    ]);
    expect($this->product->isWishlistedBy($this->customer))->toBeFalse();
});

test('customer can confirm received order and status changes to completed', function () {
    $order = Order::create([
        'invoice_number'   => 'INV-TEST12345',
        'user_id'          => $this->customer->id,
        'store_id'         => $this->store->id,
        'total_amount'     => 525000,
        'status'           => 'shipped',
        'shipping_address' => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
        'tracking_number'  => 'JNE-987654321',
    ]);

    $response = $this->actingAs($this->customer)->post(route('customer.order.confirm_received', $order));
    $response->assertRedirect(route('customer.dashboard'));

    $order->refresh();
    expect($order->status)->toBe('completed');
    expect($order->completed_at)->not->toBeNull();
});

test('customer can submit review on completed order and seller can reply', function () {
    $order = Order::create([
        'invoice_number'   => 'INV-REVIEW001',
        'user_id'          => $this->customer->id,
        'store_id'         => $this->store->id,
        'total_amount'     => 525000,
        'status'           => 'completed',
        'completed_at'     => now(),
        'shipping_address' => 'Jl. Test No. 1',
    ]);

    $orderItem = OrderItem::create([
        'order_id'   => $order->id,
        'product_id' => $this->product->id,
        'quantity'   => 1,
        'price'      => 525000,
    ]);

    // 1. Customer submit review
    $response = $this->actingAs($this->customer)->post(route('customer.reviews.store', $order), [
        'order_item_id' => $orderItem->id,
        'rating'        => 5,
        'comment'       => 'Barang sangat bagus, suara jernih dan bass mantap!',
        'is_anonymous'  => false,
    ]);

    $response->assertSessionHas('success');
    expect(Review::where('product_id', $this->product->id)->count())->toBe(1);

    $this->product->refresh();
    expect($this->product->rating)->toBe(5.0);

    // 2. Prevent duplicate review on same order item
    $dupResponse = $this->actingAs($this->customer)->post(route('customer.reviews.store', $order), [
        'order_item_id' => $orderItem->id,
        'rating'        => 4,
        'comment'       => 'Coba ulas lagi',
    ]);
    $dupResponse->assertSessionHas('error');
    expect(Review::where('product_id', $this->product->id)->count())->toBe(1);

    // 3. Seller reply to review
    $review = Review::where('product_id', $this->product->id)->first();
    $sellerResponse = $this->actingAs($this->seller)->post(route('seller.reviews.reply', $review), [
        'seller_reply' => 'Terima kasih kak sudah berbelanja di Official Tech Store!',
    ]);

    $sellerResponse->assertSessionHas('success');
    $review->refresh();
    expect($review->seller_reply)->toBe('Terima kasih kak sudah berbelanja di Official Tech Store!');
    expect($review->seller_replied_at)->not->toBeNull();
});
