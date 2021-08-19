<?php

namespace Drupal\visualnet_social\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\visualnet_social\Helper\SocialHelper;

/**
 * @Block(
 *   id = "social_widget_block",
 *   admin_label = @Translation("Social widget block")
 * )
 */
class SocialWidgetBlock extends BlockBase
{
    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $social = new SocialHelper();

        return [
            '#theme'    => 'widget',
            '#url'      => \Drupal::request()->getSchemeAndHttpHost() . \Drupal::request()->getRequestUri(),
            '#entities' => $social->getSelectedFromConfiguration(),
            '#attached' => [
                'library' => [
                    'visualnet_social/visualnet_social.assets',
                ],
            ],
        ];

    }

}
