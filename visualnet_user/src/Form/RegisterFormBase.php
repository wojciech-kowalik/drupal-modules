<?php

namespace Drupal\visualnet_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\visualnet_api\Service\VisualnetApiService;
use Drupal\visualnet_user\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PassRegisterForm
 *
 * @package Drupal\visualnet_user\Form
 */
abstract class RegisterFormBase extends FormBase
{
    /**
     * @var Drupal\visualnet_user\Service\UserService
     */
    protected $userService;

    /**
     * @var \Drupal\visualnet_api\Service\VisualnetApiService
     */
    protected $apiService;

    /**
     * RegisterFormBase constructor.
     *
     * @param \Drupal\visualnet_user\Service\UserService        $userService
     * @param \Drupal\visualnet_api\Service\VisualnetApiService $apiService
     */
    public function __construct(
        UserService $userService,
        VisualnetApiService $apiService
    ) {

        $this->userService = $userService;
        $this->apiService  = $apiService;
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
        return 'register_form_base';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $valid = \Drupal::service('email.validator')
            ->isValid($form_state->getValue('email'));

        if (!$valid) {
            $form_state->setErrorByName('email', $this->t('Wrong email'));
        }
    }
}
