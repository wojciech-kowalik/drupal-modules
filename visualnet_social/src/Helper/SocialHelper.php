<?php

namespace Drupal\visualnet_social\Helper;

/**
 * Class SocialHelper
 *
 * @package Drupal\visualnet_social\Helper
 */
class SocialHelper
{
    /**
     * Social constants
     */
    const FACEBOOK = [
        'name'      => 'facebook',
        'url'       => 'https://www.facebook.com',
        'share_uri' => '/sharer/sharer.php?u=',
        'fa_icon'   => 'fa-facebook-f',
    ];

    const TWITTER = [
        'name'      => 'twitter',
        'url'       => 'https://twitter.com',
        'share_uri' => '/home?status=',
        'fa_icon'   => 'fa-twitter',
    ];

    const GOOGLE_PLUS = [
        'name'      => 'google-plus',
        'url'       => 'https://plus.google.com',
        'share_uri' => '/share?url=',
        'fa_icon'   => 'fa-google-plus-g',
    ];

    /**
     * @var array
     */
    private $socialCollection = [];

    /**
     * SocialHelper constructor.
     */
    public function __construct()
    {
        $this->makeSocials();
    }

    /**
     * @return array
     */
    public static function getAvailable()
    {
        return [
            self::FACEBOOK,
            self::TWITTER,
            self::GOOGLE_PLUS,
        ];
    }

    /**
     * @param $social
     *
     * @return bool
     */
    public static function isSupported($social)
    {
        return (in_array(mb_strtoupper($social), static::getAvailable()));
    }

    /**
     * @return array
     */
    public function getSelectedFromConfiguration()
    {
        $socials = \Drupal::config('visualnet_social.settings')
            ->get('visualnet_social_widget_types');

        return array_filter(self::getAvailable(), function ($key) use ($socials) {
            return in_array($key, $socials);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return array
     */
    public function getAvailableCollection()
    {
        return ($this->socialCollection) ? $this->socialCollection : [];
    }

    /**
     * @return void
     */
    private function makeSocials()
    {
        array_map(function ($social) {
            $this->addSocial($social['name']);
        }, $this->getAvailable());

    }

    /**
     * @param $name
     */
    private function addSocial($name)
    {
        $this->isSupported($name);
        array_push($this->socialCollection, $name);
    }

}
