<?php

namespace Drupal\visualnet_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\ContextProvider\CurrentLanguageContext;
use Drupal\visualnet_api\Model\Ticket;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_api\Service\VisualnetApiService;
use Drupal\visualnet_utility\Service\UtilityService;
use Drupal\visualnet_utility\Utility\StringUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TestController
 *
 * @package Drupal\visualnet_api\Controller
 * @access public
 * @copyright visualnet.pl
 */
class TestController extends ControllerBase
{
    /**
     * @var VisualnetApiService
     */
    private $apiService;

    /**
     * @var VisualnetApiService
     */
    private $utilityService;

    /**
     * @var CurrentLanguageContext
     */
    private $currentLanguageService;

    /**
     * TestController constructor.
     *
     * @param \Drupal\visualnet_api\Service\VisualnetApiService            $apiService
     * @param \Drupal\visualnet_utility\Service\UtilityService             $utilityService
     * @param \Drupal\Core\Language\ContextProvider\CurrentLanguageContext $currentLanguageService
     */
    public function __construct(
        VisualnetApiService $apiService,
        UtilityService $utilityService,
        CurrentLanguageContext $currentLanguageService) {

        $this->apiService             = $apiService;
        $this->utilityService         = $utilityService;
        $this->currentLanguageService = $currentLanguageService;

    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return static
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('visualnet.api_service'),
            $container->get('visualnet.utility_service'),
            $container->get('language.current_language_context')
        );
    }

    /**
     * @return array
     */
    public function testEvent()
    {
        $events = $this->utilityService->getCollectionByType(RequestType::EVENT);

        var_dump(StringUtility::encoder('1234'));

        return [
            '#theme' => 'test',
            '#data'  => $events,
        ];
    }

    /**
     * @return array
     */
    public function testPass()
    {
        $response = $this->apiService->send(RequestType::PASS, []);
        //$response = $this->service->send(RequestType::LOGOUT, []);

        return [
            '#theme' => 'test',
            '#data'  => $response->getContentObject(),
        ];
    }

    /**
     * @return array
     */
    public function testTicket()
    {
        $response = $this->apiService->send(RequestType::TICKET, []);
        //$response = $this->service->send(RequestType::LOGOUT, []);

        $tickets = $response->getContentObject()->tickets;
        $ticket  = new Ticket($tickets[1], $response->getContentObject());

        var_dump($ticket->getId());
        //var_dump($ticket->getRepertoire());

        return [
            '#theme' => 'test',
            '#data'  => $ticket->getRepertoire(),
        ];
    }

    /**
     * @return array
     */
    public function testOrder()
    {
        $response = $this->apiService->send(RequestType::ORDER, []);
        //$response = $this->service->send(RequestType::LOGOUT, []);

        return [
            '#theme' => 'test',
            '#data'  => $response->getContentObject(),
        ];
    }

    /**
     * @return array
     */
    public function testReturnTicket()
    {
        $response = $this->apiService->send(RequestType::TICKET_RETURN, ['id' => 1]);
        //$response = $this->service->send(RequestType::LOGOUT, []);

        return [
            '#theme' => 'test',
            '#data'  => $response->getContentObject(),
        ];
    }

}
