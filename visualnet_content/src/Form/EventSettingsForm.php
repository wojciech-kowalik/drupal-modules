<?php

namespace Drupal\visualnet_content\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class EventSettingsForm.
 *
 * @ingroup visualnet_content
 */
class EventSettingsForm extends ConfigFormBase {

    const EVENT_TYPE_PATH = "/type/";

    const EVENTS_IN_SECTION = 6;

    /**
     * Returns a unique string identifying the form.
     *
     * @return string
     *   The unique string identifying the form.
     */
    public function getFormId() {
        return 'event_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'event.settings',
        ];
    }

    /**
     * Defines the settings form for Event entities.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     *
     * @return array
     *   Form definition array.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('event.settings');

        $form['custom'] = array(
            '#type'  => 'details',
            '#title' => $this->t('Visualnet Event configuration'),
            '#open'  => true,
            '#tree'  => true,
        );
        $form['custom']['visualnet_content_type_path'] = array(
            '#type'          => 'textfield',
            '#title'         => $this->t('Event type path', LanguageUtility::getCurrentLangCode()),
            '#required'      => true,
            '#default_value' => $config->get('visualnet_content_type_path'),
            '#weight'        => 0,
            '#description'   => '',
        );
        $form['custom']['visualnet_content_events_in_section'] = array(
            '#type'          => 'number',
            '#title'         => $this->t('Number of Events in Section', LanguageUtility::getCurrentLangCode()),
            '#required'      => true,
            '#default_value' => $config->get('visualnet_content_events_in_section'),
            '#weight'        => 1,
            '#description'   => 'You set this for path: DOMAIN/type/*',
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * Form submission handler.
     *
     * @param array $form
     *   An associative array containing the structure of the form.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   The current state of the form.
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $config = $this->config('event.settings');
        $custom = $form_state->getValue('custom');

        $max = $custom['visualnet_content_type_path'];
        $config->set('visualnet_content_type_path', $max);

        $max = $custom['visualnet_content_events_in_section'];
        $config->set('visualnet_content_events_in_section', $max);

        $config->save();

        parent::submitForm($form, $form_state);
        drupal_flush_all_caches();
    }

}
