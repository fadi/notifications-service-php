<?php

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    private string $logFile;

    protected function setUp(): void
    {
        $this->logFile = sys_get_temp_dir() . '/notification_test_' . uniqid() . '.log';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testInfoWritesToLogFile(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->info('Test info message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('INFO', $content);
        $this->assertStringContainsString('Test info message', $content);
    }

    public function testErrorWritesToLogFile(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->error('Something went wrong');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('ERROR', $content);
        $this->assertStringContainsString('Something went wrong', $content);
    }

    public function testWarningWritesToLogFile(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->warning('Watch out');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('WARNING', $content);
        $this->assertStringContainsString('Watch out', $content);
    }

    public function testDebugNotWrittenWhenDebugModeOff(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->debug('Debug message');

        $this->assertFileDoesNotExist($this->logFile);
    }

    public function testDebugWrittenWhenDebugModeOn(): void
    {
        $logger = new Logger($this->logFile, true);
        $logger->debug('Verbose debug output');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('DEBUG', $content);
        $this->assertStringContainsString('Verbose debug output', $content);
    }

    public function testContextIsSerializedToLog(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->info('With context', ['user_id' => 'abc123', 'action' => 'login']);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('abc123', $content);
        $this->assertStringContainsString('login', $content);
    }

    public function testLogEntryIncludesTimestamp(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->info('Timestamped');

        $content = file_get_contents($this->logFile);
        // Timestamp format: [2026-03-16 12:00:00]
        $this->assertMatchesRegularExpression('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', $content);
    }

    public function testMultipleLogEntriesAppend(): void
    {
        $logger = new Logger($this->logFile, false);
        $logger->info('First message');
        $logger->info('Second message');

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('First message', $content);
        $this->assertStringContainsString('Second message', $content);
    }
}
