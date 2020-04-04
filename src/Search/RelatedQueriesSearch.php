<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\RelatedResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedQueriesSearch extends AbstractRelatedSearch
{
    protected function getKeywordType(): string
    {
        return 'QUERY';
    }

    /**
     * @inheritDoc
     */
    protected function createResult(array $data): RelatedResult
    {
        if (!isset($data['query'], $data['value'], $data['link'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Google ranked list does not contain all keys. Only has: %s',
                    implode(', ', array_keys($data))
                )
            );
        }

        return new RelatedResult(
            (string)$data['query'],
            (bool)($data['hasData'] ?? false),
            (int)$data['value'],
            self::TRENDS_URL . (string)$data['link'],
            $this->isRisingMetric($data) ? 'RISING' : 'TOP'
        );
    }

    /**
     * @inheritDoc
     */
    protected function getToken(ExploreResultCollection $resultCollection): string
    {
        return $resultCollection->getRelatedQueriesResult()->getToken();
    }
}
