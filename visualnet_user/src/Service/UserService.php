<?php

namespace Drupal\visualnet_user\Service;

use Collections\Set;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\user\Entity\User;
use Drupal\visualnet_api\Exception\ValueNotExistsException;
use Drupal\visualnet_api\Model\Pass;
use Drupal\visualnet_api\RequestType;
use Drupal\visualnet_api\Service\VisualnetApiService;
use Drupal\visualnet_user\Entity\VisualnetUserEntity as VisualnetUser;
use Drupal\visualnet_user\Exception\CmsUserMakeException;
use Drupal\visualnet_user\Exception\VisualnetUserMakeException;
use Drupal\visualnet_user\Exception\VisualnetUserNotExistsException;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use Drupal\visualnet_utility\Utility\ModuleUtility;
use Drupal\visualnet_utility\Utility\StringUtility;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserService
 *
 * @package Drupal\visualnet_user\Service
 */
class UserService
{
    /**
     * @var Drupal\Core\Entity\EntityTypeManager
     */
    private $entityManager;

    /**
     * @var \Drupal\Core\Entity\EntityStorageInterface|object
     */
    private $storage;

    /**
     * @var \Drupal\visualnet_api\Service\VisualnetApiService
     */
    private $api;

    /**
     * @var string
     */
    private $defaultLanguage = 'pl';

    /**
     * @var \Drupal\Core\Session\AccountProxyInterface|null
     */
    private $currentCmsUser = null;

    /**
     * UserService constructor
     *
     * @param \Drupal\Core\Entity\EntityTypeManager             $entityManager
     * @param \Drupal\visualnet_api\Service\VisualnetApiService $api
     */
    public function __construct(EntityTypeManager $entityManager, VisualnetApiService $api)
    {
        $this->currentCmsUser = \Drupal::currentUser();
        $this->entityManager  = $entityManager;
        $this->api            = $api;

        $this->storage = $this->entityManager->getStorage('visualnet_user');
    }

    /**
     * @param \Drupal\Core\Form\FormStateInterface $formState
     * @param \stdClass                            $data
     */
    public function create(FormStateInterface $formState, \stdClass $data)
    {
        $user = $this->makeCmsUser($formState);
        $this->makeVisualnetUser($formState->getValue('pass'), $user, $data);
    }

    /**
     * @return \stdClass
     */
    public function get()
    {
        $user        = new \stdClass();
        $user->id    = $this->currentCmsUser->id();
        $user->email = $this->currentCmsUser->getEmail();

        return $user;
    }

    /**
     * @param array                                $parameters
     * @param \Drupal\Core\Form\FormStateInterface $formState
     *
     * @throws \Drupal\visualnet_user\Exception\VisualnetUserNotExistsException
     */
    public function activate(array $parameters, FormStateInterface $formState)
    {
        $result = $this->storage->getQuery()
            ->condition('login', $parameters['email'], 'CONTAINS')
            ->execute();

        if (!empty($result)) {
            $visualnetUser = $this->storage->load(array_keys($result)[0]);

            if (!$visualnetUser) {
                throw new VisualnetUserNotExistsException('User not exists');
            }

            $cmsUser = $visualnetUser->get('user_id')->entity;

            $visualnetUser->get('password')->value =
            StringUtility::encoder($formState->getValue('password'));

            $visualnetUser->save();

            $cmsUser->setPassword($formState->getValue('password'));
            $cmsUser->get('status')->value = 1;
            $cmsUser->save();
        }
    }

