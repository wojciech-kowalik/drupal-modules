<?php

namespace Drupal\visualnet_api;

/**
 * Class VisualnetSession
 *
 * @package Drupal\visualnet_api
 * @access public
 * @copyright visualnet.pl
 */
class VisualnetSession
{
    /**
     * @var string
     */
    const SESSION_KEY_NAME = 'api_session_data';

    /**
     * @var string
     */
    const SESSION_HASH_KEY     = 'hash';
    const IS_AUTHENTICATED_KEY = 'is_autenticated';

    /**
     * @var Object
     */
    private $store;

    /**
     * VisualnetSession constructor.
     */
    public function __construct()
    {
        $this->store = \Drupal::service('user.private_tempstore')
            ->get('visualnet_api');

    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key = self::SESSION_HASH_KEY)
    {
        $data = $this->store->get(self::SESSION_KEY_NAME);
        return (isset($data[$key])) ? $data[$key] : md5(uniqid());
    }

    /**
     * @return void
     */
    public function remove()
    {
        $this->store->delete(self::SESSION_KEY_NAME);
    }

    /**
     * @param array $data
     */
    public function set($data)
    {
        $this->store->set(self::SESSION_KEY_NAME, $data);
    }

}
