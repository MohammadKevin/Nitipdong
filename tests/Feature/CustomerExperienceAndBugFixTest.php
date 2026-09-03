<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerExperienceAndBugFixTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Store $store;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'role'              => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->seller = User::factory()->create([
            'role'              => 'seller',
            'email_verified_at' => now(),
        ]);

        $this->store = Store::create([
            'user_id'             => $this->seller->id,
            'name'                => 'Toko Serba Indah',
            'slug'                => 'toko-serba-indah',
            'province'            => 'Jawa Timur',
            'city'                => 'Surabaya',
            'district'            => 'Gubeng',
            'postal_code'         => '60281',
            'address'             => 'Jl. Gubeng Kertajaya No. 10',
            'status'              => 'approved',
            'balance'             => 0,
            'bank_name'           => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Seller Name',
        ]);

        $this->category = Category::create([
            'name' => 'Komputer & Aksesoris',
            'slug' => 'komputer-aksesoris',
        ]);
    }

    /**
     * Test 1: Seller Product Update properly parses and saves variants and specifications
     */
    public function test_seller_product_update_saves_variants_and_specifications(): void
    {
        $product = Product::create([
            'store_id'       => $this->store->id,
            'category_id'    => $this->category->id,
            'name'           => 'Headset Gaming Pro',
            'slug'           => 'headset-gaming-pro',
            'description'    => 'Headset gaming surround 7.1',
            'price'          => 350000,
            'stock'          => 15,
            'is_active'      => true,
            'specifications' => null,
            'variants'       => null,
        ]);

        $response = $this->actingAs($this->seller)->put(route('seller.products.update', $product), [
            'category_id'         => $this->category->id,
            'name'                => 'Headset Gaming Pro Updated',
            'description'         => 'Deskripsi baru yang lebih detail',
            'price'               => 399000,
            'stock'               => 20,
            'condition'           => 'new',
            'is_active'           => '1',
            'spec_keys'           => ['Konektivitas', 'Garansi'],
            'spec_values'         => ['USB Type-C', '1 Tahun Resmi'],
            'variant_names'       => ['Warna'],
            'variant_0_options'   => ['Hitam Matte', 'Putih Salju'],
        ]);

        $response->assertRedirect(route('seller.products.index'));

        $updated = $product->fresh();
        $this->assertEquals('Headset Gaming Pro Updated', $updated->name);
        $this->assertIsArray($updated->specifications);
        $this->assertEquals('USB Type-C', $updated->specifications['Konektivitas']);
        $this->assertEquals('1 Tahun Resmi', $updated->specifications['Garansi']);

        $this->assertIsArray($updated->variants);
        $this->assertCount(1, $updated->variants);
        $this->assertEquals('Warna', $updated->variants[0]['name']);
        $this->assertEquals(['Hitam Matte', 'Putih Salju'], $updated->variants[0]['options']);
    }

    /**
     * Test 2: Order cancellation properly stores customer-selected cancel_reason
     */
    public function test_customer_order_cancellation_preserves_custom_reason(): void
    {
        $order = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 150000,
            'status'           => Order::STATUS_PENDING,
            'shipping_address' => 'Surabaya',
        ]);

        $response = $this->actingAs($this->buyer)->post("/customer/orders/{$order->id}/cancel", [
            'cancel_reason' => 'Ingin mengubah alamat pengiriman',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);
    }

    /**
     * Test 3: Customer cart items endpoint returns valid JSON structure for mobile nav
     */
    public function test_cart_items_endpoint_returns_valid_json_count(): void
    {
        $product = Product::create([
            'store_id'    => $this->store->id,
            'category_id' => $this->category->id,
            'name'        => 'Mousepad Speed',
            'slug'        => 'mousepad-speed',
            'description' => 'Mousepad licin anti slip',
            'price'       => 50000,
            'stock'       => 50,
            'is_active'   => true,
        ]);

        $this->actingAs($this->buyer)->post(route('customer.cart.store', $product), [
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->buyer)->getJson(route('customer.cart.items'));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'items',
            'count',
            'subtotal',
            'formatted_subtotal',
        ]);
        $this->assertEquals(1, $response->json('count'));
    }

    /**
     * Test 4: Payment page correctly loads sibling pending orders for multi-store checkout
     */
    public function test_payment_page_loads_other_pending_orders(): void
    {
        $secondSeller = User::factory()->create(['role' => 'seller']);
        $secondStore = Store::create([
            'user_id'             => $secondSeller->id,
            'name'                => 'Toko Cabang Dua',
            'slug'                => 'toko-cabang-dua',
            'province'            => 'Jawa Timur',
            'city'                => 'Surabaya',
            'district'            => 'Wonokromo',
            'postal_code'         => '60241',
            'address'             => 'Jl. Wonokromo No. 5',
            'status'              => 'approved',
            'balance'             => 0,
            'bank_name'           => 'Mandiri',
            'bank_account_number' => '987654321',
            'bank_account_holder' => 'Second Seller',
        ]);

        $order1 = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 100000,
            'status'           => Order::STATUS_PENDING,
            'shipping_address' => 'Surabaya',
        ]);

        $order2 = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $secondStore->id,
            'total_amount'     => 200000,
            'status'           => Order::STATUS_PENDING,
            'shipping_address' => 'Surabaya',
        ]);

        $response = $this->actingAs($this->buyer)->get(route('customer.order.payment', $order1));

        $response->assertOk();
        $response->assertSee('#' . $order2->invoice_number);
        $response->assertSee('Toko Cabang Dua');
    }
}
