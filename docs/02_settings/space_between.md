# Space between currency and number
You may get or specify whether there is a space between currency and number `$( )123.4`:

## Methods

### `hasSpaceBetween()`
**Returns**: `bool`

### `setHasSpaceBetween([bool $space = true])`
**Parameters**:
1. `[bool $space = true]` (*optional*) - determines whether there is a space between a currency and amount `$ 123.4 => true`.

**Returns**: `void`

## Usage

```php
$money = money('1234000');

$money->settings()->hasSpaceBetween();  // true
$money->toString();                     // "$ 123.4"

$money->settings()->setHasSpaceBetween(false);

$money->settings()->hasSpaceBetween();  // false
$money->toString();                     // "$123.4"
```

## Exceptions

❗ There are some exceptions. There will always be a space when:
1. a currency is going first, and an amount is negative. `$( )-50`
2. a currency is displayed as an ISO code. `50( )USD` or `USD( )50`

---

📌 Back to the [contents](/docs/02_settings/README.md).
