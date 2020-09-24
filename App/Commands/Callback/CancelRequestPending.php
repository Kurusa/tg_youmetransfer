<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Models\User;
use App\Services\RequestStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class CancelRequestPending
 * @package App\Commands\Callback
 */
class CancelRequestPending extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        TelegramKeyboard::addButton('удалить запись', ['a' => 'del_msg']);
        $request_data = Request::find($this->parser::getByKey('id'));
        $this->tg->sendMessage($this->parser::getByKey('id'));
        $text = 'Заказчик N' . $request_data->creator_record_id . ' отменил ваш запрос';
        $creator_user = User::where('id', $request_data->requested_user_id)->first();
        $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
        $google = new GoogleClient();
        $google->updateSheetTravelStatus($request_data->spreadsheet_id, 'заявка отклонена');

        $request_data->status = RequestStatusService::CANCELED;
        $request_data->save();

        $this->tg->deleteMessage($this->parser::getMsgId());
    }

}