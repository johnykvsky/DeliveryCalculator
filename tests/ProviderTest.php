<?php

use PHPUnit\Framework\TestCase;

class ProviderTest extends TestCase
{
    public $provider;
    public $holidays;

    protected function setUp()
    {
        $this->provider = new johnykvsky\Utils\PolishDeliveryProvider();
        $this->holidays = array(
            0 => '2017-01-01',
            1 => '2017-01-06',
            2 => '2017-04-16',
            3 => '2017-04-17',
            4 => '2017-04-18',
            5 => '2017-05-01',
            6 => '2017-05-03',
            7 => '2017-06-04',
            8 => '2017-06-15',
            9 => '2017-08-15',
            10 => '2017-11-01',
            11 => '2017-11-11',
            12 => '2017-12-25',
            13 => '2017-12-26'
        );
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
        $this->assertEquals(array(0,6), $this->provider->getNonWorkingWeekDays());
    }

    public function testSetNonWorkingWeekDays()
    {
        $this->provider->setNonWorkingWeekDays(array(0,6));
        $this->assertEquals(array(0,6), $this->provider->getNonWorkingWeekDays());
    }

    public function testSetRegion()
    {
        $this->provider->setRegion('quacamole');
        $this->assertEquals('quacamole', $this->provider->region);
    }

    public function testGetHolidays()
    {
        $this->assertEquals($this->holidays, $this->provider->getHolidays('2017'));
    }

    public function testAddHoliday()
    {
        $reversed = array_reverse($this->holidays);
        $reversed[14] = '2017-08-01';
        $this->provider->addHoliday('2017-08-01');
        $this->assertEquals(array_reverse($reversed), $this->provider->getHolidays('2017'));
    }

    public function testAddRegionHolidays()
    {
        $this->assertEquals(null, $this->provider->addRegionHolidays('2017', '2017-04-16'));
    }
}
