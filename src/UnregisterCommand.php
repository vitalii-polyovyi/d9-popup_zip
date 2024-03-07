<?php

namespace Drupal\popup_zip;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class UnregisterCommand extends Command {

    /**
     * @var string Command Name
     */
    protected $name = "unregister";

    /**
     * @var string Command Description
     */
    protected $description = "Stop receiving messages from this Bot";

    /**
     * @inheritdoc
     */
    public function handle() {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $chatId = intval($this->getUpdate()->getChat()->id);
        if (RegisterCommand::isChatExist($chatId)) {
            $this->removeChat($chatId);
            $this->replyWithMessage(['text' => 'Chat disconnected']);
        } else {
            $this->replyWithMessage(['text' => 'I don\'t know this chat']);
        }
    }

    protected function removeChat(int $chatId) {
        $data = [
            'chat_id' => $chatId
        ];
        $chats = \Drupal::entityTypeManager()
                ->getStorage('tg_chat')
                ->loadByProperties($data);
        if (count($chats) > 0) {
            $chat = array_shift($chats);
            $chat->delete();
        }
    }
}
