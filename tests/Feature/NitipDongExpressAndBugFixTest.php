<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Voucher;
use App\Models\Warehouse;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NitipDongExpressAndBugFixTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $sellerUser;
    private Store $store;
    private Product $product;
    private User $admin;
    private User $courier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->sellerUser = User::factory()->create([
            'role' => 'seller',
            'email_verified_at' => now(),
        ]);

        $this->store = Store::create([
            'user_id' => $this->sellerUser->id,
            'name' => 'Toko Resmi Surabaya',
            'slug' => 'toko-resmi-surabaya',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'address' => 'Jl. Pemuda No. 10, Surabaya',
            'status' => 'approved',
            'balance' => 0,
        ]);

        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $this->product = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $category->id,
            'name' => 'Kabel Data Type C',
            'slug' => 'kabel-data-type-c',
            'description' => 'Kabel data fast charging berkualitas tinggi.',
            'price' => 50000,
            'stock' => 20,
            'weight' => 200,
            'sold_count' => 0,
            'status' => 'approved',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->courier = User::factory()->create([
            'role' => 'courier',
            'email_verified_at' => now(),
        ]);
    }

    public function test_shipping_service_calculates_ndx_same_city_free_shipping(): void
    {
        $options = ShippingService::getAvailableOptions(1.0, 'Surabaya', 'Surabaya');

        $this->assertCount(3, $options);

        // Reguler in same city is Rp 0 (Free Shipping)
        $regOption = collect($options)->firstWhere('service_code', 'REG');
        $this->assertNotNull($regOption);
        $this->assertEquals(0, $regOption['cost']);
        $this->assertTrue($regOption['is_free_shipping']);

        // Express has 50% discount in same city
        $expressOption = collect($options)->firstWhere('service_code', 'EXPRESS');
        $this->assertNotNull($expressOption);
        $this->assertEquals(9000, $expressOption['cost']);

        // Same day available in same city
        $sameDayOption = collect($options)->firstWhere('service_code', 'SAME_DAY');
        $this->assertNotNull($sameDayOption);
        $this->assertEquals('SAME_DAY', $sameDayOption['service_code']);
    }

    public function test_shipping_service_handles_cross_city(): void
    {
        $options = ShippingService::getAvailableOptions(1.0, 'Jakarta Pusat', 'Surabaya');

        $regOption = collect($options)->firstWhere('service_code', 'REG');
        $this->assertNotNull($regOption);
        $this->assertGreaterThan(0, $regOption['cost']);

        // Same day unavailable cross-city
        $sameDayOption = collect($options)->firstWhere('service_code', 'SAME_DAY');
        $this->assertNull($sameDayOption);
    }

    public function test_seller_order_management_flow_generates_ndx_tracking(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'store_id' => $this->store->id,
            'invoice_number' => 'INV-TEST-001',
            'total_amount' => 50000,
            'status' => 'pending',
            'shipping_address' => "Penerima\n08123456\nJl. Raya No. 1, Surabaya",
            'shipping_courier' => 'NDX - Reguler',
            'shipping_cost' => 0,
        ]);

        // Seller view orders
        $response = $this->actingAs($this->sellerUser)->get(route('seller.orders.index'));
        $response->assertStatus(200);

        // Seller view order detail
        $response = $this->actingAs($this->sellerUser)->get(route('seller.orders.show', $order));
        $response->assertStatus(200);

        // Seller process order
        $response = $this->actingAs($this->sellerUser)->post(route('seller.orders.process', $order));
        $response->assertRedirect();
        $this->assertEquals('processing', $order->fresh()->status);
        $this->assertEquals('pickup_pending', $order->fresh()->shipping_status);

        // Seller ship order -> NDX tracking auto-generated
        $response = $this->actingAs($this->sellerUser)->post(route('seller.orders.ship', $order));
        $response->assertRedirect();
        $freshOrder = $order->fresh();
        $this->assertEquals('shipped', $freshOrder->status);
        $this->assertEquals('picked_up', $freshOrder->shipping_status);
        $this->assertNotNull($freshOrder->tracking_number);
        $this->assertStringStartsWith('NDX-', $freshOrder->tracking_number);
    }

    public function test_double_credit_seller_balance_prevented_by_seller_credited_at(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'store_id' => $this->store->id,
            'invoice_number' => 'INV-TEST-CREDIT',
            'total_amount' => 100000,
            'status' => 'shipped',
            'shipping_status' => 'picked_up',
            'shipping_address' => "Penerima\n08123456\nJl. Raya No. 1, Surabaya",
        ]);

        $initialBalance = $this->store->fresh()->balance;

        // Customer confirms receipt
        $response = $this->actingAs($this->customer)->post(route('customer.order.confirm_received', $order));
        $response->assertRedirect();

        $freshOrder = $order->fresh();
        $this->assertEquals('completed', $freshOrder->status);
        $this->assertEquals('delivered', $freshOrder->shipping_status);
        $this->assertNotNull($freshOrder->seller_credited_at);

        // 85% of 100000 = 85000 credited
        $expectedBalance = $initialBalance + 85000;
        $this->assertEquals($expectedBalance, $this->store->fresh()->balance);

        // Confirm again should not increase balance
        $this->actingAs($this->customer)->post(route('customer.order.confirm_received', $order));
        $this->assertEquals($expectedBalance, $this->store->fresh()->balance);
    }

    public function test_admin_warehouse_crud_and_toggle(): void
    {
        // Admin index
        $response = $this->actingAs($this->admin)->get(route('admin.warehouses.index'));
        $response->assertStatus(200);

        // Admin create warehouse
        $response = $this->actingAs($this->admin)->post(route('admin.warehouses.store'), [
            'code' => 'NDX-SBY-TEST',
            'name' => 'NDX Hub DC Rungkut',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'address' => 'Kawasan Industri SIER Surabaya',
            'lat' => -7.3200,
            'lng' => 112.7600,
            'phone' => '031-8430000',
            'pic_name' => 'Agus Wicaksono',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('warehouses', ['code' => 'NDX-SBY-TEST']);

        $warehouse = Warehouse::where('code', 'NDX-SBY-TEST')->first();

        // Toggle active status
        $this->actingAs($this->admin)->patch(route('admin.warehouses.toggle', $warehouse));
        $this->assertFalse($warehouse->fresh()->is_active);

        // Delete warehouse
        $response = $this->actingAs($this->admin)->delete(route('admin.warehouses.destroy', $warehouse));
        $response->assertRedirect();
        $this->assertDatabaseMissing('warehouses', ['code' => 'NDX-SBY-TEST']);
    }

    public function test_courier_api_flow_and_ndx_status_updates(): void
    {
        $warehouse = Warehouse::create([
            'code' => 'NDX-SBY-HUB',
            'name' => 'NDX Hub Surabaya',
            'city' => 'Surabaya',
            'province' => 'Jawa Timur',
            'address' => 'Jl. Basuki Rahmat',
            'lat' => -7.2600,
            'lng' => 112.7400,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'store_id' => $this->store->id,
            'invoice_number' => 'INV-COURIER-TEST',
            'total_amount' => 60000,
            'status' => 'processing',
            'shipping_status' => 'pickup_pending',
            'shipping_address' => "Penerima\n08123456\nJl. Raya No. 1, Surabaya",
        ]);

        // Courier fetch warehouses
        $response = $this->actingAs($this->courier, 'sanctum')->getJson('/api/v1/courier/warehouses');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Courier accept task
        $response = $this->actingAs($this->courier, 'sanctum')->postJson("/api/v1/courier/deliveries/{$order->id}/accept", [
            'lat' => -7.2575,
            'lng' => 112.7521,
        ]);

        $response->assertStatus(200);
        $freshOrder = $order->fresh();
        $this->assertEquals('shipped', $freshOrder->status);
        $this->assertEquals('picked_up', $freshOrder->shipping_status);
        $this->assertEquals($this->courier->id, $freshOrder->courier_id);

        // Courier complete delivery
        $response = $this->actingAs($this->courier, 'sanctum')->postJson("/api/v1/courier/deliveries/{$order->id}/complete", [
            'notes' => 'Diserahkan ke satpam rumah.',
        ]);

        $response->assertStatus(200);
        $freshOrder = $order->fresh();
        $this->assertEquals('completed', $freshOrder->status);
        $this->assertEquals('delivered', $freshOrder->shipping_status);
        $this->assertNotNull($freshOrder->seller_credited_at);
        $this->assertEquals(51000, $this->store->fresh()->balance); // 85% of 60000 = 51000
    }
}
