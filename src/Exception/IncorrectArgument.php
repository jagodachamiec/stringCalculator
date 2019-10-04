<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;
use function count;

final class IncorrectArgument extends Exception
{
    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string[] $errors
     *
     * @return static
     */
    public static function allMessages(array $errors) : self
    {
        $messages = '';
        foreach ($errors as $key => $error) {
            $messages .= $error;
            if (self::isLastItem($errors, $key)) {
                continue;
            }

            $messages .= '\n';
        }

        return new self($messages);
    }

    /**
     * @param string[] $errors
     */
    private static function isLastItem(array $errors, int $key) : bool
    {
        return $key - 1 === count($errors);
    }
}
