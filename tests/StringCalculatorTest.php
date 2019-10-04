<?php

declare(strict_types=1);

namespace App\Tests;

use App\Exception\IncorrectArgument;
use App\StringCalculator;
use PHPUnit\Framework\TestCase;

class StringCalculatorTest extends TestCase
{
    /** @var StringCalculator */
    private $stringCalculator;

    /**
     * @return mixed[]
     */
    public function exceptionProvider() : array
    {
        return [
            'commaInTheEnd' => ['2,', 'Number expected but EOF found.'],
            'commaAtBeginning' => [',1', 'Number expected but , found at position 0.'],
            'negativeNumber&doubleComma' => ['-1,,2', 'Number expected but , found at position 3.\nNegative not allowed : -1.'],
            'negativeNumber' => ['-1,2', 'Negative not allowed : -1.'],
            'newLineInTheEnd' => ['2\n', 'Number expected but EOF found.'],
            'negativeNumber&newLineInTheEnd' => ['-2\n', 'Number expected but EOF found.\nNegative not allowed : -2.'],
            'doubleNegativeNumbers' => ['-2\n-1', 'Negative not allowed : -2,-1.'],
        ];
    }

    /**
     * @dataProvider exceptionProvider
     */
    public function testAddThrowInvalidException(string $argument, string $message) : void
    {
        $this->expectException(IncorrectArgument::class);
        $this->expectExceptionMessage($message);

        $this->stringCalculator->add($argument);
    }

    /**
     * @return mixed[]
     */
    public function dataProvider() : array
    {
        return [
            'empty string' => ['', '0'],
            'one int' => ['3', '3'],
            'two ints' => ['2,2', '4'],
            'one float' => ['3.3', '3.3'],
            'one float, one int' => ['1.1,2', '3.1'],
            'two floats with comma' => ['1.1,2.1', '3.2'],
            'two floats with sep' => ['1.1sep2.1', '3.2'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAdd(string $argument, string $expected) : void
    {
        $this->assertEquals($expected, $this->stringCalculator->add($argument));
    }

    public function setUp() : void
    {
        $this->stringCalculator = new StringCalculator();
    }
}
