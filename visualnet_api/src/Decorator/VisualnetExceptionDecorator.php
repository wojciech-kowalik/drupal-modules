<?php

namespace Drupal\visualnet_api\Decorator;

use Drupal\visualnet_api\Exception\UnauthorizedHttpException;
use Drupal\visualnet_utility\Utility\LanguageUtility;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VisualnetExceptionDecorator
 *
 * @package Drupal\visualnet_api\Decorator
 * @access public
 * @copyright visualnet.pl
 */
class VisualnetExceptionDecorator
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * Error constants
     */
    const ERROR_IS_ACTIVE          = 'app.bilety2.register.isactive';
    const ERROR_CUSTOMER_NOT_FOUND = 'bilety2.error.customernotfound';
    const ERROR_CUSTOMER_FOUND     = 'app.bilety2.account.exists';
    const ERROR_VALIDATION         = 'app.error.validation';
    const ERROR_FORM_VALIDATION    = 'Form validation error.';
    const ERROR_ACTIVATED          = 'app.account.activate.error';
    const ERROR_UNAUTHORIZED       = 'app.error.authentication';

    /**
     * VisualnetExceptionDecorator constructor.
     *
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
        $this->throwByCode();
    }

    /**
     * @return string
     */
    public function getError()
    {
        if ($this->exception->hasResponse()) {
            return (string) $this->translateExceptionMessages($this->exception);
        } else {
            return $this->exception->getMessage();
        }

    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $exception
     *
     * @return \Drupal\Core\StringTranslation\TranslatableMarkup
     */
    private function translateExceptionMessages(RequestException $exception)
    {
        $translated      = (string) $exception->getResponse()->getBody();
        $body            = json_decode($translated);
        $currentLangCode = LanguageUtility::getCurrentLangCode();

        $message = (isset($body->message)) ? $body->message : '';

        switch ($message) {

            case self::ERROR_IS_ACTIVE:{
                    $translated = t('User is already registered', $currentLangCode);
                }break;

            case self::ERROR_CUSTOMER_NOT_FOUND:{
                    $translated = t('Customer doesn\'t exists', $currentLangCode);
                }break;

            case self::ERROR_CUSTOMER_FOUND:{
                    $translated = t('Customer exists', $currentLangCode);
                }break;

            case self::ERROR_VALIDATION:{
                    $translated = t('Customer with this email not exists', $currentLangCode);
                }break;

            case self::ERROR_FORM_VALIDATION:{
                    $translated = t('Wrong registration data', $currentLangCode);
                }break;

            case self::ERROR_ACTIVATED:{
                    $translated = t('There is an error with user activating', $currentLangCode);
                }break;

            case self::ERROR_UNAUTHORIZED:{
                    $translated = t('Wrong login data', $currentLangCode);
                }break;

            default:break;
        }

        return $translated;

    }

    /**
     * @throws \Drupal\visualnet_api\Exception\UnauthorizedHttpException
     */
    public function throwByCode()
    {
        switch ($this->exception->getCode()) {

            case Response::HTTP_NOT_FOUND:
                throw new \Exception('No data available');
                break;

            default:break;

        }

    }

}
