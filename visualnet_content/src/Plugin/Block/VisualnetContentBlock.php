<?php

namespace Drupal\visualnet_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides visualnet_content block.
 *
 * @Block(
 *   id = "visualnet_content",
 *   admin_label = @Translation("Visualnet Content"),
 *   category = @Translation("Blocks")
 * )
 */
class VisualnetContentBlock extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return array(
            '#type'     => 'markup',
            '#markup'   => '',
            '#attached' => array(
                'library' => array(
                    'visualnet_content/responsive-styling',
                ),
            ),
        );
    }

}
