<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_utility\Utility\LanguageUtility;

/**
 * Class PassRegisterForm
 *
 * @package Drupal\visualnet_user\Form
 */
class PassRegisterForm extends RegisterFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'pass_register_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['pass'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('Pass number'),
            '#required' => true,
        );

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
            '#value'       => $this->t('Register'),
            '#attributes'  => [
                'class' => ['off-button colorized smaller'],
            ],
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
                'barcode'   => $form_state->getValue('pass'),
                'email'     => $form_state->getValue('email'),
                'is_active' => true,
            ],
        ];

        $response = $this->apiService->send(RequestType::REGISTER, $data, false);

        if ($response->hasError()) {
            drupal_set_message($response->getError(), 'error');
        } else {
            drupal_set_message(
                t('Thank you for registering. The first part of the process has been completed successfully. We have sent you an e-mail with an instruction describing what to do next',
                    LanguageUtility::getCurrentLangCode())
            );
            $this->userService->create($form_state, $response->getContentObject()->user);
        }
    }
}
