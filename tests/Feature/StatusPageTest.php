<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function assertNoEmojis(string $content): void
    {
        // Unicode ranges for common emojis
        $emojiPattern = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';
        $this->assertSame(
            0,
            preg_match($emojiPattern, $content),
            'Status page contains forbidden emoji characters'
        );
    }

    public function test_status_page_loads_and_renders_enterprise_saas_metrics_without_emojis(): void
    {
        $response = $this->get('/status');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertNoEmojis($content);

        // Core Status & Uptime Header
        $response->assertSee('Status Sistem &amp; Infrastruktur Real-Time', false);
        $response->assertSee('Semua Sistem Beroperasi Normal');
        $response->assertSee('99.98%');
        $response->assertSee('RZ Digital Creative');

        // Infrastructure Components
        $response->assertSee('Datacenter Singapore (Cluster SG-01)');
        $response->assertSee('Datacenter Jakarta (Cluster JKT-01)');
        $response->assertSee('NVMe PCIe Gen4 RAID-10 Storage Array');
        $response->assertSee('Anti-DDoS Scrubbing &amp; Filtering Engine', false);

        // Peering & Networks
        $response->assertSee('Jakarta Peering Exchange (OpenIXP &amp; IIX-APJII)', false);
        $response->assertSee('Singapore Global Transit &amp; Equinix IX', false);
        $response->assertSee('Anycast DNS Resolver &amp; Authoritative Nameservers', false);

        // Platform & Gateways
        $response->assertSee('Customer Dashboard &amp; Web Control Portal', false);
        $response->assertSee('Automated KVM Provisioning Engine');
        $response->assertSee('Billing &amp; Payment Gateway (Lynk, QRIS, Shopee)', false);

        // Databases & AI Stacks
        $response->assertSee('Managed Database Engine (PostgreSQL 16, MariaDB 11, Redis 7)');
        $response->assertSee('AI Agent Runtime &amp; Docker Engine Environment', false);

        // Incident Log
        $response->assertSee('Riwayat Pemeliharaan &amp; Catatan Insiden', false);
        $response->assertSee('Gedung Cyber 1 Jakarta');
        $response->assertSee('[TERSELESAIKAN]');

        // Links
        $response->assertSee(route('sla'));
        $response->assertSee(route('docs'));
    }
}
