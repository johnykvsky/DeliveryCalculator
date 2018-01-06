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
    public function setTimezone($timezone);

    /**
      * Set region
      *
      * @param string $region Delivery region
      *
      * @return void
      */
    public function setRegion($region);

    /**
      * Add work-free day
      *
      * @param string $date Date in Y-m-d format
      *
      * @return void
      */
    public function addHoliday($date);

    /**
      * Get DateTimeZone with providers timezone
      *
      * @return \DateTimeZone
      */
    public function getDateTimeZone();

    /**
      * Get non working days
      *
      * @return array
      */
    public function getNonWorkingWeekDays();

    /**
      * {@inheritdoc}
      */
    public function setNonWorkingWeekDays($nonWorkingWeekDays);

    /**
      * Get dates of holidays for given year
      *
      * @param integer $year Year for calculation
      *
      * @return array
      */
    public function getHolidays($year);

    /**
      * Add holidays specific for country region
      *
      * @param integer $year Year
      * @param string $easter Easter day
      *
      * @return void
      */
    public function addRegionHolidays($year, $easter);
}
