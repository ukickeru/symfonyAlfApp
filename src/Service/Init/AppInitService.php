<?php


namespace App\Service\Init;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RequestStack;

use App\Service\Request\RecognizeRequestService;

class AppInitService
{

    protected $logger;
    protected $requestStack;
    protected $currentRequest;

    protected $requestRecognizer;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack, RecognizeRequestService $requestRecognizer)
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->requestRecognizer = $requestRecognizer;
    }

    public function onKernelRequest(RequestEvent $event)
    {
//        $this->logger->alert('Initializing working!');    // Use to log all requests to app
        $this->currentRequest = $this->requestStack->getCurrentRequest();
        $this->processCurrentRequest();
    }

    public function processCurrentRequest()
    {
//        $this->request;
//        $this->requestRecognizer->test();
        $this->logger->alert($this->requestRecognizer->test());
    }

}