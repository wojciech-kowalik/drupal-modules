<?php

namespace Drupal\visualnet_sliderview\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\visualnet_sliderview\SlideBlock;

/**
 * @Block(
 *   id = "guest_slider_block",
 *   admin_label = @Translation("Slider block for guests"),
 * )
 */
class SlideGuestBlock extends SlideBlock
{
    protected $type = 'guest';

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return $this->response('Guests');
    }

}
