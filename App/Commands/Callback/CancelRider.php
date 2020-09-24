<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Request;
use App\Models\Travel;
use App\Models\User;
use App\Services\RequestStatusService;
use App\TgHelpers\GoogleClient;
use App\TgHelpers\TelegramKeyboard;

/**
 * Class CancelRider
 * @package App\Commands\Callback
 */
class CancelRider extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        TelegramKeyboard::addButton('удалить запись', ['a' => 'del_msg']);

        if ($par) {
            $request_data = Request::find($par);
            if ($request_data->creator_user_id == $this->user->id) {
                $text = 'Заказчик N' . $request_data->creator_record_id . ' отменил ваш запрос';
                $requested_user = User::where('id', $request_data->requested_user_id)->first();
                $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $requested_user->chat_id);
            } else {
                $text = 'Заказчик N' . $request_data->requested_record_id . ' отменил ваш запрос';
                $creator_user = User::where('id', $request_data->creator_user_id)->first();
                $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
            }
            Travel::where('first_record_id', $request_data->requested_record_id)->orWhere('second_record_id', $request_data->creator_record_id)->delete();
            Travel::where('first_record_id', $request_data->creator_record_id)->orWhere('second_record_id', $request_data->requested_record_id)->delete();
        } else {
            // requested user canceled
            $request_data = Request::where('creator_record_id', $this->parser::getByKey('id'))->where('requested_user_id', $this->user->id)->first();
            Travel::where('first_record_id', $request_data->requested_record_id)->orWhere('second_record_id', $request_data->creator_record_id)->delete();
            Travel::where('first_record_id', $request_data->creator_record_id)->orWhere('second_record_id', $request_data->requested_record_id)->delete();

            $text = 'Заказчик N' . $request_data->requested_record_id . ' отменил ваш запрос';
            $creator_user = User::where('id', $request_data->creator_user_id)->first();
            $this->tg->sendMessageWithInlineKeyboard($text, TelegramKeyboard::get(), $creator_user->chat_id);
        }

        $google = new GoogleClient();
        $google->updateSheetTravelStatus($request_data->spreadsheet_id, 'заявка отклонена');

        $request_data->status = RequestStatusService::CANCELED;
        $request_data->save();


        $this->tg->deleteMessage($this->parser::getMsgId());
    }

}