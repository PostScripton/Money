<?php

namespace PostScripton\Money;

use PostScripton\Money\PHPDocs\MoneySettingsInterface;

class MoneySettings implements MoneySettingsInterface
{
    public const MIN_DECIMALS = 1;
    public const MAX_DECIMALS = 4;

    private int $decimals;
    private string $thousands_separator;
    private string $decimal_separator;
    private bool $ends_with_0;
    private bool $space_between;
    private Currency $currency;

    private ?Money $money;

    public function __construct(
        int $decimals = null,
        string $thousands_separator = null,
        string $decimal_separator = null,
        bool $ends_with_0 = null,
        bool $space_between = null,
        Currency $currency = null,
    ) {
        $this->money = null;

        $this->setDecimals($decimals ?? Money::getDefaultDecimals())
            ->setThousandsSeparator($thousands_separator ?? Money::getDefaultThousandsSeparator())
            ->setDecimalSeparator($decimal_separator ?? Money::getDefaultDecimalSeparator())
            ->setEndsWith0($ends_with_0 ?? Money::getDefaultEndsWith0())
            ->setHasSpaceBetween($space_between ?? Money::getDefaultSpaceBetween())
            ->setCurrency($currency ?? Currency::code(Currency::getConfigCurrency()));
    }

    public function bind(Money $money): self
    {
        $this->money = $money;
        return $this;
    }

    public function unbind(): self
    {
        $this->money->unbind();
        $this->money = null;
        return $this;
    }

    public function bound(): bool
    {
        return !is_null($this->money);
    }

    // ========== SETTERS ==========

    public function setDecimals(int $decimals = self::MIN_DECIMALS): self
    {
        if ($decimals < self::MIN_DECIMALS) {
            $decimals = self::MIN_DECIMALS;
        } elseif ($decimals > self::MAX_DECIMALS) {
            $decimals = self::MAX_DECIMALS;
        }

        $this->decimals = $decimals;
        return $this;
    }

    public function setThousandsSeparator(string $separator): self
    {
        $this->thousands_separator = $separator;
        return $this;
    }

    public function setDecimalSeparator(string $separator): self
    {
        $this->decimal_separator = $separator;
        return $this;
    }

    public function setEndsWith0(bool $ends = false): self
    {
        $this->ends_with_0 = $ends;
        return $this;
    }

    public function setHasSpaceBetween(bool $space = true): self
    {
        $this->space_between = $space;
        return $this;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    // ========== GETTERS ==========

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function getThousandsSeparator(): string
    {
        return $this->thousands_separator;
    }

    public function getDecimalSeparator(): string
    {
        return $this->decimal_separator;
    }

    public function endsWith0(): bool
    {
        return $this->ends_with_0;
    }

    public function hasSpaceBetween(): bool
    {
        return $this->space_between;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}
