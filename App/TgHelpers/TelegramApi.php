<?php

namespace App\TgHelpers;

use Dotenv\Dotenv;

/**
 * Class TelegramApi responsible for api calls to telegram
 * @package App\TgHelpers
 */
class TelegramApi {

    public $result;
    public $chat_id;
    public $curl;

    public $API = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->curl = curl_init();
    }

    public function api($method, $params)
    {
        $url = $this->API . env('TELEGRAM_BOT_TOKEN') . '/' . $method;

        return $this->do($url, $params);
    }

    private function do(string $url, array $params = []): ?array
    {
        $params = json_encode($params);

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', 'Content-Length: ' . strlen($params),
        ]);

        $this->result = json_decode(curl_exec($this->curl), TRUE);
        return $this->result;
    }

    public function isTyping()
    {
        $this->api('sendChatAction', [
            'chat_id' => $this->chat_id,
            'action' => 'typing'
        ]);
    }

    public function sendMessage(string $text, $chat_id = null, $markdown = false)
    {
        $this->isTyping();
        $this->api('sendMessage', [
            'chat_id' => $chat_id ?: $this->chat_id,
            'text' => $text,
            'parse_mode' => $markdown ? 'markdown' : 'HTML',
        ]);
    }

    public function sendMessageWithKeyboard(string $text, array $encoded_markup, $chat_id = null)
    {
        $this->isTyping();
        $this->api('sendMessage', [
            'chat_id' => $chat_id ?: $this->chat_id,
            'text' => $text,
            'reply_markup' => [
                'keyboard' => $encoded_markup,
                'one_time_keyboard' => false,
                'resize_keyboard' => true,
            ], 'parse_mode' => 'HTML',
        ]);
    }

    public function sendMessageWithInlineKeyboard(string $text, $buttons, $chat_id = null)
    {
        $this->isTyping();
        $this->api('sendMessage', [
            'chat_id' => $chat_id ?: $this->chat_id,
            'reply_markup' => [
                'inline_keyboard' => $buttons,
            ],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    public function answerCallbackQuery($callback_query_id)
    {
        $this->api('answerCallbackQuery', [
            'callback_query_id' => $callback_query_id,
        ]);
    }

    public function deleteMessage(int $message_id)
    {
        $this->api('deleteMessage', [
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
        ]);
    }

    public function updateMessageKeyboard(int $message_id, string $new_text, array $new_button)
    {
        $this->api('editMessageText', [
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'text' => $new_text,
            'reply_markup' => [
                'inline_keyboard' => $new_button,
            ],
            'parse_mode' => 'HTML',
        ]);
    }

    public function __destruct()
    {
        $this->curl = curl_close($this->curl);
    }

}