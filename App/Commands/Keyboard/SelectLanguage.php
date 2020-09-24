<?php

namespace App\Commands\Keyboard;

use App\Commands\BaseCommand;
use App\Services\Language\ChangeLanguageService;

class SelectLanguage extends BaseCommand {

    protected $mode = 'lang';

	function processCommand($par = false)
    {
        switch ($this->user_data['mode']) {
            case $this->mode:
                if (ChangeLanguageService::$locales[$this->parser::getMessage()]) {
                    $this->db->table('userList')->where('chat_id', $this->chat_id)->update(['lang' => ChangeLanguageService::$locales[$this->parser::getMessage()], 'mode' => 'done']);
                    $this->triggerCommand(Settings::class);
                }
                break;
            default:
                $this->db->table('userList')->where('chat_id', $this->chat_id)->update(['mode' => $this->mode]);
                $this->tg->sendMessageWithKeyboard('
🇷🇺 Выберите язык с клавиатуры ниже
🇺🇸 Select language the from keyboard',
                    [[ChangeLanguageService::LANG_TEXT_RU, ChangeLanguageService::LANG_TEXT_EN]]);
        }
    }

}