<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public $provider;
    public $holidays;

    protected function setUp(): void
    {
        $this->provider = new johnykvsky\Utils\PolishDeliveryProvider();
        $this->holidays = [
            '2017-01-01',
            '2017-01-06',
            '2017-04-16',
            '2017-04-17',
            '2017-05-01',
            '2017-05-03',
            '2017-06-04',
            '2017-06-15',
            '2017-08-15',
            '2017-11-01',
            '2017-11-11',
            '2017-12-25',
            '2017-12-26'
        ];
    }

    public function testSetTimezone()
    {
        $this->provider->setTimezone('Europe/Warsaw');
        $this->assertEquals('Europe/Warsaw', $this->provider->timezone);
    }

    public function testGetDateTimeZone()
    {
        $timezone =  new \DateTimeZone('Europe/Warsaw');
        $this->assertEquals($timezone, $this->provider->getDateTimeZone());
    }

    public function testGetNonWorkingWeekDays()
    {
        $this->assertEquals([0,6], $this->provider->getNonWorkingWeekDays());
    }

    public function testSetNonWorkingWeekDays()
    {
        $this->provider->setNonWorkingWeekDays(array(0,6));
        $this->assertEquals([0,6], $this->provider->getNonWorkingWeekDays());
    }

    public function testSetRegion()
    {
        $this->provider->setRegion('quacamole');
        $this->assertEquals('quacamole', $this->provider->region);
    }

    public function testGetHolidays()
    {
        $this->assertEquals($this->holidays, $this->provider->getHolidays(2017));
    }

    public function testAddHoliday()
    {
        $reversed = array_reverse($this->holidays);
        $reversed[14] = '2017-08-01';
        $this->provider->addHoliday('2017-08-01');
        $this->assertEquals(array_reverse($reversed), $this->provider->getHolidays(2017));
    }

    public function testAddRegionHolidays()
    {
        $this->assertEquals(null, $this->provider->addRegionHolidays(2017));
    }
}
