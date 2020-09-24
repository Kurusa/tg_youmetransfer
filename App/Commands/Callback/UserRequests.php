<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Services\RequestStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class UserRequests
 * @package App\Commands\Callback
 */
class UserRequests extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $user_requests = Request::where('creator_user_id', $this->user->id)->get();
        if ($user_requests) {
            foreach ($user_requests as $request) {
                switch ($request->status) {
                    case RequestStatusService::DONE:
                        TelegramKeyboard::addButton('Попутчик N' . $request->requested_record_id . ' принял ваш запрос. ', [
                            'a' => 'accepted_request_info',
                            'id' => $request->requested_record_id,
                            'u_id' => $request->creator_record_id,
                            'r_id' => $request->requested_user_id,
                        ]);
                        break;
                    case RequestStatusService::CANCELED:
                        TelegramKeyboard::addButton('Попутчик N' . $request->requested_record_id . ' отменил ваш запрос.', []);
                        break;
                    case RequestStatusService::PENDING:
                        TelegramKeyboard::addButton('Попутчик N' . $request->requested_record_id . ' еще не ответил на ваш вопрос', []);
                        break;
                }
            }

            $this->tg->sendMessageWithInlineKeyboard('ваши запросы к попутчикам', TelegramKeyboard::get());
        } else {
            $this->tg->sendMessage($this->text['no_requests']);
        }
    }
}