<?php

namespace Drupal\popup_zip\EventSubscriber;

use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\drupal_telegram_sdk\Event\WebhookBeforeProcessing;
use Drupal\popup_zip\StartCommand;
use Drupal\popup_zip\RegisterCommand;
use Drupal\popup_zip\UnregisterCommand;

/**
 * popup_zip event subscriber.
 */
class PopupZipSubscriber implements EventSubscriberInterface {

    /**
     * The messenger.
     *
     * @var \Drupal\Core\Messenger\MessengerInterface
     */
    protected $messenger;

    /**
     * Constructs event subscriber.
     *
     * @param \Drupal\Core\Messenger\MessengerInterface $messenger
     *   The messenger.
     */
    public function __construct(MessengerInterface $messenger) {
        $this->messenger = $messenger;
    }

    /**
     * Kernel request event handler.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *   Response event. 
     */
    public function onKernelRequest(GetResponseEvent $event) {
        //Enable custom telegram commands
        try {
            $botName = \Drupal::config('popup_zip.settings')->get('telegram_bot_name');
            if ($botName) {
                $telegram_bot = \Drupal::entityTypeManager()->getStorage('telegram_bot')->load($botName);
                $telegramServ = \Drupal::service('drupal_telegram_sdk.bot_api');
                if(method_exists($telegramServ, 'getTelegram')) {
                    $telegram = $telegramServ->getTelegram($telegram_bot);
                    $telegram->addCommand(StartCommand::class);
                    $telegram->addCommand(RegisterCommand::class);
                    $telegram->addCommand(UnregisterCommand::class);
                    $telegram->commandsHandler(true);
                }
            } else {
                \Drupal::logger('popup_zip')->error('Telegram bot init error: bot not loaded');
            }
        } catch (Exception $exc) {
            \Drupal::logger('popup_zip')->error('Telegram bot init error: ' . $exc->getMessage());
        }
    }

    /**
     * Kernel response event handler.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     *   Response event.
     */
    public function onKernelResponse(FilterResponseEvent $event) {
        $this->messenger->addStatus(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
//      KernelEvents::RESPONSE => ['onKernelResponse'],
                //    WebhookBeforeProcessing::
        ];
    }

}
