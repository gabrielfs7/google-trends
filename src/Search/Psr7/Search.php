<?php declare(strict_types=1);

namespace GSoares\GoogleTrends\Search\Psr7;

use DateTimeImmutable;
use GSoares\GoogleTrends\Search\SearchFilter;
use GSoares\GoogleTrends\Search\SearchInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * @author Gabriel Felipe Soares <gabrielfs7@gmail.com>
 */
class Search
{
    /**
     * @var SearchInterface
     */
    private $search;

    public function __construct(SearchInterface $search)
    {
        $this->search = $search;
    }

    public function search(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $params = $request->getQueryParams();

            $searchFilter = (new SearchFilter())
                ->withCategory((int)($params["categoryId"] ?? 0))
                ->withSearchTerm($params["searchTerm"] ?? 'google')
                ->withLocation($params["location"] ?? 'US')
                ->withinInterval(
                    new DateTimeImmutable($params["intervalFrom"] ?? 'now -7 days'),
                    new DateTimeImmutable($params["intervalTo"] ?? 'yesterday')
                )
                ->withLanguage($params["language"] ?? 'en-US');

            $this->configureSearchSource($params["searchSource"] ?? 'web', $searchFilter);

            if (filter_var($params["withTopMetrics"] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $searchFilter->withTopMetrics();
            }

            if (filter_var($params["withRisingMetrics"] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                $searchFilter->withRisingMetrics();
            }

            //var_export($searchFilter); exit(); //FIXME
            //var_export(json_encode($this->search->search($searchFilter)->jsonSerialize())); exit(); //FIXME

            return new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                ],
                json_encode($this->search->search($searchFilter)->jsonSerialize())
            );
        } catch (Throwable $exception) {
            return new Response(
                400,
                [
                    'Content-Type' => 'application/json',
                ],
                json_encode(
                    [
                        'errors' => [
                            [
                                'status' => 400,
                                'code' => $exception->getCode(),
                                'title' => $exception->getMessage(),
                                'details' => $exception->getMessage(),
                            ]
                        ]
                    ]
                )
            );
        }
    }

    private function configureSearchSource(string $searchSource, SearchFilter $searchFilter): void
    {
        if ($searchSource === SearchFilter::SEARCH_SOURCE_NEWS) {
            $searchFilter->considerNewsSearch();

            return;
        }

        if ($searchSource === SearchFilter::SEARCH_SOURCE_IMAGES) {
            $searchFilter->considerImageSearch();

            return;
        }

        if ($searchSource === SearchFilter::SEARCH_SOURCE_YOUTUBE) {
            $searchFilter->considerYoutubeSearch();

            return;
        }

        if ($searchSource === SearchFilter::SEARCH_SOURCE_GOOGLE_SHOPPING) {
            $searchFilter->considerGoogleShoppingSearch();

            return;
        }

        $searchFilter->considerWebSearch();
    }
}
