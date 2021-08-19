<?php

namespace Drupal\visualnet_social\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_social\Helper\SocialHelper;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class VisualnetSocialForm
 *
 * @package Drupal\visualnet_social\Form
 */
class VisualnetSocialForm extends ConfigFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'visualnet_social_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            'visualnet_social.settings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $social = new SocialHelper();
        $config = $this->config('visualnet_social.settings');

        $form['youtube'] = array(
            '#type'  => 'details',
            '#title' => $this->t('Youtube configuration'),
            '#open'  => false,
            '#tree'  => true,
        );

        $form['widget'] = array(
            '#type'  => 'details',
            '#title' => $this->t('Widget configuration'),
            '#open'  => true,
            '#tree'  => true,
        );

        $form['widget']['visualnet_social_widget_types'] = array(
            '#type'          => 'checkboxes',
            '#title'         => $this->t('Type of visible social media'),
            '#options'       => $social->getAvailableCollection(),
            '#default_value' => ($config->get('visualnet_social_widget_types'))
            ? $config->get('visualnet_social_widget_types') : [],

        );

        $form['youtube']['visualnet_social_youtube_playlist_id'] = array(
            '#type'          => 'textfield',
            '#title'         => $this->t('Youtube playlist id', LanguageUtility::getCurrentLangCode()),
            '#required'      => false,
            '#default_value' => $config->get('visualnet_social_youtube_playlist_id'),
            '#weight'        => 0,
            '#description'   => '',
        );

        $form['youtube']['visualnet_social_youtube_key'] = array(
            '#type'          => 'textfield',
            '#title'         => $this->t('Youtube key value', LanguageUtility::getCurrentLangCode()),
            '#required'      => false,
            '#default_value' => $config->get('visualnet_social_youtube_key'),
            '#weight'        => 1,
            '#description'   => '',
        );

        $form['youtube']['visualnet_social_youtube_max_results'] = array(
            '#type'          => 'textfield',
            '#title'         => $this->t('Maximum number of Youtube results', LanguageUtility::getCurrentLangCode()),
            '#required'      => false,
            '#default_value' => $config->get('visualnet_social_youtube_max_results'),
            '#weight'        => 2,
            '#description'   => '',
        );

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $config = $this->config('visualnet_social.settings');

        // youtube data
        $youtube    = $form_state->getValue('youtube');
        $playlistId = $youtube['visualnet_social_youtube_playlist_id'];
        $key        = $youtube['visualnet_social_youtube_key'];
        $maxResults = $youtube['visualnet_social_youtube_max_results'];
        $config->set('visualnet_social_youtube_playlist_id', $playlistId);
        $config->set('visualnet_social_youtube_key', $key);
        $config->set('visualnet_social_youtube_max_results', $maxResults);

        // widget data
        $widget = $form_state->getValue('widget');
        $types  = $widget['visualnet_social_widget_types'];

        // remove empty types
        foreach ($types as $id => $type) {
            if (!is_string($type)) {
                unset($types[$id]);
            }
        }

        $config->set('visualnet_social_widget_types', $types);
        $config->save();

        parent::submitForm($form, $form_state);
        drupal_flush_all_caches();
    }

}
