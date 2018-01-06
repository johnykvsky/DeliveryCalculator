<?php

namespace johnykvsky\Utils;

/**
 * Calculate delivery time, skip holidays and/or weekends
 */
class DeliveryCalculator
{
    /**
     * @var mixed $shippingProvider Provider for shipping timezone and holidays
     */
    public $shippingProvider;

    /**
     * @var mixed $deliveryProvider Provider for delivery timezone and holidays
     */
    public $deliveryProvider;

    /**
     * @var mixed $additionalNonWorkingDays Additional non working days (ie. we don't deliver on mondays)
     */
    public $additionalNonWorkingDays = [];

    /**
      * Set shipping provider
      *
      * @param mixed $shippingProvider Provider
      *
      * @return void
      */
    public function setShippingProvider($shippingProvider)
    {
        $this->shippingProvider = $shippingProvider;
    }

    /**
      * Set delivery provider
      *
      * @param mixed $deliveryProvider Provider
      *
      * @return void
      */
    public function setDeliveryProvider($deliveryProvider)
    {
        $this->deliveryProvider = $deliveryProvider;
    }

    /**
      * Set additional working days
      *
      * @param mixed $additionalNonWorkingDays Additional working days
      *
      * @return void
      */
    public function setAdditionalNonWorkingDays($additionalNonWorkingDays)
    {
        $this->additionalNonWorkingDays = $additionalNonWorkingDays;
    }

    /**
      * Get additional working days
      *
      * @return array
      */
    public function getAdditionalNonWorkingDays()
    {
        return $this->additionalNonWorkingDays;
    }

    /**
      * Calculates delivery date in X days from Y date
      *
      * @param integer $deliveryInDays Deliver package in X days
      * @param string $shippingDate Shipping start date, Y-m-d format
      *
      * @return \DateTime|false
      */
    public function calculateDeliveryDate($deliveryInDays, $shippingDate)
    {
        $deliverableDaysCounter = 0;
        $deliveryDate = false;

        $shippingTimezone = $this->shippingProvider->getDateTimeZone();
        $deliveryTimezone = $this->deliveryProvider->getDateTimeZone();
        $shippingDate = \DateTime::createFromFormat('Y-m-d', $shippingDate, $shippingTimezone);

        if (empty($shippingDate)) {
            throw \Exception('Invalid shippingDate or shippingTimezone');
        }

        $initialShippingDate = \DateTimeImmutable::createFromMutable($shippingDate);

        $shippingHolidays = $this->shippingProvider->getHolidays($shippingDate->format('Y'));
        $deliveryHolidays = $this->deliveryProvider->getHolidays($shippingDate->format('Y'));

        while ($deliverableDaysCounter <= $deliveryInDays) {
            $deliveryDate = $shippingDate->modify('+1 day');

            if ($initialShippingDate->format('Y') != $deliveryDate->format('Y')) {
                $shippingHolidays = $this->shippingProvider->getHolidays($deliveryDate->format('Y'));
                $deliveryHolidays = $this->deliveryProvider->getHolidays($deliveryDate->format('Y'));
            }

            if ((!in_array($deliveryDate->format('Y-m-d'), $shippingHolidays)
                    or !in_array($deliveryDate->format('Y-m-d'), $deliveryHolidays))
                    and !in_array($deliveryDate->format('w'), $this->shippingProvider->getNonWorkingWeekDays())
                    and !in_array($deliveryDate->format('w'), $this->deliveryProvider->getNonWorkingWeekDays())
                    and !in_array($deliveryDate->format('w'), $this->getAdditionalNonWorkingDays())) { //no delivery in saturday 6, and sunday 0
                $deliverableDaysCounter++;
            }

            if ($deliverableDaysCounter == $deliveryInDays) {
                break;
            }
        }

        if (!empty($deliveryTimezone)) {
            $deliveryDate->setTimezone($deliveryTimezone);
        }
        return $deliveryDate;
    }
}
