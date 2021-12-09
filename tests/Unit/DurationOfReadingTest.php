<?php

namespace Tests\Unit;

use App\Helpers\DurationOfReading;
use PHPUnit\Framework\TestCase;

class DurationOfReadingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCanGetDurationOfReadingText()
    {
        $text = 'this is for test';

        $dor = new DurationOfReading($text);

        $this->assertEquals(4, $dor->getTimePerSeconds());
        $this->assertEquals(4/60, $dor->getTimePerMinute());
    }
}
