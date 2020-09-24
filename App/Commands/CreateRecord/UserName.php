<?php

namespace App\Commands\CreateRecord;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class UserName
 * @package App\Commands\CreateRecord
 */
class UserName extends BaseCommand {

    /**
     * ask user name
     * @param bool $par
     */
    function processCommand($par = false)
    {
        if ($this->user->status == UserStatusService::USER_NAME) {
            Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->update([
                'user_name' => trim($this->parser::getMessage())
            ]);
            $this->triggerCommand(Contact::class);
        } else {
            $this->user->status = UserStatusService::USER_NAME;
            $this->user->save();

            $this->tg->sendMessageWithKeyboard($this->text['user_name'], [
                [$this->text['back']],
                [$this->text['cancel']]
            ]);
        }
    }

}