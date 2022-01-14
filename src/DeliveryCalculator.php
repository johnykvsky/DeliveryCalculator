<?php

declare(strict_types=1);

namespace johnykvsky\Utils;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use johnykvsky\Utils\Exception\InvalidDateTimezoneException;

/**
 * Calculate delivery time, skip holidays and/or weekends
 */
class DeliveryCalculator
{
    public DeliveryProviderInterface $shippingProvider;
    public DeliveryProviderInterface $deliveryProvider;
    public array $nonWorkingDays = [];

    public function __construct(
        DeliveryProviderInterface $shippingProvider,
        DeliveryProviderInterface $deliveryProvider
    ) {
        $this->shippingProvider = $shippingProvider;
        $this->deliveryProvider = $deliveryProvider;
    }

    public function setShippingProvider(DeliveryProviderInterface $shippingProvider): void
    {
        $this->shippingProvider = $shippingProvider;
    }

    public function setDeliveryProvider(DeliveryProviderInterface $deliveryProvider): void
    {
        $this->deliveryProvider = $deliveryProvider;
    }

    public function calculateDeliveryDate(int $deliveryInDays, string $inputShippingDate): DateTimeImmutable
    {
        $deliverableDaysCounter = 0;

        $shippingTimezone = $this->shippingProvider->getDateTimeZone();
        $deliveryTimezone = $this->deliveryProvider->getDateTimeZone();

        $shippingDate = DateTimeImmutable::createFromFormat('Y-m-d', $inputShippingDate, $shippingTimezone);

        if (!$shippingDate) {
            throw new InvalidDateTimezoneException('Invalid shippingDate or shippingTimezone');
        }

        $deliveryDate = $shippingDate->setTimezone($deliveryTimezone);

        $initialShippingDate = $shippingDate;

        $shippingHolidays = $this->shippingProvider->getHolidays((int)$shippingDate->format('Y'));
        $deliveryHolidays = $this->deliveryProvider->getHolidays((int)$shippingDate->format('Y'));

        while ($deliverableDaysCounter <= $deliveryInDays) {
            $deliveryDate = $deliveryDate->add(new DateInterval('P1D'));

            if ($initialShippingDate->format('Y') !== $deliveryDate->format('Y')) {
                $shippingHolidays = $this->shippingProvider->getHolidays((int)$deliveryDate->format('Y'));
                $deliveryHolidays = $this->deliveryProvider->getHolidays((int)$deliveryDate->format('Y'));
            }

            if ($this->checkDate($deliveryDate, $shippingHolidays, $deliveryHolidays)) { //no delivery in saturday 6, and sunday 0
                $deliverableDaysCounter++;
            }

            if ($deliverableDaysCounter === $deliveryInDays) {
                break;
            }
        }

        return $deliveryDate;
    }

    public function getNonWorkingDays(): array
    {
        return $this->nonWorkingDays;
    }

    public function setNonWorkingDays(array $nonWorkingDays): void
    {
        $this->nonWorkingDays = $nonWorkingDays;
    }

    private function checkDate(DateTimeImmutable $deliveryDate, array $shippingHolidays, array $deliveryHolidays): bool
    {
        if (!in_array((int)$deliveryDate->format('w'), $this->shippingProvider->getNonWorkingWeekDays(), true)
            && !in_array((int)$deliveryDate->format('w'), $this->deliveryProvider->getNonWorkingWeekDays(), true)
            && !in_array((int)$deliveryDate->format('w'), $this->getNonWorkingDays(), true)
            && (!in_array($deliveryDate->format('Y-m-d'), $shippingHolidays, true)
                || !in_array($deliveryDate->format('Y-m-d'), $deliveryHolidays, true))
        ) { //no delivery in saturday 6, and sunday 0
            return true;
        }

        return false;
    }
}
