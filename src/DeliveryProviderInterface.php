<?php

namespace johnykvsky\Utils;

/**
 * Interface for delivery providers
 */
interface DeliveryProviderInterface
{
    /**
      * Set timezone
      *
      * @param string $timezone Timezone name
      *
      * @return void
      */
    public function setTimezone(string $timezone): void;

    /**
      * Set region
      *
      * @param string $region Delivery region
      *
      * @return void
      */
    public function setRegion(string $region): void;

    /**
      * Add work-free day
      *
      * @param string $date Date in Y-m-d format
      *
      * @return void
      */
    public function addHoliday(string $date): void;

    /**
      * Get DateTimeZone with providers timezone
      *
      * @return \DateTimeZone
      */
    public function getDateTimeZone(): \DateTimeZone;

    /**
      * Get non working days
      *
      * @return mixed[]
      */
    public function getNonWorkingWeekDays(): array;

    /**
      * @param mixed[] $nonWorkingWeekDays Array of non working days
      */
    public function setNonWorkingWeekDays(array $nonWorkingWeekDays): void;

    /**
      * Get dates of holidays for given year
      *
      * @param integer $year Year for calculation
      *
      * @return mixed[]
      */
    public function getHolidays(int $year): array;

    /**
      * Add holidays specific for country region
      *
      * @param integer $year Year
      *
      * @return void
      */
    public function addRegionHolidays(int $year): void;
}
