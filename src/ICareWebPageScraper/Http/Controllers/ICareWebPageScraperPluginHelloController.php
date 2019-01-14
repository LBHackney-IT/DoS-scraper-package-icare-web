<?php

namespace ICareWebPageScraper\Http\Controllers;

use App\Http\Driver\Exception\HttpDriverClientException;
use App\Plugins\WebPageScraper\Http\WebPageHttpServiceException;

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
            $this->makeService();
            $this->service->webPageCheckConnection();
            $data = [
                'message' => $this->service->getResponse()->getStatus(),
                'code' => $this->service->getResponse()->getStatusCode(),
                'url' => $this->service->getUrl(),
            ];
            $this->queue->push('icare_hello', $data, $this->getKafkaQueueName());
            return response()->json($data);
        } catch (HttpDriverClientException $e) {
            return $this->exceptionResponse($e);
        } catch (WebPageHttpServiceException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
