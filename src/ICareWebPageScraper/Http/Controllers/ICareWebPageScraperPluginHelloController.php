<?php

namespace ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;
use Rapide\LaravelQueueKafka\Queue\Jobs\KafkaJob;
use RdKafa\Message;
use RdKafka\Topic;

/**
 * Class ICareWebPageScraperPluginHelloController
 *
 * @package App\Scraper\ICareWebPageScraper\Http\Controllers
 */
class ICareWebPageScraperPluginHelloController extends AbstractICareWebPageScraperPluginController
{
    
    /**
     * This controller does not need to use a selector.
     */
    protected $selectorRequired = false;

    /**
     * Hello
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function hello()
    {
        try {
            $this->setKafkaQueueName('NewQueue');
            $this->makeService();
            $this->service->webPageCheckConnection();
            $data = [
                'operation' => 'hello',
                'message' => $this->service->getResponse()->getStatus(),
                'code' => $this->service->getResponse()->getStatusCode(),
                'url' => $this->service->getUrl(),
            ];
            $this->queue->push('WebPageScrape', $data, 'NewQueue');
            return response()->json($data);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function view() {
        try {
            $this->makeService();
            $job = $this->queue->pop();
            $data = [
                'queue' => [
                    'config' => $this->queue->getConfig(),
                    'data' => is_null($job) ? null : $job->payload(),
                ],
            ];
            $response = response()->json($data);
            return $response;
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function create()
    {
        try {
            $this->makeService();
            $data = [
                'package' => 'icare_webpage_scraper_package',
                'operation' => 'hello',
                'parameters' => [],
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
