<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;

class DelMsg extends BaseCommand {

    function processCommand($par = false)
    {
        $this->tg->deleteMessage($this->parser::getMsgId());
    }
}