<?php

namespace App;

use App\Commands\MainMenu;
use App\Models\User;
use App\Services\Language\ChangeLanguageService;
use App\TgHelpers\TelegramApi;

/**
 * Class WebhookController
 * @package App
 */
class WebhookController {

    /**
     * handle telegram update
     */
    public function handle()
    {
        $update = \json_decode(file_get_contents('php://input'), TRUE);
        $is_callback = !array_key_exists('message', $update);
        $response = $is_callback ? $update['callback_query'] : $update;
        $chat_id = $response['message']['chat']['id'];

        $unknown_command = true;
        if ($is_callback) {
            $config = include('config/callback_commands.php');
            $action = \json_decode($response['data'], true)['a'];

            if (isset($config[$action])) {
                (new $config[$action]($response))->handle($response);
            }

            $tg = new TelegramApi();
            $tg->answerCallbackQuery($response['id']);
        } else {

            // checking commands -> keyboard commands -> mode -> exit
            if ($update['message']['text']) {
                $text = $update['message']['text'];

                if (strpos($text, '/') === 0) {
                    $handlers = include('config/slash_commands.php');
                    $key = $text;
                } else {
                    $key = $this->processKeyboardCommand($text);

                    if ($key) {
                        $handlers = include('config/keyboard_сommands.php');
                    } else {
                        $handlers = include('config/mode_сommands.php');
                        $user = User::where('chat_id', $chat_id)->first();

                        if ($user && $handlers[$user->status]) {
                            $key = $user->status;
                        }

                    }

                }

                if (isset($handlers[$key])) {
                    (new $handlers[$key]($update))->handle($update);
                    exit;
                }

            }
        }

        if ($unknown_command) {
            (new MainMenu())->handle($update);
        }
    }

    /**
     * returns text key from language file
     * @param string $text
     * @return string|null
     */
    protected function processKeyboardCommand(string $text): ?string
    {
        $locales = ChangeLanguageService::$locales;

        foreach ($locales as $locale) {
            $config = include('config/lang/' . $locale . '/keyboard.php');
            $translations = \array_flip($config);
            if (isset($translations[$text])) {
                return $translations[$text];
            }
        }

        return null;
    }

}

