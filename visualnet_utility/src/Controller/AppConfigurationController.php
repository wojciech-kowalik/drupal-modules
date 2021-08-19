<?php

namespace Drupal\visualnet_utility\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AppConfigurationController
 *
 * @package Drupal\visualnet_utility\Controller
 * @access public
 * @copyright visualnet.pl
 */
class AppConfigurationController extends ControllerBase
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function mobile()
    {
        $host            = \Drupal::request()->getHost();
        $currentLangCode = LanguageUtility::getCurrentLangCode();
        $scheme          = 'https://';

        $global['language'] = $currentLangCode['langcode'];

        $links = [

            [
                'name'  => $this->t('Buy ticket', $currentLangCode),
                'color' => '#0B0B0B',
                'url'   => [
                    'pl' => $scheme . $host . '/calendar',
                    'en' => $scheme . $host . '/en/calendar',
                ],
            ],

            [
                'name'  => $this->t('Program', $currentLangCode),
                'color' => '#0B0B0B',
                'url'   => [
                    'pl' => $scheme . $host . '/type/movie',
                    'en' => $scheme . $host . '/en/type/movie',
                ],
            ],

            [
                'name'  => $this->t('Guests', $currentLangCode),
                'color' => '#0B0B0B',
                'url'   => [
                    'pl' => $scheme . $host . '/guest',
                    'en' => $scheme . $host . '/en/guest',
                ],
            ],

            [
                'name'  => $this->t('News', $currentLangCode),
                'color' => '#0B0B0B',
                'url'   => [
                    'pl' => $scheme . $host . '/news',
                    'en' => $scheme . $host . '/en/news',
                ],
            ],

            [
                'name'  => $this->t('About festival', $currentLangCode),
                'color' => '#0B0B0B',
                'url'   => [
                    'pl' => $scheme . $host . '/festiwal/o-festiwalu',
                    'en' => $scheme . $host . '/en/festival/about-festival',
                ],
            ],

        ];

        foreach ($links as &$link) {
            $link['url'] = $link['url'][$currentLangCode['langcode']];
        }

        $build = [
            '#theme' => 'mobile',
            '#data'  => ['links' => $links, 'global' => $global],
            '#cache' => ['max-age' => 0],
        ];

        $output = \Drupal::service('renderer')->renderRoot($build);

        $response = new Response();
        $response->setContent($output);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;

    }

}
