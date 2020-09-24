<?php

namespace App\Commands\Keyboard;

use App\Commands\BaseCommand;

class Settings extends BaseCommand {

    function processCommand($par = false)
    {
        $this->tg->sendMessageWithKeyboard($this->text['settings'], [
            [$this->text['change_lang']],
            [$this->text['back']]
        ]);
    }

}