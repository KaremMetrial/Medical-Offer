<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletCurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected $usdCountry;
    protected $sarCountry;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test countries
        $this->usdCountry = Country::create([
            'phone_code' => '+1',
            'currency_symbol' => '$',
            'currency_name' => 'US Dollar',
            'currency_unit' => 'USD',
            'currency_factor' => 100,
            'flag' => 'flags/us.png',
            'timezone' => 'America/New_York',
            'is_active' => true
        ]);

        $this->sarCountry = Country::create([
            'phone_code' => '+966',
            'currency_symbol' => 'ر.س',
            'currency_name' => 'Saudi Riyal',
            'currency_unit' => 'SAR',
            'currency_factor' => 100,
            'flag' => 'flags/sa.png',
            'timezone' => 'Asia/Riyadh',
            'is_active' => true
        ]);

        // Create test user with USD country
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'country_id' => $this->usdCountry->id,
            'balance' => 100.00 // 100 USD
        ]);
    }

    /**
     * Test wallet balance without x-cuntry-id header (uses user's country)
     */
    public function test_wallet_balance_without_header_uses_user_country()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/wallet');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'balance' => [
                        'balance' => 100.00,
                        'currency_symbol' => '$',
                        'currency_unit' => 'USD',
                        'country_id' => $this->usdCountry->id,
                        'country_name' => $this->usdCountry->name
                    ]
                ]
            ]);
    }

    /**
     * Test wallet balance with x-cuntry-id header (overrides user's country)
     */
    public function test_wallet_balance_with_header_uses_override_country()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->withHeaders([
                'x-country-id' => $this->sarCountry->id
            ])
            ->getJson('/api/v1/wallet');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'balance' => [
                        'currency_symbol' => 'ر.س',
                        'currency_unit' => 'SAR',
                        'country_id' => $this->sarCountry->id,
                        'country_name' => $this->sarCountry->name
                    ]
                ]
            ]);

        // The balance should be converted from USD to SAR
        // Assuming 1 USD = 3.75 SAR (approximate rate)
        $responseData = $response->json();
        $this->assertEquals(375.00, $responseData['data']['balance']['balance']);
    }

    /**
     * Test wallet transactions without x-cuntry-id header
     */
    public function test_wallet_transactions_without_header()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/wallet/transactions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'transactions' => []
                ]
            ]);
    }

    /**
     * Test wallet transactions with x-cuntry-id header
     */
    public function test_wallet_transactions_with_header()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->withHeaders([
                'x-country-id' => $this->sarCountry->id
            ])
            ->getJson('/api/v1/wallet/transactions');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'transactions' => []
                ]
            ]);
    }

    /**
     * Test invalid x-cuntry-id header falls back to user's country
     */
    public function test_invalid_country_id_falls_back_to_user_country()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->withHeaders([
                'x-country-id' => 99999 // Invalid country ID
            ])
            ->getJson('/api/v1/wallet');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'balance' => [
                        'balance' => 100.00,
                        'currency_symbol' => '$',
                        'currency_unit' => 'USD',
                        'country_id' => $this->usdCountry->id,
                        'country_name' => $this->usdCountry->name
                    ]
                ]
            ]);
    }
}