<?php

namespace Drupal\popup_zip;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Drupal\popup_zip\Entity\TgChat;

class RegisterCommand extends Command {

    /**
     * @var string Command Name
     */
    protected $name = "register";

    /**
     * @var string Command Description
     */
    protected $description = "Register with password: /register PASSWORD_THERE";

    /**
     * @inheritdoc
     */
    public function handle() {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $text = $this->getUpdate()->getMessage()->text;
        $pass = $this->getPass($text);
        if ($this->validatePass($pass)) {
            $this->replyWithMessage(['text' => 'Password accepted. Chat registered']);
            $chatId = intval($this->getUpdate()->getChat()->id);
            if (!$this->isChatExist($chatId)) {
                $this->connectChat($chatId);
            }else
            {
                $this->replyWithMessage(['text' => 'Chat already connected']);
            }
        } else {
            $this->replyWithMessage(['text' => 'Invalid password']);
        }
    }

    protected function getPass(string $text) {
        $arr = explode(' ', $text);
        if (count($arr) == 2 && $arr[1] != '') {
            return $arr[1];
        }
        return false;
    }

    protected function validatePass(string $password) {
        $allowedPassword = \Drupal::config('popup_zip.settings')->get('telegram_pass');
        if ($allowedPassword == $password) {
            return true;
        }
        return false;
    }

    public static function isChatExist(int $chatId) {
        $query = \Drupal::entityQuery('tg_chat')
                ->condition('chat_id', $chatId);
        $count = $query->count()->execute();

        if ($count != 0) {
            return true;
        }
        return false;
    }

    protected function connectChat(int $chatId) {
        $data = [
            'chat_id' => $chatId
        ];
        $chat = \Drupal::entityTypeManager()
                ->getStorage('tg_chat')
                ->create($data);
        $chat->save();
    }

}
