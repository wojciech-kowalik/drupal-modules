<?php

namespace Drupal\visualnet_utility\Service;

use Collections\Vector;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\visualnet_api\Exception\NotSupportedRequestException;
use Drupal\visualnet_api\Exception\ValueNotExistsException;
use Drupal\visualnet_api\QualifiedClassGenerator;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_api\Service\VisualnetApiService;

/**
 * Class UtilityService
 *
 * @package Drupal\visualnet_utility\Service
 */
class UtilityService
{
    const TICKETS_PROPERTY     = 'tickets';
    const ORDERS_PROPERTY      = 'orders';
    const EVENTS_PROPERTY      = 'events';
    const REPERTOIRES_PROPERTY = 'repertoires';

    /**
     * @var Drupal\Core\Entity\EntityTypeManager
     */
    private $entityManager;

    /**
     * @var \Drupal\visualnet_api\Service\VisualnetApiService
     */
    private $api;

    public function __construct(EntityTypeManager $entityManager, VisualnetApiService $api)
    {
        $this->entityManager = $entityManager;
        $this->api           = $api;
    }

    /**
     * @param            $type
     * @param array      $data
     * @param bool|FALSE $checkLogin
     *
     * @return \Collections\Vector
     * @throws \Drupal\visualnet_api\Exception\NotSupportedRequestException
     */
    public function getCollectionByType($type, $data = [], $checkLogin = false)
    {
        if (!RequestType::isAvailable($type)) {
            throw new NotSupportedRequestException('Request type not exists');
        }

        $collection = new Vector();

        try {

            $property  = null;
            $className = QualifiedClassGenerator::getName(
                $type,
                QualifiedClassGenerator::MODEL_TYPE
            );

            switch ($type) {
                case RequestType::ORDER:
                    $property = self::ORDERS_PROPERTY;
                    break;
                case RequestType::TICKET:
                    $property = self::TICKETS_PROPERTY;
                    break;
                case RequestType::EVENT:
                    $property = self::EVENTS_PROPERTY;
                    break;
                case RequestType::REPERTOIRE:
                    $property = self::REPERTOIRES_PROPERTY;
                    break;
                default:break;
            }

            $response      = $this->api->send($type, $data, $checkLogin);
            $contentObject = $response->getContentObject();

            $items = $contentObject->{$property};

            if ($items) {

                foreach ($items as $item) {
                    $collection->add(new $className($item, $contentObject));
                }

            }

        } catch (ValueNotExistsException $e) {
            drupal_set_message($e->getMessage(), 'error');
        } catch (\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
        }

        return $collection;

    }

}
