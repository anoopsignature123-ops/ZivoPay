<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Shop\Address;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopWalletFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_package_activation_moves_the_fee_to_shopping_wallet_without_repeating_credit(): void
    {
        $user = $this->createUser(['deposit_wallet' => 4000]);
        $service = new SubscriptionService;

        $this->assertTrue($service->activateSubscription($user));
        $this->assertFalse($service->activateSubscription($user->fresh()));

        $user->refresh();
        $this->assertSame(1000.0, (float) $user->deposit_wallet);
        $this->assertSame(3000.0, (float) $user->shopping_wallet);
        $this->assertTrue($user->is_subscription_active);
        $this->assertSame(1, Transaction::query()->where('user_id', $user->id)->where('type', 'subscription')->count());
        $this->assertSame(1, Transaction::query()->where('user_id', $user->id)->where('type', 'shopping_wallet_credit')->count());
        $this->assertSame(0, Transaction::query()->where('user_id', $user->id)->where('type', 'subscription_level')->count());
    }

    public function test_package_activation_keeps_the_existing_referral_level_rules(): void
    {
        $directSponsor = $this->createUser(['is_subscription_active' => true]);
        $upperSponsor = $this->createUser(['is_subscription_active' => true]);
        $directSponsor->sponsor_code = $upperSponsor->referral_code;
        $directSponsor->save();
        $user = $this->createUser([
            'deposit_wallet' => 3000,
            'sponsor_code' => $directSponsor->referral_code,
        ]);

        $this->assertTrue((new SubscriptionService)->activateSubscription($user));

        $this->assertSame(0.0, (float) $directSponsor->fresh()->earning_wallet);
        $this->assertSame(240.0, (float) $upperSponsor->fresh()->earning_wallet);
    }

    public function test_shop_checkout_applies_the_flat_discount_and_debits_only_shopping_wallet(): void
    {
        config([
            'shop.shopping_discount_threshold' => 1000,
            'shop.shopping_discount_amount' => 200,
            'shop.free_delivery_threshold' => 999,
            'shop.delivery_fee' => 0,
        ]);
        $user = $this->createUser();
        $user->shopping_wallet = 1000;
        $user->save();
        $address = $this->createAddress($user);
        $product = $this->createProduct();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/shop/place/orders', [
            'address_id' => $address->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertCreated()->assertJsonPath('data.order.subtotal', 1000)
            ->assertJsonPath('data.order.shopping_discount', 200)
            ->assertJsonPath('data.order.total', 800)
            ->assertJsonPath('data.order.payment_method', 'shopping_wallet')
            ->assertJsonPath('data.wallets.shopping_wallet', 200)
            ->assertJsonPath('data.wallets.currency', 'INR');
        $this->assertSame(200.0, (float) $user->fresh()->shopping_wallet);
        $this->assertSame(0.0, (float) $user->fresh()->deposit_wallet);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 800,
            'wallet_type' => 'shopping_wallet',
            'type' => 'shop_order',
            'trx_type' => '-',
        ]);
        $this->assertSame(9, (int) $product->fresh()->stock);
    }

    public function test_checkout_with_insufficient_shopping_wallet_creates_no_order_or_debit(): void
    {
        $user = $this->createUser();
        $user->shopping_wallet = 799.99;
        $user->save();
        $address = $this->createAddress($user);
        $product = $this->createProduct();
        Sanctum::actingAs($user);

        $this->postJson('/api/shop/place/orders', [
            'address_id' => $address->id,
            'payment_method' => 'shopping_wallet',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertUnprocessable()->assertJsonValidationErrors('payment_method');

        $this->assertDatabaseMissing('shop_orders', ['user_id' => $user->id]);
        $this->assertSame(799.99, (float) $user->fresh()->shopping_wallet);
        $this->assertSame(10, (int) $product->fresh()->stock);
    }

    public function test_paid_shop_order_cancellation_refunds_shopping_wallet_only_once(): void
    {
        config([
            'shop.shopping_discount_threshold' => 1000,
            'shop.shopping_discount_amount' => 200,
            'shop.free_delivery_threshold' => 999,
            'shop.delivery_fee' => 0,
        ]);
        $user = $this->createUser();
        $user->shopping_wallet = 1000;
        $user->save();
        $address = $this->createAddress($user);
        $product = $this->createProduct();
        Sanctum::actingAs($user);
        $orderId = $this->postJson('/api/shop/place/orders', [
            'address_id' => $address->id,
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertCreated()->json('data.order.id');

        $this->postJson('/api/shop/orders-cancel', ['order_id' => $orderId])
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'refunded');

        $this->assertSame(1000.0, (float) $user->fresh()->shopping_wallet);
        $this->assertSame(0.0, (float) $user->fresh()->deposit_wallet);
        $this->assertSame(10, (int) $product->fresh()->stock);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 800,
            'wallet_type' => 'shopping_wallet',
            'type' => 'shop_order_refund',
            'trx_type' => '+',
        ]);
        $this->postJson('/api/shop/orders-cancel', ['order_id' => $orderId])->assertUnprocessable();
        $this->assertSame(1, Transaction::query()->where('user_id', $user->id)->where('type', 'shop_order_refund')->count());
    }

    public function test_empty_order_list_succeeds_and_missing_order_has_a_clean_json_404(): void
    {
        $user = $this->createUser();
        Sanctum::actingAs($user);

        $this->getJson('/api/shop/orders')->assertOk()->assertJsonPath('data.items', []);

        $this->getJson('/api/shop/orders/99999999')->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'The requested resource was not found.',
                'errors' => ['resource' => ['The requested record does not exist or is unavailable.']],
            ])
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('trace');
    }

    public function test_all_product_endpoints_include_the_authenticated_shopping_wallet_balance(): void
    {
        $user = $this->createUser();
        $user->shopping_wallet = 1234.50;
        $user->save();
        $product = $this->createProduct();
        Sanctum::actingAs($user);

        $this->getJson('/api/shop/products')->assertOk()->assertJsonPath('data.wallets.shopping_wallet', 1234.5);
        $this->getJson('/api/shop/products/search?keyword=Test')->assertOk()->assertJsonPath('data.wallets.shopping_wallet', 1234.5);
        $this->getJson('/api/shop/products/'.$product->id)->assertOk()->assertJsonPath('data.wallets.shopping_wallet', 1234.5);
        $this->getJson('/api/shop/categories/'.$product->category_id.'/products')
            ->assertOk()->assertJsonPath('data.wallets.shopping_wallet', 1234.5);
    }

    private function createUser(array $attributes = []): User
    {
        $role = Role::query()->firstOrCreate(['slug' => 'member'], ['name' => 'Member']);

        return User::factory()->create(['role_id' => $role->id, ...$attributes]);
    }

    private function createAddress(User $user): Address
    {
        return Address::query()->create([
            'user_id' => $user->id,
            'recipient_name' => 'Test Buyer',
            'phone' => '9876543210',
            'address_line1' => '1 Test Street',
            'city' => 'Delhi',
            'state' => 'Delhi',
            'postal_code' => '110001',
            'country' => 'India',
        ]);
    }

    private function createProduct(): Product
    {
        $category = Category::query()->create(['name' => 'Test Category', 'slug' => fake()->unique()->slug()]);

        return Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => fake()->unique()->slug(),
            'sku' => fake()->unique()->bothify('SKU-#####'),
            'price' => 1000,
            'mrp' => 1200,
            'stock' => 10,
            'is_active' => true,
        ]);
    }
}
