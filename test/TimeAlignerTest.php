<?php

namespace SoftCreatR\SteamGuard\Test;

use SoftCreatR\SteamGuard\TimeAligner;

/**
 * This class tests the TimeAligner class. However, I'm actually too stupid to mock the external requests,
 * so this class is pretty useless right now.
 *
 * @coversDefaultClass \SoftCreatR\SteamGuard\TimeAligner
 */
class TimeAlignerTest extends TestCase
{
    private TimeAligner $timeAligner;

    public function setUp(): void
    {
        $this->timeAligner = new TimeAligner(
            $this->getHttpClient()
        );
    }

    public function testGetTimeDiff(): void
    {
        $this->markTestIncomplete();
    }

    public function testGetSteamTime(): void
    {
        $this->markTestIncomplete();
        /*$this->mockRequest('success');
        $this->assertEquals(1655007471, $this->timeAligner->getSteamTime());*/
    }
}
