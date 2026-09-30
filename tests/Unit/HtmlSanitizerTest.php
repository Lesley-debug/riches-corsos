<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_scripts_events_and_unsafe_urls(): void
    {
        $html = <<<'HTML'
<p onclick="alert(1)">Hello <strong>world</strong></p>
<script>alert('xss')</script>
<a href="javascript:alert(1)" target="_blank">Unsafe</a>
<a href="https://example.com" target="_blank">Safe</a>
HTML;

        $clean = (new HtmlSanitizer())->sanitize($html);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringContainsString('<strong>world</strong>', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }
}