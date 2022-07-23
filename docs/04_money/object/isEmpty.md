# `isEmpty()`

checks whether the money's number is zero.

## Methods

### `isEmpty()`
**Returns**: `bool`

## Usage

```php
$m1 = money('0');
$m2 = money('1000000');
$m3 = money('-10000000');

$m1->isEmpty(); // true
$m2->isEmpty(); // false
$m3->isEmpty(); // false
```

---

📌 Back to the [contents](/docs/04_money/README.md).
