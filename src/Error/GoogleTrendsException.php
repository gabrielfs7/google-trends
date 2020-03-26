<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Error;

use Exception;
use Throwable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class GoogleTrendsException extends Exception
{
    private const ERROR_CODE = 19980904; // Google's creation date

    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'GoogleTrends error: %s',
                $message
            ),
            self::ERROR_CODE,
            $previous
        );
    }
}
