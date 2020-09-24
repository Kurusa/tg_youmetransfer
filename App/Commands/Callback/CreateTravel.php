<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Models\Travel;
use App\Models\User;

/**
 * Class CreateTravel
 * @package App\Commands\Callback
 */
class CreateTravel extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $request_data = Request::where('creator_record_id', $par)->where('requested_user_id', $this->user->id)->first();
        $creator_user = User::where('id', $request_data->creator_user_id)->first();
        $travel = Travel::create([
            'first_record_id' => $request_data->creator_record_id,
            'second_record_id' => $request_data->requested_record_id,
        ]);

        $text = 'Создана поездка N' . $travel->id . ' (попутчики N' . $request_data->creator_record_id . ', N' . $request_data->requested_record_id . ')';
        $href0 = '<a href="tg://user?id=' . $creator_user->chat_id . '">нажмите тут, чтобы открыть прямой чат с пользователем</a>';
        $href1 = '<a href="tg://user?id=' . $this->user->chat_id . '">нажмите тут, чтобы открыть прямой чат с пользователем</a>';

        $this->tg->sendMessage($text, $this->user->chat_id);
        $this->tg->sendMessage($text, $creator_user->chat_id);

        $this->tg->sendMessage($href0, $this->user->chat_id);
        $this->tg->sendMessage($href1, $creator_user->chat_id);
    }

}