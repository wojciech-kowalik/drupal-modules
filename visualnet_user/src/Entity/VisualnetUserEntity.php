<?php

namespace Drupal\visualnet_user\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\visualnet_user\VisualnetUserInterface;

/**
 * Defines the visualnet user entity class.
 *
 * @ContentEntityType(
 *   id = "visualnet_user",
 *   label = @Translation("Visualnet User"),
 *   admin_permission = "administer visualnet_users",
 *   base_table = "visualnet_users",
 *   entity_keys = {
 *     "id" = "id",
 *     "system_user_id" = "system_user_id"
 *   }
 * )
 */
class VisualnetUserEntity extends ContentEntityBase implements VisualnetUserInterface
{
    /**
     * @param $code
     *
     * @return $this
     */
    public function setBarcodePass($code)
    {
        $this->get('barcode_pass')->value = $code;
        return $this;
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->get('password')->value = $password;
        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setSystemUserId($id)
    {
        $this->get('system_user_id')->value = $id;
        return $this;
    }

    /**
     * @param $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->get('user_id')->entity = $user;
        return $this;
    }

    /**
     * @param $login
     *
     * @return $this
     */
    public function setLogin($login)
    {
        $this->get('login')->value = $login;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of user'))
            ->setReadOnly(true)
            ->setSetting('unsigned', true);

        $fields['system_user_id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('System id'))
            ->setDescription(t('Visualnet user system id'))
            ->setReadOnly(true);

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('CMS user id'))
            ->setDescription(t('The CMS user id'))
            ->setSettings(['target_type' => 'user', 'default_value' => 0]);

        $fields['barcode_pass'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Barcode pass'))
            ->setDescription(t('Visualnet user\'s pass barcode'))
            ->setRequired(false);

        $fields['login'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Login'))
            ->setDescription(t('Visualnet user\'s login'))
            ->setRequired(true);

        $fields['password'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Password'))
            ->setDescription(t('Hashed Visualnet user\'s password'))
            ->setRequired(true);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        return $fields;
    }

}
