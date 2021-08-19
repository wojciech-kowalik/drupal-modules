<?php

namespace Drupal\visualnet_sliderview\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\visualnet_sliderview\SlideBlock;

/**
 * @Block(
 *   id = "news_slider_block",
 *   admin_label = @Translation("Slider block for news"),
 * )
 */
class SlideNewsBlock extends SlideBlock
{
    protected $type = 'news';

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return $this->response('News');
    }
}
