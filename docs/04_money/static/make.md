# `Money::make()`
creates a monetary object.

## Usage

```php
use PostScripton\Money\Currency;
use PostScripton\Money\Money;

// Default currency: USD

Money::make('0');                               // "$ 0"
Money::make('1230000');                         // "$ 123"
Money::make('1234000');                         // "$ 123.4"
Money::make('12345000');                        // "$ 1 234.5"

Money::make('12345000', Currency::code('RUB')); // "1 234.5 ₽"
```

Method `make()` is one of the variants to create a Money object.
All the ways to pass parameters have already been discussed [here](/docs/01_usage/creating.md).

---

📌 Back to the [contents](/docs/04_money/README.md).