    /**
     * @param                          $pass
     * @param \Drupal\user\Entity\User $user
     * @param \stdClass                $data
     *
     * @return \Drupal\Core\Entity\EntityInterface|static
     * @throws \Drupal\visualnet_user\Exception\VisualnetUserMakeException
     */
    private function makeVisualnetUser( /* string */$pass, User $user, \stdClass $data)
    {
        $visualnetUser = VisualnetUser::create();

        try {
            $visualnetUser->setSystemUserId($data->id);
            $visualnetUser->setUser($user);
            $visualnetUser->setLogin($data->email);

            if (isset($pass)) {
                $visualnetUser->setBarcodePass($pass);
            }

            $visualnetUser->save();
        } catch (\Exception $e) {
            ModuleUtility::exceptionSupport($e);

            throw new VisualnetUserMakeException(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $visualnetUser;
    }

    /**
     * @param \Drupal\Core\Form\FormStateInterface $formState
     *
     * @return \Drupal\Core\Entity\EntityInterface|static
     * @throws \Drupal\visualnet_user\Exception\CmsUserMakeException
     */
    private function makeCmsUser(FormStateInterface $formState)
    {
        mb_internal_encoding('UTF-8');

        $user = User::create();

        $name         = $formState->getValue('name');
        $lastName     = $formState->getValue('lastname');
        $email        = $formState->getValue('email');
        $randomNumber = mt_rand(10, 1000);

        try {
            $user->setPassword(base64_encode($randomNumber));
            $user->enforceIsNew();
            $user->setEmail($email);

            $cmsUserName = StringUtility::removeAccents(
                mb_strtolower(mb_substr($name, 0, 1)) . mb_strtolower($lastName)
            );

            $user->setUsername($cmsUserName . $randomNumber);

            $user->set("init", $email);
            $user->set("langcode", $this->defaultLanguage);
            $user->set("preferred_langcode", $this->defaultLanguage);
            $user->set("preferred_admin_langcode", $this->defaultLanguage);

            $user->save();
        } catch (\Exception $e) {
            ModuleUtility::exceptionSupport($e);

            throw new CmsUserMakeException(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $user;
    }

    /**
     * @return \Drupal\Core\Entity\EntityInterface|null
     * @throws \Drupal\visualnet_user\Exception\VisualnetUserNotExistsException
     */
    public function getLoggedAuthenticationData()
    {
        $result = $this->storage->getQuery()
            ->condition('user_id', $this->currentCmsUser->id())
            ->execute();

        if (!isset(array_keys($result)[0])) {
            throw new VisualnetUserNotExistsException('Can\'t find user data');
        }

        $visualnetUser = $this->storage->load(array_keys($result)[0]);

        if (!$visualnetUser) {
            throw new VisualnetUserNotExistsException('User not exists');
        }

        return $visualnetUser;
    }

    /**
     * @return array
     */
    public function getPasses()
    {
        $collection = new Set();

        if (\Drupal::currentUser()->isAnonymous()) {
            return $collection;
        }

        try {
            $accountResponse = $this->api->send(RequestType::ACCOUNT, []);
            $passResponse    = $this->api->send(RequestType::PASS, []);

            if (isset($accountResponse->getLoggedData()->passes)) {
                $passes = $accountResponse->getLoggedData()->passes;

                foreach ($passes as $pass) {
                    $collection->add(new Pass($pass, $passResponse->getContentObject()));
                }
            }
        } catch (ValueNotExistsException $e) {
            drupal_set_message($e->getMessage(), 'error');
        } catch (\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
        }

        return $collection;
    }

    /**
     * @param $id
     */
    public function returnTicket($id)
    {
        try {
            $this->api->send(RequestType::TICKET_RETURN, ['id' => $id]);
            drupal_set_message(t('Ticket has been returned', LanguageUtility::getCurrentLangCode()));
        } catch (ValueNotExistsException $e) {
            drupal_set_message($e->getMessage(), 'error');
        } catch (\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
        }
    }

    /**
     * @param \Drupal\Core\Form\FormStateInterface $formState
     *
     * @throws \Drupal\visualnet_user\Exception\VisualnetUserNotExistsException
     */
    public function changePassword(FormStateInterface $formState)
    {
        $result = $this->storage->getQuery()
            ->condition('user_id', $this->currentCmsUser->id(), '=')
            ->execute();

        if (!empty($result)) {
            $visualnetUser = $this->storage->load(array_keys($result)[0]);

            if (!$visualnetUser) {
                throw new VisualnetUserNotExistsException('User not exists');
            }

            $cmsUser = $visualnetUser->get('user_id')->entity;

            //$visualnetUser->get('password')->value =
            //StringUtility::encoder($formState->getValue('new_password'));
            //$visualnetUser->save();

            $cmsUser->setPassword($formState->getValue('new_password'));
            $cmsUser->save();
        }

        \Drupal::cache('render')->deleteAll();
    }

    /**
     * @return void
     */
    public function logout()
    {
        $this->api->send(RequestType::LOGOUT, [], false);

        drupal_set_message(t('User has been logged out', LanguageUtility::getCurrentLangCode()));

        $user = \Drupal::currentUser();
        \Drupal::logger('user')->notice('Session closed for %name.', ['%name' => $user->getAccountName()]);
        \Drupal::moduleHandler()->invokeAll('user_logout', [$user]);
        \Drupal::service('session_manager')->destroy();
        $user->setAccount(new AnonymousUserSession());
    }
}
