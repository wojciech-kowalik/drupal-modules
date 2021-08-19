<?php

namespace Drupal\visualnet_sliderview\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class VisualnetSliderviewForm
 *
 * @package Drupal\visualnet_sliderview\Form
 */
class VisualnetSliderviewForm extends ConfigFormBase
{
    const MAX_SLIDER_ELEMENTS = 10;

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'visualnet_sliderview_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'visualnet_sliderview.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config('visualnet_sliderview.settings');

        $form['custom'] = array(
            '#type'  => 'details',
            '#title' => $this->t('Visualnet Sliderview configuration'),
            '#open'  => true,
            '#tree'  => true,
        );

        $form['custom']['visualnet_sliderview_max_elements'] = array(
            '#type'          => 'textfield',
            '#title'         => $this->t('Maximum elements on the slider', LanguageUtility::getCurrentLangCode()),
            '#required'      => true,
            '#default_value' => $config->get('visualnet_sliderview_max_elements'),
            '#weight'        => 0,
            '#description'   => '',
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('visualnet_sliderview.settings');
        $custom = $form_state->getValue('custom');

        $max = $custom['visualnet_sliderview_max_elements'];
        $config->set('visualnet_sliderview_max_elements', $max);
        $config->save();

        parent::submitForm($form, $form_state);
        drupal_flush_all_caches();
    }

}
