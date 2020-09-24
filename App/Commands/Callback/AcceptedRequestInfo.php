<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Travel;
use App\Models\User;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class AcceptedRequestInfo
 * @package App\Commands\Callback
 */
class AcceptedRequestInfo extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $travel = Travel::where('first_record_id', $this->parser::getByKey('u_id'))->orWhere('second_record_id', $this->parser::getByKey('id'))->first();
        $user = User::find($this->parser::getByKey('r_id'));

        $text = 'Заказчик N' . $this->parser::getByKey('id') . ' принял ваш запрос' . "\n";
        $text .= 'Создана поездка N' . $travel->id . ' (попутчики N' . $this->parser::getByKey('id') . ', N' . $this->parser::getByKey('u_id') . ')' . "\n";
        $text .= '<a href="tg://user?id=' . $user['chat_id'] . '">нажмите тут, чтобы открыть прямой чат с пользователем</a>';

        //TelegramKeyboard::addButton('отменить попутку', ['a' => 'cancel_rider', 'id' => $travel->id]);
        $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get());
    }

}