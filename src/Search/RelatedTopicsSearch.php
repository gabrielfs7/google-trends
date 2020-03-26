<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\RelatedResult;
use GSoares\GoogleTrends\Result\ExploreResultCollection;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class RelatedTopicsSearch extends AbstractRelatedSearch
{
    protected function getKeywordType(): string
    {
        return 'ENTITY';
    }

    /**
     * @inheritDoc
     */
    protected function createResult(array $data): RelatedResult
    {
        if (!isset($data['topic']['title'], $data['topic']['type'], $data['value'], $data['link'])) {
            throw new GoogleTrendsException(
                sprintf(
                    'Google ranked list does not contain all keys. Only has: %s',
                    implode(', ', array_keys($data))
                )
            );
        }

        return new RelatedResult(
            (string)$data['topic']['title'] . ' - ' . $data['topic']['type'],
            (bool)($data['hasData'] ?? false),
            (int)$data['value'],
            self::TRENDS_URL . (string)$data['link']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getToken(ExploreResultCollection $resultCollection): string
    {
        return $resultCollection->getRelatedTopicsResult()->getToken();
    }
}
