<?php

use PHPUnit\Framework\TestCase;

class MailgunSenderTest extends TestCase
{
    private Logger $logger;

    protected function setUp(): void
    {
        $this->logger = new Logger(sys_get_temp_dir() . '/mailgun_test.log', false);
    }

    public function testNotConfiguredWhenBothKeysEmpty(): void
    {
        $sender = new MailgunSender([
            'mailgun_api_key' => '',
            'mailgun_domain'  => '',
            'mailgun_region'  => 'us',
            'from_email'      => 'noreply@example.com',
            'from_name'       => 'Test',
        ], $this->logger);

        $this->assertFalse($sender->isConfigured());
    }

    public function testNotConfiguredWhenApiKeyEmpty(): void
    {
        $sender = new MailgunSender([
            'mailgun_api_key' => '',
            'mailgun_domain'  => 'mg.example.com',
            'mailgun_region'  => 'us',
            'from_email'      => 'noreply@example.com',
            'from_name'       => 'Test',
        ], $this->logger);

        $this->assertFalse($sender->isConfigured());
    }

    public function testNotConfiguredWhenDomainEmpty(): void
    {
        $sender = new MailgunSender([
            'mailgun_api_key' => 'key-abc123',
            'mailgun_domain'  => '',
            'mailgun_region'  => 'us',
            'from_email'      => 'noreply@example.com',
            'from_name'       => 'Test',
        ], $this->logger);

        $this->assertFalse($sender->isConfigured());
    }

    public function testConfiguredWhenBothKeysSet(): void
    {
        $sender = new MailgunSender([
            'mailgun_api_key' => 'key-abc123',
            'mailgun_domain'  => 'mg.example.com',
            'mailgun_region'  => 'us',
            'from_email'      => 'noreply@example.com',
            'from_name'       => 'Test',
        ], $this->logger);

        $this->assertTrue($sender->isConfigured());
    }

    public function testSendReturnsFalseWhenNotConfigured(): void
    {
        $sender = new MailgunSender([
            'mailgun_api_key' => '',
            'mailgun_domain'  => '',
            'mailgun_region'  => 'us',
            'from_email'      => 'noreply@example.com',
            'from_name'       => 'Test',
        ], $this->logger);

        $result = $sender->send('user@example.com', 'Hello', 'Test body');
        $this->assertFalse($result);
    }
}
