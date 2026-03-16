<?php

use PHPUnit\Framework\TestCase;

class TemplateEngineTest extends TestCase
{
    private TemplateEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new TemplateEngine(
            [
                'welcome'        => 'Hi {{ name }}, welcome to {{ product }}!',
                'reset_password' => 'Hello {{ name }}, reset your password using this code: {{ code }}',
                'invoice_ready'  => 'Hi {{ name }}, your invoice {{ invoice_id }} is ready. Total: {{ total }}',
            ],
            [
                'welcome'        => 'Welcome to {{ product }}!',
                'reset_password' => 'Password Reset Code',
            ]
        );
    }

    public function testRenderReplacesVariables(): void
    {
        $result = $this->engine->render('welcome', ['name' => 'Alice', 'product' => 'HealthApp']);
        $this->assertSame('Hi Alice, welcome to HealthApp!', $result);
    }

    public function testRenderHandlesMultipleVariables(): void
    {
        $result = $this->engine->render('reset_password', ['name' => 'Bob', 'code' => '99321']);
        $this->assertSame('Hello Bob, reset your password using this code: 99321', $result);
    }

    public function testRenderThrowsForUnknownTemplate(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches("/Template 'nonexistent' not found/");
        $this->engine->render('nonexistent', []);
    }

    public function testRenderEscapesHtmlInVariables(): void
    {
        $result = $this->engine->render('welcome', ['name' => '<script>alert(1)</script>', 'product' => 'App']);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function testRenderSubjectWithDefinedSubjectTemplate(): void
    {
        $result = $this->engine->renderSubject('welcome', ['product' => 'HealthApp']);
        $this->assertSame('Welcome to HealthApp!', $result);
    }

    public function testRenderSubjectWithLiteralSubjectTemplate(): void
    {
        $result = $this->engine->renderSubject('reset_password', []);
        $this->assertSame('Password Reset Code', $result);
    }

    public function testRenderSubjectFallsBackToFormattedTemplateName(): void
    {
        $result = $this->engine->renderSubject('invoice_ready', []);
        $this->assertSame('Invoice ready', $result);
    }

    public function testExistsReturnsTrueForKnownTemplate(): void
    {
        $this->assertTrue($this->engine->exists('welcome'));
        $this->assertTrue($this->engine->exists('reset_password'));
        $this->assertTrue($this->engine->exists('invoice_ready'));
    }

    public function testExistsReturnsFalseForUnknownTemplate(): void
    {
        $this->assertFalse($this->engine->exists('no_such_template'));
    }

    public function testRenderWithNoVariablesLeavesPlaceholders(): void
    {
        $result = $this->engine->render('welcome', []);
        $this->assertSame('Hi {{ name }}, welcome to {{ product }}!', $result);
    }
}
