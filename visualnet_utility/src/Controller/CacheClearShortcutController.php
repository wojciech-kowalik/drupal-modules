<?php

namespace Drupal\visualnet_utility\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CacheClearShortcutController
 *
 * @package Drupal\visualnet_utility\Controller
 * @access public
 * @copyright visualnet.pl
 */
class CacheClearShortcutController extends ControllerBase
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function make()
    {
        \Drupal::cache('render')->deleteAll();
        \Drupal::logger('visualnet_utility')->notice('Cache has been cleared');
        drupal_set_message(t('Cache has been cleared', LanguageUtility::getCurrentLangCode()));

        return $this->redirect('<front>');
    }

}
