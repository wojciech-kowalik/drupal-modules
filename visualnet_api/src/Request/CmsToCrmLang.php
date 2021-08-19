<?php 
namespace Drupal\visualnet_api\Request;

use Drupal\visualnet_utility\Utility\LanguageUtility;

trait CmsToCrmLang
{
    public function getCrmLangCode()
    {
        $currentLangcode = LanguageUtility::getCurrentLangCode()['langcode'];
        return ($currentLangcode == 'pl') ? 'pl-PL' : 'en-US';
    }
}
