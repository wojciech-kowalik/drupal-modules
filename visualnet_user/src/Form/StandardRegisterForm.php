<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class StandardRegisterForm
 *
 * @package Drupal\visualnet_user\Form
 */
class StandardRegisterForm extends RegisterFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'standard_register_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['name'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('Firstname'),
            '#required' => false,
        );

        $form['lastname'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('Lastname'),
            '#required' => false,
        );

        $form['email'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('Email'),
            '#required' => false,
        );

        $form['actions']['#type'] = 'actions';

        $form['actions']['submit'] = array(
            '#type'        => 'submit',
            '#attributes'  => [
                'class' => ['off-button colorized smaller'],
            ],
            '#value'       => $this->t('Register'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $data = [
            'user' => [
                'firstName' => $form_state->getValue('name'),
                'lastName'  => $form_state->getValue('lastname'),
                'email'     => $form_state->getValue('email'),
            ],
        ];

        $response = $this->apiService->send(RequestType::REGISTER, $data, false);

        if ($response->hasError()) {
            drupal_set_message($response->getError(), 'error');
        } else {
            drupal_set_message(
                t('User has been successfully created', LanguageUtility::getCurrentLangCode())
            );
            $this->userService->create($form_state, $response->getContentObject()->user);
        }

    }

}
