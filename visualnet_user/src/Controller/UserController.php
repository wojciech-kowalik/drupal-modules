<?php

namespace Drupal\visualnet_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\visualnet_api\Comparer\UnixDateComparer;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_user\Service\UserService;
use Drupal\visualnet_utility\Service\UtilityService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserController extends ControllerBase
{
    /**
     * @var \Drupal\Core\Form\FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var Drupal\visualnet_user\Service\UserService
     */
    protected $userService;

    /**
     * @var \Drupal\visualnet_utility\Service\UtilityService
     */
    protected $utilityService;

    /**
     * UserController constructor.
     *
     * @param \Drupal\visualnet_user\Service\UserService        $userService
     * @param \Drupal\Core\Form\FormBuilderInterface            $formBuilder
     */
    public function __construct(
        UserService $userService,
        UtilityService $utilityService,
        FormBuilderInterface $formBuilder
    ) {

        $this->userService    = $userService;
        $this->utilityService = $utilityService;
        $this->formBuilder    = $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('visualnet.user_service'),
            $container->get('visualnet.utility_service'),
            $container->get('form_builder')
        );
    }

    /**
     * @return array
     */
    public function login()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('\Drupal\user\Form\UserLoginForm'),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout()
    {
        $this->userService->logout();
        return $this->redirect('<front>');
    }

    /**
     * @return array
     */
    public function register()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('Drupal\visualnet_user\Form\PassRegisterForm'),
        ];
    }

    /**
     * @return array
     */
    public function activate()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('Drupal\visualnet_user\Form\ActivateForm'),
        ];
    }

    /**
     * @return array
     */
    public function me()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('Drupal\visualnet_user\Form\ProfileForm'),
        ];
    }

    /**
     * @return array
     */
    public function pass()
    {
        return [
            '#theme' => __FUNCTION__,
            '#data'  => $this->userService
                ->getPasses(),
        ];
    }

    /**
     * @return array
     */
    public function ticket()
    {
        $data = [];

        $tickets = $this->utilityService
            ->getCollectionByType(RequestType::TICKET, [], true);

        $tickets->sort(new UnixDateComparer(UnixDateComparer::DIRECTION_DESC));

        $data['current'] = $tickets->filter(function ($ticket) {
            return $ticket->hasCurrentYear();
        });

        $data['previous'] = $tickets->filter(function ($ticket) {
            return $ticket->isPreviousDate();
        });

        return [
            '#theme'          => 'ticket_base',
            '#data'           => $data,
            '#previous_count' => $data['previous']->count(),
        ];
    }

    /**
     * @return array
     */
    public function order()
    {
        $orders = $this->utilityService
            ->getCollectionByType(RequestType::ORDER, [], true);

        $orders->sort(new UnixDateComparer(UnixDateComparer::DIRECTION_DESC));

        return [
            '#theme' => __FUNCTION__,
            '#data'  => $orders,
        ];
    }

    /**
     * @return array
     */
    public function password()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('Drupal\visualnet_user\Form\ChangePasswordForm'),
        ];
    }

    /**
     * @return array
     */
    public function remind()
    {
        return [
            '#theme' => __FUNCTION__,
            '#form'  => $this->formBuilder
                ->getForm('\Drupal\user\Form\UserPasswordForm'),
        ];
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function returnTicket($id)
    {
        $this->userService->returnTicket($id);
        return $this->redirect('visualnet_user.ticket');
    }

}
