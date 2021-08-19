<?php

namespace Drupal\visualnet_content\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class SectionController
 * @package Drupal\visualnet_content\Controller
 */
class SectionController extends ControllerBase
{
    /**
     * @var Drupal\Core\Entity\EntityStorageInterface|object
     */
    protected $taxonomyStorage;

    /**
     * SectionController constructor.
     */
    public function __construct()
    {
        $this->taxonomyStorage = $this->entityTypeManager()->getStorage("taxonomy_term");
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function show($id)
    {
        $entity = $this->taxonomyStorage->load($id);

        $section = [
            'id'          => $entity->id(),
            'title'       => $entity->get('name')->value,
            'description' => $entity->get('description')->value,
            'color'       => $entity->get('field_color')->color,
            'image_uri'   => $entity->get('field_visual')->entity->uri->value,
        ];

        return array(
            '#section' => $section,
            '#theme'   => 'section',
            '#cache'   => [
                'max-age' => 0,
            ],
        );
    }

}
