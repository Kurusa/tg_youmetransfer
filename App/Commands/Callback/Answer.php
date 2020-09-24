<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Travel;

class Answer extends BaseCommand {

    function processCommand($par = false)
    {
        if ($this->parser::getByKey('ans') == 'replied') {
            $this->tg->sendMessage('Ок, спасибо, что сообщили. Мы закроем ваш заказ, чтобы вам не приходили лишние запросы. В календаре он также отмечен, как закрытый.
            Вы можете отменить вашу поездку в любое время и заказ снова станет актуальным.');
        }
        
        $travel = Travel::find($this->parser::getByKey('id'));
        $travel->notified = 1;
        $travel->save();
            
        $this->tg->deleteMessage($this->parser::getMsgId());
        \App\Models\Answer::create([
            'user_id' => $this->user->id,
            'travel_id' => $this->parser::getByKey('id'),
            'answer' => $this->parser::getByKey('ans')
        ]);

    }
}