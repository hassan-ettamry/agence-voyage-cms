<?php

namespace Tests\Feature;

use App\Services\SecurityEventLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\TestCase;

class SecurityHeadersAndErrorsTest extends TestCase
{
    public function test_security_headers_are_added_without_breaking_same_origin_builder_frames(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeader(
            'Content-Security-Policy',
            "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'"
        );
        $this->assertTrue(Str::isUuid((string) $response->headers->get('X-Request-ID')));
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_hsts_is_sent_only_over_https(): void
    {
        $this->get('/login')->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/login')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_custom_error_pages_do_not_expose_internal_exception_messages(): void
    {
        config(['app.debug' => false]);
        Route::middleware('web')->get('/_security-test/server-error', function () {
            throw new RuntimeException('TOP_SECRET_INTERNAL_DETAIL');
        });

        $this->get('/_security-test/missing')
            ->assertNotFound()
            ->assertSee('Page introuvable');

        $this->get('/_security-test/server-error')
            ->assertStatus(500)
            ->assertSee('Une erreur est survenue')
            ->assertDontSee('TOP_SECRET_INTERNAL_DETAIL');
    }

    public function test_security_logger_redacts_sensitive_context(): void
    {
        $logPath = tempnam(sys_get_temp_dir(), 'security-log-test-');
        config([
            'logging.channels.security' => [
                'driver' => 'single',
                'path' => $logPath,
                'level' => 'info',
                'replace_placeholders' => true,
            ],
        ]);
        Log::forgetChannel('security');

        $request = Request::create('/account/security/password', 'POST');
        $request->attributes->set('request_id', (string) Str::uuid());

        app(SecurityEventLogger::class)->record('account.security_test', null, $request, [
            'password' => 'NeverLogThisPassword',
            'reset_token' => 'NeverLogThisToken',
            'result' => 'accepted',
        ]);
        Log::forgetChannel('security');

        $contents = file_get_contents($logPath);

        $this->assertIsString($contents);
        $this->assertStringContainsString('account.security_test', $contents);
        $this->assertStringContainsString('[REDACTED]', $contents);
        $this->assertStringContainsString('accepted', $contents);
        $this->assertStringNotContainsString('NeverLogThisPassword', $contents);
        $this->assertStringNotContainsString('NeverLogThisToken', $contents);

        @unlink($logPath);
    }
}
