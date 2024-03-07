<?php

namespace Drupal\popup_zip\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the tg_chat entity edit forms.
 */
class TgChatForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New tg_chat %label has been created.', $message_arguments));
        $this->logger('popup_zip')->notice('Created new tg_chat %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The tg_chat %label has been updated.', $message_arguments));
        $this->logger('popup_zip')->notice('Updated tg_chat %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.tg_chat.canonical', ['tg_chat' => $entity->id()]);

    return $result;
  }

}
