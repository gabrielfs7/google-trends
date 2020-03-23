<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Result;

use JsonSerializable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class ExploreResult
{
    public const TYPE_INTEREST_OVER_TIME = 'TIMESERIES';
    public const TYPE_INTEREST_BY_REGION = 'GEO_MAP';
    public const TYPE_RELATED_TOPICS = 'RELATED_TOPICS';
    public const TYPE_RELATED_QUERIES = 'RELATED_QUERIES';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $token;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRelatedQueriesSearch(): bool
    {
        return self::TYPE_RELATED_QUERIES === $this->id;
    }
}
