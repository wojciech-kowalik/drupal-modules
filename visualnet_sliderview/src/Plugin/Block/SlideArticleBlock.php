<?php

namespace Drupal\visualnet_sliderview\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\visualnet_sliderview\SlideBlock;

/**
 * @Block(
 *   id = "article_slider_block",
 *   admin_label = @Translation("Slider block for articles"),
 * )
 */
class SlideArticleBlock extends SlideBlock
{
    protected $type = 'article';

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        return $this->response('Articles');
    }

}
