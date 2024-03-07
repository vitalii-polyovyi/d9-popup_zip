<?php

namespace Drupal\popup_zip;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\popup_zip\StartCommand;
// @note: You only need Reference, if you want to change service arguments.
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PopupZipServiceProvider.
 *
 * @package Drupal\popup_zip
 */
class PopupZipServiceProvider extends ServiceProviderBase {

    /**
     * {@inheritdoc}
     */
    public function alter(ContainerBuilder $container) {
        // Overrides language_manager class to test domain language negotiation.
        // Adds entity_type.manager service as an additional argument.
        // Note: it's safest to use hasDefinition() first, because getDefinition() will 
        // throw an exception if the given service doesn't exist.
        if ($container->hasDefinition('drupal_telegram_sdk.bot_api') && $container->getDefinition('drupal_telegram_sdk.bot_api')) {

        }
    }

}
