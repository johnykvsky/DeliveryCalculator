<?php

namespace johnykvsky\Utils;

use johnykvsky\Utils\Exception\InvalidDateTimezoneException;

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
    public $nonWorkingDays = [0,6]; //no delivery on saturday 6, and sunday 0

    /**
     * @var string $region Region to deliver
     */
    public $region;

    /**
      * {@inheritdoc}
      */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
      * {@inheritdoc}
      */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
      * {@inheritdoc}
      */
    public function addHoliday(string $date): void
    {
        $this->holidays[] = $date;
    }

    /**
      * {@inheritdoc}
      */
    public function getDateTimeZone(): \DateTimeZone
    {
        return new \DateTimeZone($this->timezone);
    }

    /**
      * {@inheritdoc}
      */
    public function getNonWorkingWeekDays(): array
    {
        return $this->nonWorkingDays;
    }

    /**
      * {@inheritdoc}
      */
    public function setNonWorkingWeekDays(array $nonWorkingWeekDays): void
    {
        $this->nonWorkingDays = $nonWorkingWeekDays;
    }

    /**
      * {@inheritdoc}
      */
    public function getHolidays(int $year): array
    {
        $timezone = $this->getDateTimeZone();
        $date = \DateTime::createFromFormat('Y-m-d', $year.'-01-01', $timezone);

        if (empty($date)) {
            throw new InvalidDateTimezoneException('Invalid shippingDate or shippingTimezone');
        }
        
        $year = (int) $date->format('Y');
       
        $easterDateTime = new \DateTime('@' . easter_date($year));
        $easterDateTime->setTimezone($timezone);

        $this->addHoliday($year.'-01-01'); //Nowy Rok, Świętej Bożej Rodzicielki
        $this->addHoliday($year.'-01-06'); //Trzech Króli (Objawienie Pańskie)
        $this->addHoliday($easterDateTime->format('Y-m-d')); //Wielkanocna niedziela, ruchome święto
        $this->addHoliday($easterDateTime->add(new \DateInterval('P1D'))->format('Y-m-d')); //Poniedziałek Wielkanocny
        $this->addHoliday($year.'-05-01'); //Święto Pracy
        $this->addHoliday($year.'-05-03'); //Święto Konstytucji 3 Maja
        $this->addHoliday($easterDateTime->add(new \DateInterval('P48D'))->format('Y-m-d')); //Zesłanie Ducha Świętego (Zielone Świątki), 50 dni po niedzieli wielkanocnej
        $this->addHoliday($easterDateTime->add(new \DateInterval('P11D'))->format('Y-m-d')); //Boże Ciało, 60 dni po niedzieli wielkanocnej
        $this->addHoliday($year.'-08-15'); //Święto Wojska Polskiego, Wniebowzięcie Najświętszej Maryi Panny
        $this->addHoliday($year.'-11-01'); //Wszystkich Świętych
        $this->addHoliday($year.'-11-11'); //Święto Niepodległości
        $this->addHoliday($year.'-12-25'); //Boże Narodzenie (pierwszy dzień)
        $this->addHoliday($year.'-12-26'); //Boże Narodzenie (pierwszy dzień)
        
        $this->addRegionHolidays($year);

        return $this->holidays;
    }

    /**
      * {@inheritdoc}
      */
    public function addRegionHolidays(int $year): void
    {
    }
}
