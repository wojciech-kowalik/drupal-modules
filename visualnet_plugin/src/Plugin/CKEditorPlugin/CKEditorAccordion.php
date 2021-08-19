<?php

namespace Drupal\visualnet_plugin\Plugin\CKEditorPlugin;

use Drupal\ckeditor\Annotation\CKEditorPlugin;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\Core\Annotation\Translation;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "CKEditorAccordion" plugin.
 *
 * @CKEditorPlugin (
 *   id = "accordion",
 *   label = @Translation("CKEditorAccordion"),
 *   module = "visualnet_plugin"
 * )
 */
class CKEditorAccordion extends CKEditorPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function getConfig(Editor $editor)
    {
        $config = array();
        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(Editor $editor)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/accordion/plugin.js';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtons()
    {
        $path = drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/accordion/icons';
        return array(
            'Accordion' => array(
                'label' => t('Insert accordion'),
                'image' => $path . '/accordion.png',
            ),
        );
    }

}
