<?php

declare(strict_types=1);

namespace johnykvsky\Utils;

use johnykvsky\Utils\Exception\InvalidDateTimezoneException;

/**
 * Calculate delivery time, skip holidays and/or weekends
 */
class DeliveryCalculator
{
    /**
     * @var DeliveryProviderInterface $shippingProvider Provider for shipping timezone and holidays
     */
    public $shippingProvider;

    /**
     * @var DeliveryProviderInterface $deliveryProvider Provider for delivery timezone and holidays
     */
    public $deliveryProvider;

    /**
     * @var array<string> $additionalNonWorkingDays Additional non working days (ie. we don't deliver on mondays)
     */
    public $additionalNonWorkingDays = [];

    /**
      * Set shipping provider
      *
      * @param DeliveryProviderInterface $shippingProvider Provider
      *
      * @return void
      */
    public function setShippingProvider(DeliveryProviderInterface $shippingProvider): void
    {
        $this->shippingProvider = $shippingProvider;
    }

    /**
      * Set delivery provider
      *
      * @param DeliveryProviderInterface $deliveryProvider Provider
      *
      * @return void
      */
    public function setDeliveryProvider(DeliveryProviderInterface $deliveryProvider): void
    {
        $this->deliveryProvider = $deliveryProvider;
    }

    /**
      * Set additional working days
      *
      * @param mixed[] $additionalNonWorkingDays Additional working days
      *
      * @return void
      */
    public function setAdditionalNonWorkingDays(array $additionalNonWorkingDays): void
    {
        $this->additionalNonWorkingDays = $additionalNonWorkingDays;
    }

    /**
      * Get additional working days
      *
      * @return mixed[]
      */
    public function getAdditionalNonWorkingDays(): array
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
    public function calculateDeliveryDate(int $deliveryInDays, string $shippingDate)
    {
        $deliverableDaysCounter = 0;
        $deliveryDate = false;

        $shippingTimezone = $this->shippingProvider->getDateTimeZone();
        $deliveryTimezone = $this->deliveryProvider->getDateTimeZone();
        
        $shippingDate = \DateTime::createFromFormat('Y-m-d', $shippingDate, $shippingTimezone);

        if (empty($shippingDate)) {
            throw new InvalidDateTimezoneException('Invalid shippingDate or shippingTimezone');
        }

        $deliveryDate = clone $shippingDate;
        
        if (!empty($deliveryTimezone)) {
            $deliveryDate->setTimezone($deliveryTimezone);
        }
        
        if (empty($deliveryDate)) {
            throw new InvalidDateTimezoneException('Invalid deliveryDate or deliveryTimezone');
        }

        $initialShippingDate = \DateTimeImmutable::createFromMutable($shippingDate);

        $shippingHolidays = $this->shippingProvider->getHolidays((int) $shippingDate->format('Y'));
        $deliveryHolidays = $this->deliveryProvider->getHolidays((int) $shippingDate->format('Y'));

        while ($deliverableDaysCounter <= $deliveryInDays) {
            $deliveryDate = $shippingDate->add(new \DateInterval('P1D'));

            if ($initialShippingDate->format('Y') != $deliveryDate->format('Y')) {
                $shippingHolidays = $this->shippingProvider->getHolidays((int) $deliveryDate->format('Y'));
                $deliveryHolidays = $this->deliveryProvider->getHolidays((int) $deliveryDate->format('Y'));
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

        return $deliveryDate;
    }
}
