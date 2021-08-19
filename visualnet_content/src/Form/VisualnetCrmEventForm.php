<?php

namespace Drupal\visualnet_content\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Visualnet crm event edit forms.
 *
 * @ingroup visualnet_content
 */
class VisualnetCrmEventForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\visualnet_content\Entity\VisualnetCrmEvent */
    $form = parent::buildForm($form, $form_state);

    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Visualnet crm event.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Visualnet crm event.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.visualnet_crm_event.canonical', ['visualnet_crm_event' => $entity->id()]);
  }

}
