<?php

namespace PostScripton\Money\Tests\Unit;

use InvalidArgumentException;
use PostScripton\Money\Currency;
use PostScripton\Money\Exceptions\MoneyHasDifferentCurrenciesException;
use PostScripton\Money\Money;
use PostScripton\Money\Tests\TestCase;

class MoneyTest extends TestCase
{
    public function testAllTheWaysToCreateMoney(): void
    {
        $money1 = new Money('12345000');
        $money2 = Money::of('12345000');
        $money3 = money('12345000');
        $money4 = money_parse('$ 1234.5');
        $money5 = money('12345000', currency('USD'));
        $money6 = money('12345000.1234567890');

        $this->assertMoneyEquals($money1, $money2);
        $this->assertMoneyEquals($money1, $money3);
        $this->assertMoneyEquals($money1, $money4);
        $this->assertMoneyEquals($money1, $money5);
        $this->assertMoneyEquals($money1, $money6);
        $this->assertEquals('12345000', $money6->getAmount());
    }

    public function testCreatingMonetaryObjectAcceptsStringAsCurrency(): void
    {
        $m1 = money('12345000', 'RUB');
        $m2 = Money::of('12345000', 'RUB');

        $this->assertMoneyEquals($m1, $m2);
    }

    public function testZero(): void
    {
        $m1 = Money::zero();
        $m2 = money_zero();
        $expectedUsd = money('0', 'USD');

        $this->assertMoneyEquals($expectedUsd, $m1);
        $this->assertMoneyEquals($expectedUsd, $m2);

        $m3 = Money::zero('RUB');
        $m4 = money_zero(currency('RUB'));
        $expectedRub = money('0', 'RUB');

        $this->assertMoneyEquals($expectedRub, $m3);
        $this->assertMoneyEquals($expectedRub, $m4);
    }

    public function testSetCurrency(): void
    {
        $money = money('12345000', 'RUB');

        $money->setCurrency(currency('USD'));
        $this->assertEquals('USD', $money->getCurrency()->getCode());

        $money->setCurrency('EUR');
        $this->assertEquals('EUR', $money->getCurrency()->getCode());
    }

    /** @dataProvider creatingMoneyWithExceptionDataProvider */
    public function testCreatingMoneyWithNonNumericStringThrowsAnException(string $amount): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The amount must be a numeric-string, [%s] given', $amount));

