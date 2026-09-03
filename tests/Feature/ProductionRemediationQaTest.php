<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Voucher;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionRemediationQaTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $sellerUser;
    private Store $store;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->buyer = User::factory()->create([
            'email'             => 'buyer@example.com',
            'role'              => 'customer',
            'email_verified_at' => now(),
        ]);

        $this->sellerUser = User::factory()->create([
            'email'             => 'seller@example.com',
            'role'              => 'seller',
            'email_verified_at' => now(),
        ]);

        $this->store = Store::create([
            'user_id'             => $this->sellerUser->id,
            'name'                => 'Toko Serba Ada',
            'slug'                => 'toko-serba-ada',
            'province'            => 'Jawa Timur',
            'city'                => 'Surabaya',
            'district'            => 'Gubeng',
            'postal_code'         => '60281',
            'address'             => 'Jl. Gubeng Kertajaya',
            'status'              => 'approved',
            'balance'             => 100000,
            'bank_name'           => 'BCA',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Seller Name',
        ]);

        $this->category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);
    }

    /**
     * Test 1: Finite State Machine (FSM) Transition Rules
     */
    public function test_order_fsm_allows_valid_transitions_and_blocks_invalid_or_terminal_changes(): void
    {
        $order = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 50000,
            'status'           => Order::STATUS_PENDING,
            'shipping_address' => 'Surabaya',
        ]);

        $this->assertTrue($order->canTransitionTo(Order::STATUS_PROCESSING));
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DELIVERED));

        // Transition pending -> processing
        $order->transitionTo(Order::STATUS_PROCESSING);
        $this->assertEquals(Order::STATUS_PROCESSING, $order->status);

        // Transition processing -> shipped
        $order->transitionTo(Order::STATUS_SHIPPED);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);

        // Transition shipped -> completed
        $order->transitionTo(Order::STATUS_COMPLETED);
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
        $this->assertNotNull($order->completed_at);
        $this->assertTrue($order->isTerminal());

        // Terminal state cannot transition to cancelled or processing
        $this->assertFalse($order->canTransitionTo(Order::STATUS_CANCELLED));
        $this->expectException(\DomainException::class);
        $order->transitionTo(Order::STATUS_CANCELLED);
    }

    /**
     * Test 2: Double-Sell Prevention & Pre-decrement Stock Locking in Checkout
     */
    public function test_checkout_rejects_insufficient_stock_and_decrements_properly(): void
    {
        $product = Product::create([
            'store_id'    => $this->store->id,
            'category_id' => $this->category->id,
            'name'        => 'Mouse Gaming',
            'slug'        => 'mouse-gaming',
            'description' => 'Deskripsi Mouse Gaming high quality',
            'price'       => 150000,
            'stock'       => 2,
            'sold_count'  => 0,
            'is_active'   => true,
            'weight'      => 0.3,
        ]);

        Cart::create([
            'user_id'    => $this->buyer->id,
            'product_id' => $product->id,
            'quantity'   => 5, // Exceeds stock (2)
        ]);

        $response = $this->actingAs($this->buyer, 'sanctum')->postJson('/api/v1/orders/checkout', [
            'shipping_address' => 'Jl. Pemuda No. 1, Surabaya',
            'payment_method'   => 'QRIS',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('tidak mencukupi', $response->json('message'));
        $this->assertEquals(2, $product->fresh()->stock); // Stock remains untouched

        // Now test valid checkout: quantity = 2
        Cart::where('user_id', $this->buyer->id)->update(['quantity' => 2]);

        $validResponse = $this->actingAs($this->buyer, 'sanctum')->postJson('/api/v1/orders/checkout', [
            'shipping_address' => 'Jl. Pemuda No. 1, Surabaya',
            'payment_method'   => 'QRIS',
        ]);

        $validResponse->assertStatus(201);
        $this->assertEquals(0, $product->fresh()->stock); // Stock decremented atomically

        $order = Order::latest()->first();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);

        // Transition pending -> processing (paid)
        $order->transitionTo(Order::STATUS_PROCESSING);
        $this->assertEquals(2, $product->fresh()->sold_count);
    }

    /**
     * Test 3: Order Cancellation Automatically Restores Stock, Voucher Quota, and Refunds Wallet
     */
    public function test_order_cancellation_restores_stock_and_refunds_wallet(): void
    {
        $product = Product::create([
            'store_id'    => $this->store->id,
            'category_id' => $this->category->id,
            'name'        => 'Keyboard Mechanical',
            'slug'        => 'keyboard-mechanical',
            'description' => 'Keyboard mechanical RGB',
            'price'       => 500000,
            'stock'       => 10,
            'is_active'   => true,
        ]);

        $voucher = Voucher::create([
            'code'        => 'PROMO50K',
            'name'        => 'Promo Diskon 50K',
            'type'        => 'fixed',
            'amount'      => 50000,
            'min_spend'   => 100000,
            'quota'       => 5,
            'start_date'  => now()->subDay(),
            'end_date'    => now()->addDays(7),
            'is_active'   => true,
        ]);

        // Order is in PROCESSING status (was paid)
        $order = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 450000,
            'voucher_code'     => 'PROMO50K',
            'discount_amount'  => 50000,
            'status'           => Order::STATUS_PROCESSING,
            'shipping_address' => 'Surabaya',
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 2,
            'price'      => 500000,
        ]);

        // Stock was 8 after checkout
        $product->update(['stock' => 8, 'sold_count' => 2]);
        $voucher->update(['quota' => 4]);

        // Cancel order via WalletService
        WalletService::refundAndCancelOrder($order, 'Stok kosong atau permintaan pembeli');

        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertEquals(10, $product->fresh()->stock); // Stock restored
        $this->assertEquals(0, $product->fresh()->sold_count); // Sold count decremented
        $this->assertEquals(5, $voucher->fresh()->quota); // Voucher quota restored

        // Check wallet refund cache
        $wallet = Cache::get("wallet_data_{$this->buyer->id}");
        $this->assertNotNull($wallet);
        $this->assertEquals(450000, $wallet['balance']);
    }

    /**
     * Test 4: IDOR Protection on Order Endpoints
     */
    public function test_unauthorized_user_cannot_view_or_cancel_other_users_order(): void
    {
        $intruder = User::factory()->create([
            'email' => 'intruder@example.com',
            'role'  => 'customer',
        ]);

        $order = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 100000,
            'status'           => Order::STATUS_PENDING,
            'shipping_address' => 'Surabaya',
        ]);

        // Intruder tries to view buyer's order via API
        $responseView = $this->actingAs($intruder, 'sanctum')->getJson("/api/v1/orders/{$order->uuid}");
        $responseView->assertStatus(404);

        // Intruder tries to cancel buyer's order via API
        $responseCancel = $this->actingAs($intruder, 'sanctum')->postJson("/api/v1/orders/{$order->uuid}/cancel");
        $responseCancel->assertStatus(404);

        // Intruder tries to cancel buyer's order via Web
        $webCancel = $this->actingAs($intruder)->post("/customer/orders/{$order->id}/cancel");
        $webCancel->assertStatus(403);
    }

    /**
     * Test 5: Strict File Upload MIME Validation
     */
    public function test_upload_endpoints_reject_invalid_mime_types(): void
    {
        Storage::fake('public');

        // Text or php file pretending to be image
        $fakeFile = UploadedFile::fake()->create('exploit.txt', 500, 'text/plain');

        // Customer complaint upload
        $order = Order::create([
            'user_id'          => $this->buyer->id,
            'store_id'         => $this->store->id,
            'total_amount'     => 100000,
            'status'           => Order::STATUS_COMPLETED,
            'shipping_address' => 'Surabaya',
        ]);

        $response = $this->actingAs($this->buyer)
            ->from("/customer/orders/{$order->id}")
            ->post("/customer/orders/{$order->id}/complaints", [
                'reason'      => 'Barang Rusak',
                'description' => 'Produk patah saat pengiriman',
                'photo'       => $fakeFile,
            ]);

        $response->assertSessionHasErrors('photo');
    }
}
