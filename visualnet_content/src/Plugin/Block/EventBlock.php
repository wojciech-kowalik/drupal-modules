<?php

namespace Drupal\visualnet_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use function Functional\map;
use function Functional\select;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Event block.
 *
 * @Block(
 *   id = "event_block",
 *   admin_label = @Translation("Event Block"),
 *   category = @Translation("Blocks")
 * )
 */
class EventBlock extends BlockBase implements ContainerFactoryPluginInterface
{

    /**
     * @var \Drupal\Core\Entity\EntityTypeManager
     */
    protected $entityManager;

    /**
     * @var \Drupal\Core\Config\ConfigFactory
     */
    protected $eventConfiguration;

    /**
     * @var \Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $eventStorage;

    /**
     * @var \Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $taxonomyStorage;

    /**
     * @var array
     */
    protected $language;

    /**
     * @var string
     */
    protected $currentUrl;

    /**
     * EventBlock constructor.
     *
     * @param array $configuration
     * @param $plugin_id
     * @param $plugin_definition
     * @param EntityTypeManager $entityManager
     * @param ConfigFactory $configFactory
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EntityTypeManager $entityManager,
        ConfigFactory $configFactory) {

        $currentPath = \Drupal::service('path.current')->getPath();

        $this->entityManager      = $entityManager;
        $this->eventStorage       = $this->entityManager->getStorage("visualnet_event");
        $this->taxonomyStorage    = $this->entityManager->getStorage("taxonomy_term");
        $this->eventConfiguration = $configFactory->get('event.settings');
        $this->language           = LanguageUtility::getCurrentLangCode();
        $this->currentUrl         = \Drupal::service('path.alias_manager')->getAliasByPath($currentPath);
    }

    /**
     * @param ContainerInterface $container
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
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
     * @return array
     */
    public function createEventTypeMenu()
    {

        return $this
            ->taxonomyStorage
            ->loadTree('event_types');
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $eventsNode = $this->eventStorage->loadMultiple();

        $events = map($eventsNode, function ($event) {
            $typeTags    = $event->get('event_type')->referencedEntities();
            $sectionTags = $event->get('event_section')->referencedEntities();

            return [
                'title'          => $event->get('name')->value,
                'title_original' => $event->get('name_original')->value,
                'description'    => $event->get('description')->value,
                'type'           => $typeTags[0]->get('name')->value,
                'section'        => $sectionTags[0]->get('name')->value,
            ];
        });

        $typePath = $this->eventConfiguration->get("visualnet_content_type_path");

        $eventsForCurrentUrl = select($events, function ($event) use($typePath) {

            if ($typePath . $event['type'] == $this->currentUrl) {
                return true;
            }
        });

        $sections = $this->taxonomyStorage->loadTree('event_sections');

        $sectionOutput = [];
        foreach ($sections as $section) {
            $events = select($eventsForCurrentUrl, function ($event) use ($section) {

                if ($event['section'] == $section->name) {
                    return true;
                }
            });

            $sectionOutput[] = [
                'name'   => $section->name,
                'events' => $events,
            ];
        }


        /* ARRAY OUTPUT EXAMPLE for type meeting
        $sections = [
            1 => [
                'name' => "test_1",
                'events' => [
                    1 => [
                        'title' => "event_test_11",
                    ],
                    2 => [
                        'title' => "event_test_12",
                    ],
                ],
            ],
        ];
        */

        return array(
            '#entities' => $sectionOutput,
            '#menu'     => $this->createEventTypeMenu(),
            '#theme'    => 'eventview',
            '#cache'    => [
                'max-age' => 0,
            ],
            '#attached' => [
                'library' => [
                    'visualnet_content/responsive-styling',
                ],
            ],
        );
    }

}