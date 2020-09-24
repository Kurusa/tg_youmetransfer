<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Services\RequestStatusService;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class CloseRecordFound
 * @package App\Commands\Callback
 */
class CloseRecordFound extends BaseCommand {

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
        if ($user_requests) {
            $one_row = [];
            $buttons = [];
            foreach ($user_requests as $request) {
                $callback_data = [
                    'a' => 'select_rider_info',
                    'id' => $user_record_id,
                    'r_id' => $request['id']
                ];

                $one_row[] = [
                    'text' => $request['creator_record_id'] == $user_record_id ? $request['requested_title'] : $request['creator_title'],
                    'callback_data' => json_encode($callback_data),
                ];

                if (count($one_row) == 1) {
                    $buttons[] = $one_row;
                    $one_row = [];
                }
            }
            if (count($one_row) > 0) {
                $buttons[] = $one_row;
            }

            $this->tg->sendMessageWithInlineKeyboard($this->text['select_rider'], $buttons);
        } else {
            $this->tg->sendMessage($this->text['no_requests']);
        }
    }

}