<?php

namespace Drupal\visualnet_content\Builder;

use Drupal\visualnet_content\Builder\EventQueryBuilder;

/**
 * Class EventQueryDirector
 *
 * @package Drupal\visualnet_content\Builder
 */
class EventQueryDirector
{
    /**
     * @var EventQueryBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Drupal\visualnet_content\Builder\EventQueryBuilder $builder
     * @return void
     */
    public function setBuilder(EventQueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return void
     */
    public function buildQuery()
    {
        $this->builder->addJoins();
        $this->builder->addFields();
        $this->builder->addConditions($this->options);
        $this->builder->addOrders($this->options);
    }

}
