<?php

namespace PostScripton\Money\Tests\Unit;

use PostScripton\Money\Currency;
use PostScripton\Money\Money;
use PostScripton\Money\Tests\TestCase;

class MoneyTest extends TestCase
{
    /** @test */
    public function allTheWaysToCreateMoney(): void
    {
        $money1 = Money::of('12345000');
        $money2 = money('12345000');

        $this->assertEquals($money1, $money2);
    }

    /** @test */
    public function baseWaysOfFormattingMoney(): void
    {
        $usd = Currency::code('USD');
        $rub = Currency::code('RUB');

        $this->assertEquals('$ 123', money('1230000', $usd)->toString());
        $this->assertEquals('$ 123.4', money('1234000', $usd)->toString());
        $this->assertEquals('$ 1 234', money('12340000', $usd)->toString());
        $this->assertEquals('$ 1 234.5', money('12345000', $usd)->toString());

        $this->assertEquals('123 ₽', money('1230000', $rub)->toString());
        $this->assertEquals('123.4 ₽', money('1234000', $rub)->toString());
        $this->assertEquals('1 234 ₽', money('12340000', $rub)->toString());
        $this->assertEquals('1 234.5 ₽', money('12345000', $rub)->toString());
    }

    /** @test */
    public function numbersCanBeFetchedOutOfTheMoney(): void
    {
        $money = Money::of('12345000');

        $this->assertEquals('1 234.5', $money->getAmount());
        $this->assertEquals('12345000', $money->getPureAmount());
    }

    /** @test */
    public function allCastsToString(): void
    {
        $money = Money::of('1234000');

        $this->assertEquals('$ 123.4', $money->toString());
        $this->assertEquals('$ 123.4', strval($money));
        $this->assertEquals('$ 123.4', '' . $money);
        $this->assertEquals('$ 123.4', $money);
    }

    /** @test */
    public function moneyGetsRidOfDecimalsWithFloorMethod(): void
    {
        $money = new Money('102500');

        $this->assertEquals('102500', $money->getPureAmount());
        $this->assertEquals('$ 10.3', $money->toString());

        $money->floor();

        $this->assertEquals('100000', $money->getPureAmount());
        $this->assertEquals('$ 10', $money->toString());
    }

    /** @test */
    public function moneyGetsRidOfDecimalsWithCeilMethod(): void
    {
        $money = new Money('102500');

        $this->assertEquals('102500', $money->getPureAmount());
        $this->assertEquals('$ 10.3', $money->toString());

        $money->ceil();

        $this->assertEquals('110000', $money->getPureAmount());
        $this->assertEquals('$ 11', $money->toString());
    }

    /**
     * @test
     * @dataProvider absoluteDataProvider
     */
    public function moneyAbsoluteAmount(string $negative, string $absolute): void
    {
        $money = money($negative);

        $money->absolute();

        $this->assertEquals($absolute, $money->getPureAmount());
    }

    /** @test */
    public function correctWayToHandleImmutableMoneyObjects(): void
    {
        $m1 = money('1000000');

        $m2 = $m1
            // adds to the both
            ->add(money('500000'))
            // $m2 is $150 as long as $m1 but $m2 is independent now
            ->clone()
            // $m2 is $300 whereas $m1 is still $150
            ->multiply(2);

        $this->assertEquals('1500000', $m1->getPureAmount());
        $this->assertEquals('3000000', $m2->getPureAmount());
        $this->assertFalse($m1->equals($m2));
    }

    protected function absoluteDataProvider(): array
    {
        return [
            [
                'negative' => '-12345',
                'absolute' => '12345',
            ],
            [
                'negative' => '12345',
                'absolute' => '12345',
            ],
            [
                'negative' => '-0',
                'absolute' => '0',
            ],
        ];
    }
}
