<?php

namespace Drupal\visualnet_plugin\Plugin\CKEditorPlugin;

use Drupal\ckeditor\Annotation\CKEditorPlugin;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\Core\Annotation\Translation;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "CKEditorButton" plugin.
 *
 * @CKEditorPlugin (
 *   id = "vtbutton",
 *   label = @Translation("CKEditorButton"),
 *   module = "visualnet_plugin"
 * )
 */
class CKEditorButton extends CKEditorPluginBase
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
        return drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/button/plugin.js';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtons()
    {
        $path = drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/button/icons';
        return array(
            'Button' => array(
                'label' => t('Insert button'),
                'image' => $path . '/button.png',
            ),
        );
    }

}
