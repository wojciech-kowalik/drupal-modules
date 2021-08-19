<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_user\Exception\VisualnetUserNotExistsException;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Drupal\visualnet_utility\Utility\ModuleUtility;

/**
 * Class ActivateForm
 *
 * @package Drupal\visualnet_user\Form
 */
class ActivateForm extends RegisterFormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'activate_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['password'] = array(
            '#type'     => 'password',
            '#title'    => $this->t('Password'),
            '#required' => true,
        );

        $form['repeat_password'] = array(
            '#type'     => 'password',
            '#title'    => $this->t('Repeat password'),
            '#required' => true,
        );

        $form['actions']['#type'] = 'actions';

        $form['actions']['submit'] = array(
            '#type'        => 'submit',
            '#value'       => $this->t('Send'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if ($form_state->getValue('password')
            !== $form_state->getValue('repeat_password')) {

            $form_state->setErrorByName(
                'repeat_password',
                $this->t('Repeated password is not the same')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        try {

            $parameters = $this->parametersGenerator();

            $data = [
                'hash' => $parameters['hash'],
                'user' => [
                    'email'    => $parameters['email'],
                    'password' => $form_state->getValue('password'),
                ],
            ];

            $response = $this->apiService->send(RequestType::ACTIVATE, $data, false);

            if ($response->hasError()) {

                drupal_set_message(
                    $this->t(
                        'Thank you, the registration process was successful and you can log in now',
                        LanguageUtility::getCurrentLangCode())
                );

                $this->userService->activate($parameters, $form_state);
            }

        } catch (\InvalidArgumentException $e) {

            ModuleUtility::exceptionSupport($e);

        } catch (VisualnetUserNotExistsException $e) {

            ModuleUtility::exceptionSupport($e);
        }

        $form_state->setRedirect('visualnet_user.login');

    }

    /**
     * @return array
     */
    private function parametersGenerator()
    {
        $query = \Drupal::request()->query;
        $hash  = $query->get('hash');
        $email = str_replace(' ', '+', $query->get('email'));

        if (!isset($hash)) {
            throw new \InvalidArgumentException('Hash parameter required');
        }

        if (!isset($email)) {
            throw new \InvalidArgumentException('Email parameter required');
        }

        return ['hash' => $hash, 'email' => $email];
    }

}
