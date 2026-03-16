<?php

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testValidRequestPassesValidation(): void
    {
        $result = $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => 'welcome',
            'variable_data' => ['name' => 'Alice'],
        ]);

        $this->assertSame('user123', $result['recipient_id']);
        $this->assertSame('welcome', $result['template_name']);
        $this->assertSame(['name' => 'Alice'], $result['variable_data']);
    }

    public function testMissingRecipientIdFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/recipient_id/');
        $this->validator->validateSendRequest([
            'template_name' => 'welcome',
            'variable_data' => [],
        ]);
    }

    public function testEmptyRecipientIdFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/recipient_id/');
        $this->validator->validateSendRequest([
            'recipient_id'  => '   ',
            'template_name' => 'welcome',
            'variable_data' => [],
        ]);
    }

    public function testRecipientIdTooLongFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/recipient_id/');
        $this->validator->validateSendRequest([
            'recipient_id'  => str_repeat('a', 256),
            'template_name' => 'welcome',
            'variable_data' => [],
        ]);
    }

    public function testMissingTemplateNameFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/template_name/');
        $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'variable_data' => [],
        ]);
    }

    public function testEmptyTemplateNameFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/template_name/');
        $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => '',
            'variable_data' => [],
        ]);
    }

    public function testInvalidTemplateNameFormatFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/template_name/');
        $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => 'has spaces!',
            'variable_data' => [],
        ]);
    }

    public function testTemplateNameAllowsHyphensAndUnderscores(): void
    {
        $result = $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => 'reset-password_v2',
            'variable_data' => [],
        ]);
        $this->assertSame('reset-password_v2', $result['template_name']);
    }

    public function testMissingVariableDataFails(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/variable_data/');
        $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => 'welcome',
        ]);
    }

    public function testVariableDataMustBeArray(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/variable_data/');
        $this->validator->validateSendRequest([
            'recipient_id'  => 'user123',
            'template_name' => 'welcome',
            'variable_data' => 'not-an-array',
        ]);
    }

    public function testValidRequestTrimsRecipientIdWhitespace(): void
    {
        $result = $this->validator->validateSendRequest([
            'recipient_id'  => '  user123  ',
            'template_name' => 'welcome',
            'variable_data' => [],
        ]);
        $this->assertSame('user123', $result['recipient_id']);
    }
}
