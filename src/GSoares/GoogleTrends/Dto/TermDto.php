<?php

namespace GSoares\GoogleTrends\Dto;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 * @package GSoares\GoogleTrends\Dto
 */
class TermDto
{

    /**
     * @var string
     */
    public $term;

    /**
     * @var float
     */
    public $ranking;

    /**
     * @var string
     */
    public $searchUrl;

    /**
     * @var string
     */
    public $searchImageUrl;

    /**
     * @var string
     */
    public $trendsUrl;
}