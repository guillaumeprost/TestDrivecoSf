<?php

namespace App\Tests;

use App\Entity\PriceComputation;
use App\Entity\PriceRule;
use App\Exceptions\WrongDateOrderException;
use App\Service\CalculatePrices;
use PHPUnit\Framework\TestCase;


class CalculatePricesTest extends TestCase
{
    function testOnePriceRule(): void
    {
        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $calculationService($computation, false);

        $this->assertEquals($expected, $result, 'Test with one rule');
    }

    function testTwoOverlappingPriceRule(): void
    {
        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 240, 0.24, 0))
            ->addRule(new PriceRule(1, 7, 210, 240, 0.4, 1));

        $expected = round((90 * 0.24) + (30 * 0.4), 2);
        $result = $calculationService($computation, false);

        $this->assertEquals($expected, $result, 'Test with two not overlapping prices');

    }

    function testTwoNotOverlapingPriceRule(): void
    {
        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 180, 0.24, 0))
            ->addRule(new PriceRule(1, 7, 180, 240, 0.4, 1));

        $expected = round((60 * 0.24) + (60 * 0.4), 2);
        $result = $calculationService($computation, false);

        $this->assertEquals($expected, $result, 'Test with two not overlaping prices');
    }

    function testMultipleDaysPriceRule(): void
    {
        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/06 2am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round((1440 * 4) * 0.24, 2);
        $result = $calculationService($computation, false);

        $this->assertEquals($expected, $result, 'Test multiple days price');
    }

    function testMultipleDaysThroughWeekPriceRule(): void
    {
        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/09 2am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0))
            ->addRule(new PriceRule(6, 7, 0, 1440, 0.18, 99));

        $expected = round(((1440 * 5) * 0.24) + ((1440 * 2) * 0.18), 2);
        $result = $calculationService($computation, false);

        $this->assertEquals($expected, $result, 'Test multiple days throught week price');
    }

    function testWrongDates(): void
    {
        $this->expectException(\DateMalformedStringException::class);

        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/19/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/02 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $calculationService($computation, false);
    }

    function testWrongOrderDates(): void
    {
        $this->expectException(WrongDateOrderException::class);

        $calculationService = new CalculatePrices();
        $computation = (new PriceComputation())
            ->setFrom(new \DateTimeImmutable('2024/09/02 2am'))
            ->setTo(new \DateTimeImmutable('2024/09/01 4am'))
            ->addRule(new PriceRule(1, 7, 0, 1440, 0.24, 0));

        $expected = round(120 * 0.24, 2);
        $result = $calculationService($computation, false);
    }
}
