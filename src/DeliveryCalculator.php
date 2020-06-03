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
    /**
     * @var DeliveryProviderInterface $shippingProvider Provider for shipping timezone and holidays
     */
    public $shippingProvider;

    /**
     * @var DeliveryProviderInterface $deliveryProvider Provider for delivery timezone and holidays
     */
    public $deliveryProvider;

    /**
     * @var array<string> $nonWorkingDays Additional non working days (ie. we don't deliver on mondays)
     */
    public $nonWorkingDays = [];

    public function __construct(
        DeliveryProviderInterface $shippingProvider,
        DeliveryProviderInterface $deliveryProvider
    ) {
        $this->shippingProvider = $shippingProvider;
        $this->deliveryProvider = $deliveryProvider;
    }

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
     * Calculates delivery date in X days from Y date
     *
     * @param integer $deliveryInDays Deliver package in X days
     * @param string $inputShippingDate Shipping start date, Y-m-d format
     *
     * @return DateTime|false
     */
    public function calculateDeliveryDate(
        int $deliveryInDays,
        string $inputShippingDate
    ) {
        $deliverableDaysCounter = 0;

        $shippingTimezone = $this->shippingProvider->getDateTimeZone();
        $deliveryTimezone = $this->deliveryProvider->getDateTimeZone();

        $shippingDate = DateTime::createFromFormat('Y-m-d', $inputShippingDate, $shippingTimezone);

        if (empty($shippingDate)) {
            throw new InvalidDateTimezoneException('Invalid shippingDate or shippingTimezone');
        }

        $deliveryDate = clone $shippingDate;
        $deliveryDate->setTimezone($deliveryTimezone);

        if (empty($deliveryDate)) {
            throw new InvalidDateTimezoneException('Invalid deliveryDate or deliveryTimezone');
        }

        $initialShippingDate = DateTimeImmutable::createFromMutable($shippingDate);

        $shippingHolidays = $this->shippingProvider->getHolidays((int)$shippingDate->format('Y'));
        $deliveryHolidays = $this->deliveryProvider->getHolidays((int)$shippingDate->format('Y'));

        while ($deliverableDaysCounter <= $deliveryInDays) {
            $deliveryDate = $shippingDate->add(new DateInterval('P1D'));

            if ($initialShippingDate->format('Y') !== $deliveryDate->format('Y')) {
                $shippingHolidays = $this->shippingProvider->getHolidays((int)$deliveryDate->format('Y'));
                $deliveryHolidays = $this->deliveryProvider->getHolidays((int)$deliveryDate->format('Y'));
            }

            if ($this->checkDate(
                $deliveryDate,
                $shippingHolidays,
                $deliveryHolidays
            )) { //no delivery in saturday 6, and sunday 0
                $deliverableDaysCounter++;
            }

            if ($deliverableDaysCounter === $deliveryInDays) {
                break;
            }
        }

        return $deliveryDate;
    }

    /**
     * @param DateTime $deliveryDate
     * @param array $shippingHolidays
     * @param array $deliveryHolidays
     * @return bool
     */
    private function checkDate(DateTime $deliveryDate, array $shippingHolidays, array $deliveryHolidays): bool
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

    /**
     * Get additional working days
     *
     * @return mixed[]
     */
    public function getNonWorkingDays(): array
    {
        return $this->nonWorkingDays;
    }

    /**
     * Set additional working days
     *
     * @param mixed[] $nonWorkingDays Additional working days
     *
     * @return void
     */
    public function setNonWorkingDays(array $nonWorkingDays): void
    {
        $this->nonWorkingDays = $nonWorkingDays;
    }
}
