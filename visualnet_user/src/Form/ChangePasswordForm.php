<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Drupal\visualnet_api\Service\VisualnetApiService;
use Drupal\visualnet_user\Service\UserService;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Drupal\visualnet_utility\Utility\ModuleUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ChangePasswordForm
 *
 * @package Drupal\visualnet_user\Form
 */
class ChangePasswordForm extends FormBase
{
    /**
     * ChangePasswordForm constructor.
     *
     * @param \Drupal\visualnet_user\Service\UserService        $userService
     * @param \Drupal\visualnet_api\Service\VisualnetApiService $apiService
     */
    public function __construct(
        UserService $userService,
        VisualnetApiService $apiService
    ) {

        $this->user = $userService;
        $this->api  = $apiService;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('visualnet.user_service'),
            $container->get('visualnet.api_service')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'change_password_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        //        $form['old_password'] = array(
        //            '#type'     => 'textfield',
        //            '#title'    => $this->t('Old password', LanguageUtility::getCurrentLangCode()),
        //            '#required' => true,
        //        );

        $form['new_password'] = array(
            '#type'     => 'password',
            '#title'    => $this->t('New password', LanguageUtility::getCurrentLangCode()),
            '#required' => true,
        );

        $form['repeated_new_password'] = array(
            '#type'     => 'password',
            '#title'    => $this->t('Repeated new password', LanguageUtility::getCurrentLangCode()),
            '#required' => true,
        );

        $form['actions']['#type'] = 'actions';

        $form['actions']['submit'] = array(
            '#type'        => 'submit',
            '#value'       => $this->t('Send'),
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
    public function validateForm(array &$form, FormStateInterface $form_state)
    {

        if ($form_state->getValue('new_password')
            !== $form_state->getValue('repeated_new_password')) {
            $form_state->setErrorByName('new_password', $this->t(
                "Passwords doesn't match",
                LanguageUtility::getCurrentLangCode()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        try {

            //            $response = $this->api->send(RequestType::CHANGE_PASSWORD,
            //                [
            //                    'oldPassword'         => $form_state->getValue('old_password'),
            //                    'newPassword'         => $form_state->getValue('new_password'),
            //                    'repeatedNewPassword' => $form_state->getValue('repeated_new_password'),
            //                ]
            //            );
            //
            //            if ($response->hasError()) {
            //
            //                drupal_set_message($this->t(
            //                    'Wrong previous password',
            //                    LanguageUtility::getCurrentLangCode()), 'error');
            //
            //            } else {
            //
            //                $this->user->changePassword($form_state);
            //                $this->api->send(RequestType::LOGOUT, [], false);
            //
            //                drupal_set_message($this->t(
            //                    'Data has been saved',
            //                    LanguageUtility::getCurrentLangCode())
            //                );
            //            }

            $this->user->changePassword($form_state);

            drupal_set_message($this->t(
                'Data has been saved',
                LanguageUtility::getCurrentLangCode())
            );

        } catch (\Exception $e) {
            ModuleUtility::exceptionSupport($e);
        }

    }

}
