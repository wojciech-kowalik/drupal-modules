<?php

namespace Drupal\visualnet_sliderview;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use function Functional\map;
use function Functional\select;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SlideBlock
 *
 * @package Drupal\visualnet_sliderview
 */
abstract class SlideBlock extends BlockBase implements ContainerFactoryPluginInterface
{
    /**
     * @var string
     */
    const DB_ENTITY_NAME = 'node';
    const IMAGE_STYLE    = 'sliderview_style';
    const MODULE_NAME    = 'visualnet_sliderview';

    /**
     * @var \Drupal\Core\Entity\EntityTypeManager
     */
    protected $entityManager;

    /**
     * @var \Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $storage;

    /**
     * @var \Drupal\Core\Config\ConfigFactory
     */
    protected $slideConfiguration;

    /**
     * @var array
     */
    protected $language;

    /**
     * @var string
     */
    protected $type = 'default';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array                                                     $configuration
     * @param string                                                    $plugin_id
     * @param mixed                                                     $plugin_definition
     *
     * @return static
     */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition) {

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('entity_type.manager'),
            $container->get('config.factory')
        );
    }

    /**
     * SlideBlock constructor.
     *
     * @param array                                 $configuration
     * @param string                                $plugin_id
     * @param mixed                                 $plugin_definition
     * @param \Drupal\Core\Entity\EntityTypeManager $entityManager
     * @param \Drupal\Core\Config\ConfigFactory     $configFactory
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EntityTypeManager $entityManager,
        ConfigFactory $configFactory) {

        $this->entityManager      = $entityManager;
        $this->storage            = $this->entityManager->getStorage(self::DB_ENTITY_NAME);
        $this->slideConfiguration = $configFactory->get('visualnet_sliderview.settings');
        $this->language           = LanguageUtility::getCurrentLangCode();
    }

    /**
     * @param $entity
     *
     * @return array
     */
    protected function makeImageData($entity)
    {
        return [
            'src' => ImageStyle::load(self::IMAGE_STYLE)->buildUrl($entity->get('field_image')->entity->uri->value),
            'alt' => $entity->get('field_image')->alt,
        ];
    }

    /**
     * @return array
     */
    protected function populate()
    {
        $nodeIds = $this->storage->getQuery()
            ->condition('type', 'article', '=')
            ->condition('status', 1, '=')
            ->condition('langcode', LanguageUtility::getCurrentLangCode()['langcode'], '=')
            ->sort('created', 'DESC')
            ->execute();

        $nodes = $this->storage->loadMultiple($nodeIds);

        $entities = map($nodes, function ($article) {

            $nodeUrl = Url::fromRoute('entity.node.canonical', ['node' => $article->id()], ['absolute' => true]);
            $terms   = $article->get('field_tags')->referencedEntities();

            $tags = map($terms, function ($term) {
                return [
                    'name' => $term->get('name')->value,
                ];
            });

            return [
                'title' => $article->get('title')->value,
                'body'  => $article->get('body')->value,
                'date'  => $article->get('created')->value,
                'image' => $this->makeImageData($article),
                'url'   => $nodeUrl->toString(),
                'tags'  => $tags,
            ];
        });

        $entitiesOfType = select($entities, function ($entity) {
            foreach ($entity['tags'] as $tag) {
                if ($this->type == $tag['name']) {
                    return true;
                }

            }
        });

        return array_slice(
            $entitiesOfType,
            0,
            $this->slideConfiguration->get('visualnet_sliderview_max_elements')
        );
    }

    /**
     * @return string
     */
    private function getLanguageToUrl()
    {
        $current = $this->language['langcode'];

        if ('en' == $current) {
            return '/' . $current;
        }
    }

    /**
     * @param $title
     *
     * @return array
     */
    protected function response($title)
    {
        return array(
            '#title'           => $this->t($title, $this->language),
            '#theme'           => 'sliderview',
            '#entities'        => $this->populate(),
            '#node'            => $this->type,
            '#language_prefix' => $this->getLanguageToUrl(),
            '#attached'        => [
                'library' => [
                    'visualnet_sliderview/visualnet_sliderview.sliderview_assets',
                ],
            ],
        );
    }

}
