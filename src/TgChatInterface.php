<?php

namespace Drupal\popup_zip;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a tg_chat entity type.
 */
interface TgChatInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
