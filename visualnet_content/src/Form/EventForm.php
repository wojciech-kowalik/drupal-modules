<?php

namespace Drupal\visualnet_content\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Event edit forms.
 *
 * @ingroup visualnet_content
 */
class EventForm extends ContentEntityForm
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        /* @var $entity \Drupal\visualnet_content\Entity\Event */
        $form = parent::buildForm($form, $form_state);

//        $utilityService = \Drupal::service('visualnet.utility_service');
        //        $events         = $utilityService->getCollectionByType(RequestType::EVENT);
        //        $entity         = $this->entity;
        //
        //        foreach ($events as $event) {
        //            $form['system_id']['widget']['#options'][$event->getId()] = $event->getTitle();
        //        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function save(array $form, FormStateInterface $form_state)
    {
        $entity = $this->entity;

        $status = parent::save($form, $form_state);

        switch ($status) {
            case SAVED_NEW:
                drupal_set_message($this->t('Created the %label Event.', [
                    '%label' => $entity->label(),
                ]));
                break;

            default:
                drupal_set_message($this->t('Saved the %label Event.', [
                    '%label' => $entity->label(),
                ]));
        }
        $form_state->setRedirect('entity.visualnet_event.canonical', ['visualnet_event' => $entity->id()]);
    }

}
