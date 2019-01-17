<?php

namespace ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Http\Driver\Exception\HttpDriverServerException;
use App\Http\Request\HttpInvalidRequestException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use App\Plugins\WebPageScraper\Service\ParameterExtractor\ParameterExtractJobQuery;
use http\Exception\InvalidArgumentException;
use ICareWebPageScraper\Http\Request\GetICareServiceRequest;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ICareWebPageScraperPluginController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginServiceController extends AbstractICareWebPageScraperPluginController
{
    /**
     * @var string - The base path on the iCare website.
     */
    protected $path = '/kb5/hackney/asch/service.page';

    /**
     * @var string
     */
    protected $kafkaProduceQueue = 'NewQueue';

    protected $parameters;

    /**
     * ICareWebPageScraperPluginController constructor.
     *
     * @throws HttpInvalidRequestException
     */
    public function __construct()
    {

    }

    /**
     * Retrieve stuff from a service page on the Hackney iCare website.
     *
     * @param array $parameters – Array of task parameters.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws InvalidArgumentException
     */
    public function retrieve($parameters)
    {
        try {
            if (empty($parameters['path']['id'])) {
                throw new InvalidArgumentException('ID parameter missing');
            }
            $this->parameters = $parameters;
            $this->readyForKafka();
            $this->setKafkaQueueName($this->kafkaProduceQueue);
            $this->extractQuery();
            $this->setQuery();

            if ($this->selectorRequired && empty($this->query['selector'])) {
                throw new HttpInvalidRequestException('Please set a CSS selector', 422);
            }

            $id = $this->parameters['path']['id'];
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
            $this->queue->push('WebPageScrape', $build, 'NewQueue');
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

    protected function extractQuery()
    {
        $queryExtractor = new ParameterExtractJobQuery($this->parameters);
        $this->query = $queryExtractor->getQuery();
    }
}
