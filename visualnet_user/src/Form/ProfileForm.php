<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_api\Service\VisualnetApiService;
use Drupal\visualnet_user\Service\UserService;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Drupal\visualnet_utility\Utility\ModuleUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProfileForm
 *
 * @package Drupal\visualnet_user\Form
 */
class ProfileForm extends FormBase
{
    /**
     * ProfileForm constructor.
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
        return 'profile_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $accountResponse = $this->api->send(RequestType::ACCOUNT, []);
        $logged          = $accountResponse->getLoggedData();

        $form['name'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('User firstname', LanguageUtility::getCurrentLangCode()),
            '#value'    => $logged->firstName,
            '#required' => true,
        );

        $form['lastname'] = array(
            '#type'     => 'textfield',
            '#title'    => $this->t('User lastname', LanguageUtility::getCurrentLangCode()),
            '#value'    => $logged->lastName,
            '#required' => true,
        );

        $form['email'] = array(
            '#type'       => 'email',
            '#title'      => $this->t('Email'),
            '#value'      => $logged->email,
            '#attributes' => [
                'readonly' => [true],
            ],
            '#required'   => true,
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
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        try {

            $response = $this->api->send(
                RequestType::UPDATE_ACCOUNT,
                [
                    'firstName' => $form_state->getValue('name'),
                    'lastName'  => $form_state->getValue('lastname'),
                ]
            );

            if (!$response->hasError()) {

                drupal_set_message(
                    $this->t(
                        'Data has been saved',
                        LanguageUtility::getCurrentLangCode())
                );

            }

        } catch (\Exception $e) {

            ModuleUtility::exceptionSupport($e);
        }

    }

}
