<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Models\Travel;
use App\Models\User;
use App\Services\RequestStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class UserCoriders
 * @package App\Commands\Callback
 */
class UserCoriders extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $user_record_id = $this->parser::getByKey('id');
        $user_requests = Request::where('creator_record_id', $user_record_id)
            ->orWhere('requested_record_id', $user_record_id)
            ->where('status', '=', RequestStatusService::DONE)
            ->get();
        if (count($user_requests)) {
            foreach ($user_requests as $request) {
                switch ($request['status']) {
                    case RequestStatusService::DONE:
                        $record_id = $request['requested_record_id'] == $user_record_id ? $request['creator_record_id'] : $request['requested_record_id'];
                        $travel = Travel::where('first_record_id', $record_id)
                            ->orWhere('first_record_id', $user_record_id)
                            ->orWhere('second_record_id', $user_record_id)
                            ->orWhere('second_record_id', $record_id)
                            ->first();
                        $user_id = $request['creator_user_id'] == $this->user->id ? $request['requested_user_id'] : $request['creator_user_id'];
                        $user = User::where('id', $user_id)->first();
                        $text = 'Заказчик N' . $record_id . ' принял ваш запрос' . "\n";
                        $text .= 'Создана поездка N' . $travel->id . ' (попутчики N' . $travel->first_record_id . ', N' . $travel->second_record_id . ')' . "\n";
                        $text .= '<a href="tg://user?id=' . $user->chat_id . '">нажмите тут, чтобы открыть прямой чат с пользователем</a>' . "\n";
                        $this->tg->sendMessage($text);
                        break;
                    case RequestStatusService::CANCELED:
                        TelegramKeyboard::$buttons = [];
                        TelegramKeyboard::addButton('удалить запись', ['a' => 'del_msg']);
                        $request_data = $request_data = Request::find($request['id']);
                        $text = 'Заказчик N' . $request_data->requested_record_id . ' отменил ваш запрос';
                        $creator_user = User::where('id', $request_data->creator_user_id)->first();
                        $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
                        break;
                    case RequestStatusService::PENDING:
                        TelegramKeyboard::$buttons = [];
                        TelegramKeyboard::addButton('отменить запрос', ['a' => 'cancel_request_pending', 'id' => $request['id']]);
                        $request_data = $request_data = Request::find($request['id']);
                        $text = 'Заказчик N' . $request_data->requested_record_id . ' еще не ответил на ваш запрос';
                        $creator_user = User::where('id', $request_data->creator_user_id)->first();
                        $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
                        break;
                }
            }

        } else {
            $this->triggerCommand(SearchForCorider::class, $user_record_id);
        }
    }

}