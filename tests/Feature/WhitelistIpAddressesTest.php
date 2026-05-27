<?php

namespace Tests\Feature;

use App\Http\Middleware\WhitelistIpAddresses;
use App\Models\AllowedIp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class WhitelistIpAddressesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(WhitelistIpAddresses::class)
            ->get('/_test/whitelist', fn (): array => ['ok' => true]);
    }

    public function test_empty_whitelist_blocks_all_requests(): void
    {
        $this->getFromIp('203.0.113.10')->assertForbidden();
    }

    public function test_exact_ip_match_is_allowed(): void
    {
        AllowedIp::factory()->create(['ip_address' => '203.0.113.10']);

        $this->getFromIp('203.0.113.10')->assertOk();
    }

    public function test_ip_not_in_list_is_blocked(): void
    {
        AllowedIp::factory()->create(['ip_address' => '203.0.113.10']);

        $this->getFromIp('198.51.100.7')->assertForbidden();
    }

    public function test_cidr_range_match_is_allowed(): void
    {
        AllowedIp::factory()->create(['ip_address' => '10.0.0.0/24']);

        $this->getFromIp('10.0.0.5')->assertOk();
        $this->getFromIp('10.0.1.1')->assertForbidden();
    }

    public function test_inactive_entries_are_ignored(): void
    {
        AllowedIp::factory()->inactive()->create(['ip_address' => '203.0.113.10']);

        $this->getFromIp('203.0.113.10')->assertForbidden();
    }

    public function test_hostname_entry_resolves_via_dns(): void
    {
        $localhostIps = gethostbynamel('localhost');

        if ($localhostIps === false || ! in_array('127.0.0.1', $localhostIps, true)) {
            $this->markTestSkipped('Host system does not resolve localhost to 127.0.0.1.');
        }

        AllowedIp::factory()->create(['ip_address' => 'localhost']);

        $this->getFromIp('127.0.0.1')->assertOk();
        $this->getFromIp('203.0.113.10')->assertForbidden();
    }

    public function test_unresolvable_hostname_does_not_crash_the_middleware(): void
    {
        AllowedIp::factory()->create(['ip_address' => 'no-such-host.invalid']);

        $this->getFromIp('203.0.113.10')->assertForbidden();
    }

    public function test_saving_an_allowed_ip_flushes_the_cache(): void
    {
        AllowedIp::activeList();
        $this->assertTrue(Cache::has(AllowedIp::CACHE_KEY));

        AllowedIp::factory()->create(['ip_address' => '203.0.113.10']);

        $this->assertFalse(Cache::has(AllowedIp::CACHE_KEY));
    }

    public function test_deleting_an_allowed_ip_flushes_the_cache(): void
    {
        $ip = AllowedIp::factory()->create(['ip_address' => '203.0.113.10']);
        AllowedIp::activeList();
        $this->assertTrue(Cache::has(AllowedIp::CACHE_KEY));

        $ip->delete();

        $this->assertFalse(Cache::has(AllowedIp::CACHE_KEY));
    }

    private function getFromIp(string $ip): TestResponse
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->get('/_test/whitelist');
    }
}
