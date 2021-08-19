<?php

namespace Drupal\visualnet_content\Service;

use Drupal\Core\Database\Connection;
use Drupal\image\Entity\ImageStyle;
use Drupal\visualnet_content\Exception\TypeNotExistsException;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class TypeService
 *
 * @package Drupal\visualnet_content\Service
 * @deprecated for learning purpose only
 */
class TypeService
{
    /**
     * Types
     * @param string
     */
    const TYPE_ARTICLE = 'article';
    const TYPE_NEWS    = 'news';
    const TYPE_GUEST   = 'guest';

    /**
     * Image
     * @param string
     */
    const IMAGE_TYPE_STYLE = 'node_image_style';

    /**
     * @var \Drupal\Core\Database\Connection
     */
    private $connection;

    /**
     * TypeService constructor.
     *
     * @param \Drupal\Core\Database\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $type
     *
     * @return bool
     */
    public function isAvailable($type)
    {
        $class     = new \ReflectionClass(static::class);
        $available = $class->getConstants();
        unset($available['IMAGE_TYPE_STYLE']);

        return in_array($type, $available);
    }

    /**
     * @param array $parameters
     *
     * @return mixed
     * @throws \Drupal\visualnet_content\Exception\TypeNotExistsException
     */
    public function getTaxonomyIdByName(array $parameters)
    {
        if (!$this->isAvailable($parameters['type'])) {
            throw new TypeNotExistsException('Type not supported');
        }

        $query = $this->connection->select('taxonomy_term_field_data', 'ttfd');
        $query->fields('ttfd');
        $query->condition('ttfd.name', $parameters['type']);

        $taxonomy = $query->execute()->fetchObject();

        return $taxonomy->tid;
    }

    /**
     * @param array $parameters
     */
    public function get(array $parameters)
    {
        $taxonomyId      = $this->getTaxonomyIdByName($parameters);
        $currentLanguage = LanguageUtility::getCurrentLangCode();

        $query = $this->connection->select('node', 'n');
        $query
            ->fields('n', ['langcode'])
            ->fields('nb', ['body_value', 'body_summary'])
            ->fields('nfd', ['title', 'created'])
            ->fields('fm', ['uri', 'filename']);

        $query->innerJoin('node__body', 'nb', 'n.nid = nb.entity_id');
        $query->innerJoin('node_field_data', 'nfd', 'n.nid = nfd.nid');
        $query->innerJoin('taxonomy_index', 'ti', 'n.nid = ti.nid');
        $query->leftJoin('node__field_image', 'nfi', 'n.nid = nfi.entity_id');
        $query->leftJoin('file_managed', 'fm', 'nfi.field_image_target_id = fm.fid');

        $query->condition('n.type', 'article');
        $query->condition('ti.tid', $taxonomyId);
        $query->condition('n.nid', $parameters['id']);
        $query->condition('n.langcode', $currentLanguage['langcode']);

        $item = $query->execute()->fetchObject();

        if (!$item) {
            throw new \InvalidArgumentException('No data');
        }

        $item->type      = $parameters['type'];
        $item->image_url = ImageStyle::load(self::IMAGE_TYPE_STYLE)
            ->buildUrl($item->uri);

        return $item;
    }

}
