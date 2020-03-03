# DeliveryCalculator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Calculate delivery time - only working days, skip holidays or weekends (witch can be turned off, but by default is on).

## Install

Via Composer

``` bash
$ composer require johnykvsky/deliverycalculator
```

Should work fine on PHP 5.6, but I didn't check that. Just change required PHP version in composer.json and maybe remove dev packages.

## Usage

``` php
$dlvCalc = new johnykvsky\Utils\DeliveryCalculator();
$provider = new johnykvsky\Utils\PolishDeliveryProvider(); //We want to ship/deliver in Poland
$dlvCalc->setShippingProvider($provider); //from Poland
$dlvCalc->setDeliveryProvider($provider); //to Poland
$result = $dlvCalc->calculateDeliveryDate(14, '2017-10-20'); //Get delivery date, shipping on 2017-10-20, deliver in 14 working days
echo $result->format('Y-m-d'); //2017-11-10
```

DeliveryCalculator depends on providers, however, you can create one generic provider and use it for all calculations, modifying on the fly (getters/setters) timezones, holidays and delivery-free days (ie. when client paid for delivery on sunday).

Providers has following settings:
- timezone
- holidays
- nonWorkingDays
- region

Timezone is used for proper delivery time calculation, with ie. 8h differences delivery date might skip to next day. Holidays are dates of christmas, easter and all other "officially free from work" days. NonWorkingDays are days when usually we don't deliver, like sunday. Region can be optionally set and provided with additional holidays, specific for this particular country area.

Delivery and shipping location has separate providers, witch is important, since if we want to ship tomorrow (item was sold today), but tomorrow is sunday, next day is holiday, so shipping should skip them and count from first working day.


## Testing

``` bash
$ composer test
```

## Code checking

``` bash
$ composer phpstan
$ composer phpstan-max
```

## Security

If you discover any security related issues, please email johnykvsky@protonmail.com instead of using the issue tracker.

## Credits

- [johnykvsky][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/johnykvsky/DeliveryCalculator.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/johnykvsky/DeliveryCalculator/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/johnykvsky/DeliveryCalculator.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/johnykvsky/DeliveryCalculator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/johnykvsky/DeliveryCalculator.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/johnykvsky/DeliveryCalculator
[link-travis]: https://travis-ci.org/johnykvsky/DeliveryCalculator
[link-scrutinizer]: https://scrutinizer-ci.com/g/johnykvsky/DeliveryCalculator/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/johnykvsky/DeliveryCalculator
[link-downloads]: https://packagist.org/packages/johnykvsky/DeliveryCalculator
[link-author]: https://github.com/johnykvsky
