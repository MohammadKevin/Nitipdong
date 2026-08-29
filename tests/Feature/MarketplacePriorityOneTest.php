<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketplacePriorityOneTest extends TestCase
{
    use RefreshDatabase;

    protected $seller;
    protected $store;
    protected $category;
    protected $product;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();

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
            'max_order_quantity'  => 10,
            'is_active'           => true,
        ]);

        $this->customer = User::factory()->create([
            'role'              => 'customer',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_customer_can_create_update_and_manage_address_book(): void
    {
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
        $this->assertEquals(1, UserAddress::where('user_id', $this->customer->id)->count());

        $address1 = UserAddress::where('user_id', $this->customer->id)->first();
        $this->assertTrue((bool) $address1->is_default);

        // 2. Create second address (Office)
        $this->actingAs($this->customer)->post(route('customer.addresses.store'), [
            'label'          => 'Kantor',
            'recipient_name' => 'John Doe Kantor',
            'phone'          => '081234567891',
            'full_address'   => 'Gedung Menara Tower Lt. 15',
            'city'           => 'Jakarta Selatan',
            'province'       => 'DKI Jakarta',
            'postal_code'    => '12190',
            'is_default'     => true,
        ]);

        $this->assertEquals(2, UserAddress::where('user_id', $this->customer->id)->count());
        $address1->refresh();
        $this->assertFalse((bool) $address1->is_default);
    }

    public function test_customer_can_toggle_wishlist_for_a_product(): void
    {
        // Add to wishlist
        $response = $this->actingAs($this->customer)->postJson(route('customer.wishlist.toggle', $this->product));
        $response->assertJson([
            'status'        => 'added',
            'is_wishlisted' => true,
        ]);
        $this->assertTrue($this->product->isWishlistedBy($this->customer));

        // Remove from wishlist
        $response2 = $this->actingAs($this->customer)->postJson(route('customer.wishlist.toggle', $this->product));
        $response2->assertJson([
            'status'        => 'removed',
            'is_wishlisted' => false,
        ]);
        $this->assertFalse($this->product->isWishlistedBy($this->customer));
    }

    public function test_customer_can_confirm_received_order_and_status_changes_to_completed(): void
    {
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
        $this->assertEquals('completed', $order->status);
        $this->assertNotNull($order->completed_at);
    }

    public function test_customer_can_submit_review_on_completed_order_and_seller_can_reply(): void
    {
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
        $this->assertEquals(1, Review::where('product_id', $this->product->id)->count());

        $this->product->refresh();
        $this->assertEquals(5.0, (float) $this->product->rating);

        // 2. Prevent duplicate review on same order item
        $dupResponse = $this->actingAs($this->customer)->post(route('customer.reviews.store', $order), [
            'order_item_id' => $orderItem->id,
            'rating'        => 4,
            'comment'       => 'Coba ulas lagi',
        ]);
        $dupResponse->assertSessionHas('error');
        $this->assertEquals(1, Review::where('product_id', $this->product->id)->count());

        // 3. Seller reply to review
        $review = Review::where('product_id', $this->product->id)->first();
        $sellerResponse = $this->actingAs($this->seller)->post(route('seller.reviews.reply', $review), [
            'seller_reply' => 'Terima kasih kak sudah berbelanja di Official Tech Store!',
        ]);

        $sellerResponse->assertSessionHas('success');
        $review->refresh();
        $this->assertEquals('Terima kasih kak sudah berbelanja di Official Tech Store!', $review->seller_reply);
        $this->assertNotNull($review->seller_replied_at);
    }
}
