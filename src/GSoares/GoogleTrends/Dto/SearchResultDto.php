<?php

namespace GSoares\GoogleTrends\Dto;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 * @package GSoares\GoogleTrends\Dto
 */
class SearchResultDto
{

    /**
     * @var string
     */
    public $searchUrl;

    /**
     * @var integer
     */
    public $totalResults = 0;

    /**
     * @var TermDto[]
     */
    public $results = [];
}