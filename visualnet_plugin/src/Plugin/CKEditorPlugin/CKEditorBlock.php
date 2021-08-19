<?php

namespace Drupal\visualnet_plugin\Plugin\CKEditorPlugin;

use Drupal\ckeditor\Annotation\CKEditorPlugin;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\Core\Annotation\Translation;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "CKEditorBlock" plugin.
 *
 * @CKEditorPlugin (
 *   id = "block",
 *   label = @Translation("CKEditorBlock"),
 *   module = "visualnet_plugin"
 * )
 */
class CKEditorBlock extends CKEditorPluginBase
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
        return drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/block/plugin.js';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtons()
    {
        $path = drupal_get_path('module', 'visualnet_plugin') . '/js/plugins/block/icons';
        return array(
            'Block' => array(
                'label' => t('Insert block'),
                'image' => $path . '/block.png',
            ),
        );
    }

}
