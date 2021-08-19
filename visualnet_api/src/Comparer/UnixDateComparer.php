<?php

namespace Drupal\visualnet_api\Comparer;

use Collections\Generic\ComparerInterface;

class UnixDateComparer implements ComparerInterface
{
    const DIRECTION_ASC  = 'asc';
    const DIRECTION_DESC = 'desc';

    private $direction;

    public function __construct($direction = self::DIRECTION_ASC)
    {
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function compare($first, $second)
    {
        $compare = null;

        switch ($this->direction) {

            case self::DIRECTION_DESC:{
                    $compare = ($first->getUnixDate() < $second->getUnixDate());
                }break;

            case self::DIRECTION_ASC:{
                    $compare = ($first->getUnixDate() > $second->getUnixDate());
                }break;

        }

        return $compare;
    }
}
