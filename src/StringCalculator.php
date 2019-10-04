<?php

declare(strict_types=1);

namespace App;

use App\Exception\IncorrectArgument;
use function implode;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

final class StringCalculator
{
    private const EXPECTED_NUMBER_AT_THE_END = 'Number expected but EOF found.';
    private const EXPECTED_NUMBER            = 'Number expected but %s found at position %s.';
    private const NEGATIVE_NOT_ALLOWED       = 'Negative not allowed : %s.';

    private const SEPARATORS = [',', '\n', 'sep'];
    /** @var float */
    private $sum;
    /** @var string */
    private $unprocessedChars;
    /** @var int */
    private $currentPosition;
    /** @var string[] */
    private $errors = [];
    /** @var string[] */
    private $negativeNumbers = [];

    /**
     * @throws IncorrectArgument
     */
    public function add(string $string) : string
    {
        if (empty($string)) {
            return '0';
        }

        $this->unprocessedChars = $string;
        $this->sum              = 0;
        $this->currentPosition  = -1;
        $currentNumber          = '';

        while (! empty($this->unprocessedChars)) {
            $separator = $this->dequeueSeparator();
            if (! $separator) {
                $currentNumber .= $this->dequeueNextChar();
                continue;
            }

            $this->validateNumber($currentNumber, $separator);
            $this->addToSum($currentNumber);
            $currentNumber = '';
        }

        $this->validateLastNumber($currentNumber);
        $this->addToSum($currentNumber);

        if (! empty($this->negativeNumbers)) {
            $this->errors[] = sprintf(self::NEGATIVE_NOT_ALLOWED, implode(',', $this->negativeNumbers));
        }

        if (! empty($this->errors)) {
            throw IncorrectArgument::allMessages($this->errors);
        }

        return (string) $this->sum;
    }

    private function dequeueSeparator() : string
    {
        foreach (self::SEPARATORS as $separator) {
            if (strpos($this->unprocessedChars, $separator) === 0) {
                $length                 = strlen($separator);
                $this->unprocessedChars = substr($this->unprocessedChars, $length);
                $this->currentPosition += $length;

                return $separator;
            }
        }

        return '';
    }

    private function validateNumber(string $currentNumber, string $lastSeparator) : void
    {
        if (empty($currentNumber)) {
            $this->errors[] = sprintf(self::EXPECTED_NUMBER, $lastSeparator, $this->currentPosition);
        }

        $this->validateNegativeNumber($currentNumber);
    }

    private function addToSum(string $tmp) : void
    {
        $this->sum += (float) $tmp;
    }

    private function dequeueNextChar() : string
    {
        $char                   = $this->unprocessedChars[0];
        $this->unprocessedChars = substr($this->unprocessedChars, 1);
        $this->currentPosition++;

        return $char;
    }

    private function validateLastNumber(string $currentNumber) : void
    {
        if (empty($currentNumber)) {
            $this->errors[] = self::EXPECTED_NUMBER_AT_THE_END;
        }

        $this->validateNegativeNumber($currentNumber);
    }

    private function validateNegativeNumber(string $currentNumber) : void
    {
        if (strpos($currentNumber, '-') !== 0) {
            return;
        }

        $this->negativeNumbers[] = $currentNumber;
    }
}
