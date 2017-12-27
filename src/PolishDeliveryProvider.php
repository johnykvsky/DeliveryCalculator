<?php

namespace johnykvsky\Utils;

/**
 * Calculate delivery time, only workdays
 */
class PolishDeliveryProvider implements \johnykvsky\Utils\DeliveryProviderInterface
{
    /**
     * @var string $timezone Timezone name
     */
    public $timezone = 'Europe/Warsaw';

    /**
     * @var array $holidays Holidays dates
     */
    public $holidays = [];

    /**
     * @var array $nonWorkingDays Non working days (weekends)
     */
    public $nonWorkingDays = array('0','6'); //no delivery on saturday 6, and sunday 0

    /**
     * @var string $region Region to deliver
     */
    public $region;

    /**
      * {@inheritdoc}
      */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
      * {@inheritdoc}
      */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
      * {@inheritdoc}
      */
    public function addHoliday($date)
    {
        $this->holidays[] = $date;
    }

    /**
      * {@inheritdoc}
      */
    public function getDateTimeZone()
    {
        return new \DateTimeZone($this->timezone);
    }

    /**
      * {@inheritdoc}
      */
    public function getNonWorkingWeekDays()
    {
        return $this->nonWorkingDays;
    }

    /**
      * {@inheritdoc}
      */
    public function setNonWorkingWeekDays($nonWorkingWeekDays)
    {
        $this->nonWorkingDays = $nonWorkingWeekDays;
    }

    /**
      * {@inheritdoc}
      */
    public function getHolidays($year)
    {
        $timezone = $this->getDateTimeZone();
        $date = \DateTime::createFromFormat('Y-m-d', $year.'-01-01', $timezone);
        $year = $date->format('Y');

        $easter = easter_date($year);

        $this->addHoliday($year.'-01-01'); //Nowy Rok, Świętej Bożej Rodzicielki
        $this->addHoliday($year.'-01-06'); //Trzech Króli (Objawienie Pańskie)
        $this->addHoliday(date('Y-m-d', $easter)); //Wielkanocna sobota, ruchome święto
        $this->addHoliday(date('Y-m-d', strtotime('+1 day', $easter))); //Wielkanocna niedziela, ruchome święto
        $this->addHoliday(date('Y-m-d', strtotime('+2 day', $easter))); //Poniedziałek Wielkanocny
        $this->addHoliday($year.'-05-01'); //Święto Pracy
        $this->addHoliday($year.'-05-03'); //Święto Konstytucji 3 Maja
        $this->addHoliday(date('Y-m-d', strtotime('+49 days', $easter))); //Zesłanie Ducha Świętego (Zielone Świątki), 49 dni po niedzieli wielkanocnej
        $this->addHoliday(date('Y-m-d', strtotime('+60 days', $easter))); //Boże Ciało, 60 dni po niedzieli wielkanocnej
        $this->addHoliday($year.'-08-15'); //Święto Wojska Polskiego, Wniebowzięcie Najświętszej Maryi Panny
        $this->addHoliday($year.'-11-01'); //Wszystkich Świętych
        $this->addHoliday($year.'-11-11'); //Święto Niepodległości
        $this->addHoliday($year.'-12-25'); //Boże Narodzenie (pierwszy dzień)
        $this->addHoliday($year.'-12-26'); //Boże Narodzenie (pierwszy dzień)

        $this->addRegionHolidays($year, $easter);

        return $this->holidays;
    }

    /**
      * {@inheritdoc}
      */
    public function addRegionHolidays($year, $easter)
    {
        return;
    }
}
