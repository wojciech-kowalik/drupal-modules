<?php

namespace Drupal\visualnet_api;

use GuzzleHttp\Cookie\CookieJar;

/**
 * Class VisualnetCookie
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
class VisualnetCookie
{
    /**
     * @var \Drupal\visualnet_api\VisualnetSession
     */
    private $session;

    /**
     * VisualnetCookie constructor.
     *
     * @param \Drupal\visualnet_api\VisualnetSession $session
     */
    public function __construct(VisualnetSession $session)
    {
        $this->session = $session;
    }

    /**
     * @param $data
     */
    public function set(&$data)
    {
        $protocols = ['http://', 'https://'];

        $cookieJar = CookieJar::fromArray([
            'PHPSESSID' => $this->session->get(),
        ], str_replace($protocols, '', getenv('CRM_API_URL')));

        $data['cookies'] = $cookieJar;
    }

}
