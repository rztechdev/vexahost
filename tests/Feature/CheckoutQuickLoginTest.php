<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\VpsSpec;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckoutQuickLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_checkout_page_renders_tab_switcher_for_guest(): void
    {
        $response = $this->get(route('checkout'));

        $response->assertStatus(200);
        $response->assertSee('1. Buat Akun Baru');
        $response->assertSee('2. Sudah Punya Akun (Masuk)');
        $response->assertSee('Masuk dengan Akun Google');
        $response->assertSee('performQuickLogin');
        $response->assertSee('saveDraft');
        $response->assertSee('restoreDraft');
    }

    public function test_quick_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson(route('checkout.quick-login'), [
            'login' => 'nonexistent@example.com',
            'password' => 'wrongpassword123',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Email/username atau password tidak sesuai.',
        ]);
        $this->assertFalse(Auth::check());
    }

    public function test_quick_login_succeeds_with_valid_email(): void
    {
        $user = User::create([
            'username' => 'existingclient',
            'email' => 'client@vexahostcloud.my.id',
            'full_name' => 'Existing Client',
            'phone' => '081234567890',
            'password' => Hash::make('SecretPass123!'),
            'channel' => 'website',
            'is_admin' => false,
        ]);

        $response = $this->postJson(route('checkout.quick-login'), [
            'login' => 'client@vexahostcloud.my.id',
            'password' => 'SecretPass123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login berhasil.',
            'user' => [
                'id' => $user->id,
                'full_name' => 'Existing Client',
                'email' => 'client@vexahostcloud.my.id',
                'phone' => '081234567890',
            ],
        ]);
        $response->assertJsonStructure(['csrf_token']);
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    public function test_quick_login_succeeds_with_valid_username(): void
    {
        $user = User::create([
            'username' => 'nickdev',
            'email' => 'nick@vexahostcloud.my.id',
            'full_name' => 'Nick Dev',
            'password' => Hash::make('NickPass123!'),
            'channel' => 'website',
            'is_admin' => false,
        ]);

        $response = $this->postJson(route('checkout.quick-login'), [
            'login' => 'nickdev',
            'password' => 'NickPass123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Login berhasil.',
        ]);
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    public function test_checkout_allows_guest_to_specify_custom_username(): void
    {
        $spec = VpsSpec::where('is_active', true)->first();

        $payload = [
            'vps_spec_id' => $spec->id,
            'payment_method' => 'qris',
            'full_name' => 'Budi Pratama',
            'username' => 'budipratama99',
            'email' => 'budi.pratama99@example.com',
            'password' => 'BudiSecure123!',
            'hostname' => 'vx-node-budi',
            'root_password' => 'RootBudi123!',
            'os' => 'ubuntu2404',
            'provider' => $spec->defaultProvider(),
            'datacenter_location' => 'indonesia',
            'control_panel' => 'none',
        ];

        $response = $this->postJson(route('order.store'), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['order_id']);

        $createdUser = User::where('email', 'budi.pratama99@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertSame('budipratama99', $createdUser->username);
        $this->assertSame('Budi Pratama', $createdUser->full_name);
    }
}
