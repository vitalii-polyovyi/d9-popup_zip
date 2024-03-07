<?php

namespace Drupal\popup_zip\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\popup_zip\TgChatInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the tg_chat entity class.
 *
 * @ContentEntityType(
 *   id = "tg_chat",
 *   label = @Translation("tg_chat"),
 *   label_collection = @Translation("tg_chats"),
 *   label_singular = @Translation("tg_chat"),
 *   label_plural = @Translation("tg_chats"),
 *   label_count = @PluralTranslation(
 *     singular = "@count tg_chats",
 *     plural = "@count tg_chats",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\popup_zip\TgChatListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\popup_zip\TgChatAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\popup_zip\Form\TgChatForm",
 *       "edit" = "Drupal\popup_zip\Form\TgChatForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "tg_chat",
 *   admin_permission = "administer tg chat",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *     "chat_id" = "chat_id"
 *   },
 *   links = {
 *     "collection" = "/admin/content/tg-chat",
 *     "add-form" = "/tg-chat/add",
 *     "canonical" = "/tg-chat/{tg_chat}",
 *     "edit-form" = "/tg-chat/{tg_chat}/edit",
 *     "delete-form" = "/tg-chat/{tg_chat}/delete",
 *   },
 *   field_ui_base_route = "entity.tg_chat.settings",
 * )
 */
class TgChat extends ContentEntityBase implements TgChatInterface {

    use EntityChangedTrait;
    use EntityOwnerTrait;

    /**
     * {@inheritdoc}
     */
    public function preSave(EntityStorageInterface $storage) {
        parent::preSave($storage);
        if (!$this->getOwnerId()) {
            // If no owner has been set explicitly, make the anonymous user the owner.
            $this->setOwnerId(0);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['label'] = BaseFieldDefinition::create('string')
                ->setLabel(t('Label'))
                ->setRequired(TRUE)
                ->setSetting('max_length', 255)
                ->setDisplayOptions('form', [
                    'type' => 'string_textfield',
                    'weight' => -5,
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('view', [
                    'label' => 'hidden',
                    'type' => 'string',
                    'weight' => -5,
                ])
                ->setDisplayConfigurable('view', TRUE);

        $fields['status'] = BaseFieldDefinition::create('boolean')
                ->setLabel(t('Status'))
                ->setDefaultValue(TRUE)
                ->setSetting('on_label', 'Enabled')
                ->setDisplayOptions('form', [
                    'type' => 'boolean_checkbox',
                    'settings' => [
                        'display_label' => FALSE,
                    ],
                    'weight' => 0,
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('view', [
                    'type' => 'boolean',
                    'label' => 'above',
                    'weight' => 0,
                    'settings' => [
                        'format' => 'enabled-disabled',
                    ],
                ])
                ->setDisplayConfigurable('view', TRUE);

        $fields['description'] = BaseFieldDefinition::create('text_long')
                ->setLabel(t('Description'))
                ->setDisplayOptions('form', [
                    'type' => 'text_textarea',
                    'weight' => 10,
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('view', [
                    'type' => 'text_default',
                    'label' => 'above',
                    'weight' => 10,
                ])
                ->setDisplayConfigurable('view', TRUE);

        $fields['uid'] = BaseFieldDefinition::create('entity_reference')
                ->setLabel(t('Author'))
                ->setSetting('target_type', 'user')
                ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
                ->setDisplayOptions('form', [
                    'type' => 'entity_reference_autocomplete',
                    'settings' => [
                        'match_operator' => 'CONTAINS',
                        'size' => 60,
                        'placeholder' => '',
                    ],
                    'weight' => 15,
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('view', [
                    'label' => 'above',
                    'type' => 'author',
                    'weight' => 15,
                ])
                ->setDisplayConfigurable('view', TRUE);

        $fields['created'] = BaseFieldDefinition::create('created')
                ->setLabel(t('Authored on'))
                ->setDescription(t('The time that the tg_chat was created.'))
                ->setDisplayOptions('view', [
                    'label' => 'above',
                    'type' => 'timestamp',
                    'weight' => 20,
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('form', [
                    'type' => 'datetime_timestamp',
                    'weight' => 20,
                ])
                ->setDisplayConfigurable('view', TRUE);

        $fields['changed'] = BaseFieldDefinition::create('changed')
                ->setLabel(t('Changed'))
                ->setDescription(t('The time that the tg_chat was last edited.'));

        $fields['chat_id'] = BaseFieldDefinition::create('string')
                ->setLabel(t('Chat ID'))
                ->setRequired(TRUE)
                ->setSetting('max_length', 255)
                ->setDisplayOptions('form', [
                    'type' => 'string_textfield',
                ])
                ->setDisplayConfigurable('form', TRUE)
                ->setDisplayOptions('view', [
                    'label' => 'hidden',
                    'type' => 'string',
                ])
                ->setDisplayConfigurable('view', TRUE);        
        return $fields;
    }

}