        new Money($amount);
    }

    public function testBaseWaysOfFormattingMoney(): void
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

    public function testNumbersCanBeFetchedOutOfTheMoney(): void
    {
        $money = Money::of('12345000');

        $this->assertEquals('1 234.5', $money->toAmountOnlyString());
        $this->assertEquals('12345000', $money->getAmount());
    }

    public function testAllCastsToString(): void
    {
        $money = Money::of('1234000');

        $this->assertEquals('$ 123.4', $money->toString());
        $this->assertEquals('$ 123.4', strval($money));
        $this->assertEquals('$ 123.4', '' . $money);
        $this->assertEquals('$ 123.4', $money);
    }

    public function testMultiply(): void
    {
        $money = money_parse('100');

        $money->multiply('2');
        $this->assertEquals('2000000', $money->getAmount());
        $this->assertEquals('$ 200', $money->toString());

        $money->multiply('-2');
        $this->assertEquals('-4000000', $money->getAmount());
        $this->assertEquals('$ -400', $money->toString());
    }

    public function testMultiplyByFloat(): void
    {
        $money = money_parse('100');

        $money->multiply(0.5);
        $this->assertEquals('500000', $money->getAmount());
        $this->assertEquals('$ 50', $money->toString());

        $money->multiply(-0.5);
        $this->assertEquals('-250000', $money->getAmount());
        $this->assertEquals('$ -25', $money->toString());
    }

    public function testMultiplyButNoDecimalsInAmount(): void
    {
        $money = money_parse('10');
        $expectedMoney = money_parse('3.3333');

        $money->multiply((string) (1 / 3));

        $this->assertEquals('$ 3.3', $money->toString());
        $this->assertMoneyEquals($expectedMoney, $money);
    }

    public function testDivideButNoDecimalsInAmount(): void
    {
        $money = money_parse('100');
        $expectedMoney = money_parse('33.3333');

        $money->divide('3');

        $this->assertEquals('$ 33.3', $money->toString());
        $this->assertMoneyEquals($expectedMoney, $money);
    }

    public function testFloor(): void
    {
        $money = money('102500');

        $this->assertEquals('102500', $money->getAmount());
        $this->assertEquals('$ 10.3', $money->toString());

        $money->floor();

        $this->assertEquals('100000', $money->getAmount());
        $this->assertEquals('$ 10', $money->toString());
    }

    public function testNegativeFloor(): void
    {
        $money = money('-102500');

        $this->assertEquals('-102500', $money->getAmount());
        $this->assertEquals('$ -10.3', $money->toString());

        $money->floor();

        $this->assertEquals('-110000', $money->getAmount());
        $this->assertEquals('$ -11', $money->toString());
    }

    public function testCeil(): void
    {
        $money = money('102500');

        $this->assertEquals('102500', $money->getAmount());
        $this->assertEquals('$ 10.3', $money->toString());

        $money->ceil();

        $this->assertEquals('110000', $money->getAmount());
        $this->assertEquals('$ 11', $money->toString());
    }

    public function testNegativeCeil(): void
    {
        $money = money('-102500');

        $this->assertEquals('-102500', $money->getAmount());
        $this->assertEquals('$ -10.3', $money->toString());

        $money->ceil();

        $this->assertEquals('-100000', $money->getAmount());
        $this->assertEquals('$ -10', $money->toString());
    }

    public function testFractionPartOfAmountPassedToConstructorIsCut(): void
    {
        $this->assertEquals('102500', money('102500.1234')->getAmount());
        $this->assertEquals('-102500', money('-102500.1234')->getAmount());
    }

    public function testRebaseMoney(): void
    {
        $m1 = money_parse('100');
        $m2 = money_parse('250');

        $m1->rebase($m2);

        $this->assertMoneyEquals(money_parse('250'), $m1);
    }

    public function testAddingMoneyWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(MoneyHasDifferentCurrenciesException::class);

        $m1 = money_parse('100');
        $m2 = money_parse('250', 'RUB');

        $m1->add($m2);
    }

    public function testSubtractingMoneyWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(MoneyHasDifferentCurrenciesException::class);

        $m1 = money_parse('100');
        $m2 = money_parse('250', 'RUB');

        $m1->subtract($m2);
    }

    public function testRebasingMoneyWithDifferentCurrencyThrowsException(): void
    {
        $this->expectException(MoneyHasDifferentCurrenciesException::class);

        $m1 = money_parse('100');
        $m2 = money_parse('250', 'RUB');

        $m1->rebase($m2);
    }

    /** @dataProvider absoluteDataProvider */
    public function testMoneyAbsoluteAmount(string $negative, string $absolute): void
    {
        $money = money($negative);

        $money->absolute();

        $this->assertEquals($absolute, $money->getAmount());
    }

    public function testCorrectWayToHandleImmutableMoneyObjects(): void
    {
        $m1 = money_parse('100');

        $m2 = $m1
            // adds to the both
            ->add(money_parse('50'))
            // $m2 is $150 as long as $m1 but $m2 is independent now
            ->clone()
            // $m2 is $300 whereas $m1 is still $150
            ->multiply(2);

        $this->assertMoneyEquals('150', $m1);
        $this->assertMoneyEquals('300', $m2);
        $this->assertMoneyNotEquals($m1, $m2);
    }

    /** @dataProvider differenceDataProvider */
    public function testDifference(string $first, string $second, string $result): void
    {
        $m1 = money_parse($first);
        $m2 = money_parse($second);

        $diff = $m1->difference($m2);

        $this->assertInstanceOf(Money::class, $diff);
        $this->assertMoneyEquals(money_parse($result), $diff);
    }

    /** @dataProvider providerJsonSerialize */
    public function testJsonSerialize(Money $money, array $expected): void
    {
        $this->assertEquals($expected, $money->jsonSerialize());
        $this->assertEquals(json_encode($expected), json_encode($money));
    }

    public function providerJsonSerialize(): array
    {
        $this->createApplication();

        return [
            [Money::parse('10.25', 'USD'), ['amount' => '102500', 'currency' => 'USD']],
            [Money::of('12345', 'RUB'), ['amount' => '12345', 'currency' => 'RUB']],
        ];
    }

    public function testExceptionIsThrownWhenThereAreTwoDifferentCurrencies(): void
    {
        $m1 = money('500000');
        $m2 = money('1000000', currency('rub'));

        $this->expectException(MoneyHasDifferentCurrenciesException::class);

        $m1->difference($m2);
    }

    protected function differenceDataProvider(): array
    {
        return [
            [
                'first' => '$ 25',
                'second' => '$ 100',
                'result' => '$ 75',
            ],
            [
                'first' => '$ 100',
                'second' => '$ 25',
                'result' => '$ 75',
            ],
            [
                'first' => '$ 0',
                'second' => '$ 25',
                'result' => '$ 25',
            ],
            [
                'first' => '$ 25',
                'second' => '$ 0',
                'result' => '$ 25',
            ],
            [
                'first' => '$ 0',
                'second' => '$ 0',
                'result' => '$ 0',
            ],
            [
                'first' => '$ 100',
                'second' => '$ -25',
                'result' => '$ 125',
            ],
            [
                'first' => '$ -100',
                'second' => '$ -115',
                'result' => '$ 15',
            ],
            [
                'first' => '$ -500',
                'second' => '$ 100',
                'result' => '$ 600',
            ],
        ];
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

    protected function creatingMoneyWithExceptionDataProvider(): array
    {
        return [
            ['amount' => 'qwerty'],
            ['amount' => '$ 1 234.5'],
            ['amount' => '$ 1234.5'],
        ];
    }
}
