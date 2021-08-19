<?php

namespace Drupal\visualnet_utility\Utility;

/**
 * Class LanguageUtility
 *
 * @package Drupal\visualnet_utility\Utility
 */
class LanguageUtility
{
    /**
     * @return array
     */
    public static function getCurrentLangCode()
    {
        $currentLanguage =
        \Drupal::languageManager()->getCurrentLanguage()->getId();

        return ['langcode' => $currentLanguage];

    }
}
