<?php

use PHPUnit\Framework\TestCase;

class RateLimiterTest extends TestCase
{
    private Logger $logger;

    protected function setUp(): void
    {
        $this->logger = new Logger(sys_get_temp_dir() . '/test_rate.log', false);
    }

    public function testFirstRequestIsAllowed(): void
    {
        $limiter = new RateLimiter(5, $this->logger);
        $this->assertTrue($limiter->checkLimit('client1'));
    }

    public function testRequestsAllowedUpToLimit(): void
    {
        $limiter = new RateLimiter(3, $this->logger);
        $this->assertTrue($limiter->checkLimit('client1'));
        $this->assertTrue($limiter->checkLimit('client1'));
        $this->assertTrue($limiter->checkLimit('client1'));
    }

    public function testRequestBlockedWhenLimitExceeded(): void
    {
        $limiter = new RateLimiter(2, $this->logger);
        $limiter->checkLimit('client1');
        $limiter->checkLimit('client1');
        $this->assertFalse($limiter->checkLimit('client1'));
    }

    public function testGetRemainingStartsAtLimit(): void
    {
        $limiter = new RateLimiter(10, $this->logger);
        $this->assertSame(10, $limiter->getRemaining('new_client'));
    }

    public function testGetRemainingDecreasesWithEachRequest(): void
    {
        $limiter = new RateLimiter(5, $this->logger);
        $limiter->checkLimit('client1');
        $this->assertSame(4, $limiter->getRemaining('client1'));
        $limiter->checkLimit('client1');
        $this->assertSame(3, $limiter->getRemaining('client1'));
    }

    public function testGetRemainingNeverGoesBelowZero(): void
    {
        $limiter = new RateLimiter(1, $this->logger);
        $limiter->checkLimit('client1');
        $limiter->checkLimit('client1'); // blocked
        $this->assertSame(0, $limiter->getRemaining('client1'));
    }

    public function testDifferentClientsHaveIndependentLimits(): void
    {
        $limiter = new RateLimiter(1, $this->logger);
        $limiter->checkLimit('clientA');
        $this->assertFalse($limiter->checkLimit('clientA'));
        $this->assertTrue($limiter->checkLimit('clientB'));
    }
}
