<?php

namespace Drupal\visualnet_user\RouteSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class UserRouteSubscriber
 *
 * @package Drupal\visualnet_user\RouteSubscriber
 */
class UserRouteSubscriber extends RouteSubscriberBase
{
    /**
     * {@inheritdoc}
     */
    protected function alterRoutes(RouteCollection $collection)
    {
//        if ($route = $collection->get('user.login')) {
        //            $route->setPath('/login');
        //        }
    }
}
