<?php

namespace Drupal\visualnet_content\Builder;

/**
 * @package Drupal\visualnet_content\Builder
 */
interface QueryBuilderInterface
{
    /**
     * Add joins to query
     *
     * @return void
     */
    public function addJoins();

    /**
     * Add fields to select
     *
     * @return void
     */
    public function addFields();

    /**
     * Set proper order for query
     *
     * @param array $options
     * @return void
     */
    public function addOrders(array $options);

    /**
     * Add conditions for query
     *
     * @param array $options
     * @return void
     */
    public function addConditions(array $options);
}
