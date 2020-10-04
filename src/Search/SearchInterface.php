<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search;

use GSoares\GoogleTrends\Error\GoogleTrendsException;
use GSoares\GoogleTrends\Result\AbstractResultCollection;
use GSoares\GoogleTrends\Result\ResultCollectionInterface;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
interface SearchInterface
{
    /**
     * @param SearchFilter $searchFilter
     *
     * @return AbstractResultCollection
     *
     * @throws GoogleTrendsException
     */
    public function search(SearchFilter $searchFilter): ResultCollectionInterface;
}
