<?php

namespace ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Http\Driver\Exception\HttpDriverServerException;
use App\Http\Request\HttpInvalidRequestException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use ICareWebPageScraper\Http\Request\GetICareServiceRequest;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginController extends AbstractICareWebPageScraperPluginController
{
    /**
     * @var string - The base path on the iCare website.
     */
    protected $path = '/kb5/hackney/asch/service.page';

    /**
     * This controller does not need to use a selector.
     */
    protected $selectorRequired = true;

    /**
     * ICareWebPageScraperPluginController constructor.
     *
     * @param Request $request
     * @param array $conf
     *
     * @throws HttpInvalidRequestException
     */
    public function __construct()
    {
        parent::__construct();
        $this->conf['path'] = $this->path;
    }

    /**
     * Retrieve stuff from a service page on the Hackney iCare website.
     *
     * @param Request $request
     * @param string $id – Service id on the Hackney iCare web site.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieve(Request $request, $id)
    {
        try {
            $this->readyForKafka();
            $this->request = $request;
            $this->extractQuery();
            $this->setQuery();

            if ($this->selectorRequired && empty($this->query['selector'])) {
                throw new HttpInvalidRequestException('Please set a CSS selector', 422);
            }

            // Create the request/response service.
            $this->makeService();
            // Prepare the request.
            $request = new GetICareServiceRequest();
            $request->setQueryParameter('id', $id);
            // Do the request and get the response.
            $response = $this->service->iCareService($request);
            // Assume the status code from the iCare website is good place to start.
            $status = $this->service->getResponse()->getStatusCode();
            // Build the data to return.
            $build = [
                'message' => $this->service->getResponse()->getStatus(),
                'code' => null,
                'url' => current($response->getResponseHeaders()['X-GUZZLE-EFFECTIVE-URL']),
                'id' => $id,
            ];
            // Get the iCare service page data from the response.
            $html = $response->getData();
            // A crawler tool to traverse the data with a CSS selector.
            $crawler = new Crawler($html);
            // Page title.
            $build['label'] = $crawler->filter('#content > h1')->text();
            // Get the text passed by the CSS selector(s) in the query parameters
            $dom = $this->selector($crawler);
            $build['code'] = $dom['status'];
            $build['response'] = [
                'dom' => empty($dom['items']) ? $dom : $dom['items'],
                'headers' => $response->getResponseHeaders(),
            ];
            return response()->json($build, $status);
        } catch (HttpDriverServerException $e) {
            return $this->exceptionResponse($e);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        } catch (HttpInvalidRequestException $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function create(Request$request, $id)
    {
        try {
            $this->readyForKafka();
            $this->request = $request;
            $this->extractQuery();
            $this->setQuery();
//            $this->makeService();
            $data = [
                'package' => 'icare_webpage_scraper_package',
                'operation' => 'item/{id}',
                'parameters' => [
                    'path' => [
                        'id' => $id,
                    ],
                    'query' => $this->query,
                ],
            ];
            $this->queue->push('App\\Jobs\\ProcessWebPageScrapeJob', $data, $this->getKafkaQueueName());
            return response()->json($data);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
