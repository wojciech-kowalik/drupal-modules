<?php

namespace Drupal\visualnet_utility\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class LanguageShortcutController
 *
 * @package Drupal\visualnet_utility\Controller
 * @access public
 * @copyright visualnet.pl
 */
class LanguageShortcutController extends ControllerBase
{
    const LANGUAGE_PL = 'pl';
    const LANGUAGE_EN = 'en';

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function change()
    {
        $current = LanguageUtility::getCurrentLangCode();
        $path    = '/';

        switch ($current['langcode']) {
            case self::LANGUAGE_PL:{
                    $path = '/' . self::LANGUAGE_EN;
                }break;

            case self::LANGUAGE_EN:{
                    $path = '/';
                }break;

            default:break;
        }

        drupal_set_message(t('Language has been changed', $current));

        return new RedirectResponse($path);

    }

}
