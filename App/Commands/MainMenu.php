<?php

namespace App\Commands;

use App\Models\Record;
use App\Services\RecordStatusService;
use App\Services\UserStatusService;

/**
 * Class MainMenu
 * @package App\Commands
 */
class MainMenu extends BaseCommand {

    /**
     * @param bool $par
     */
    function processCommand($par = false)
    {
        // delete possible undone trip
        $filling_record = Record::where('user_id', $this->user->id)->where('status', RecordStatusService::FILLING)->first();
        if ($filling_record) {
            $filling_record->delete();
        }

        $this->user->status = UserStatusService::DONE;
        $this->user->save();

        $buttons = [[$this->text['calendar'], $this->text['your_records']], [$this->text['create_record']]];
        // add buttons to main menu if its admin
        if ($this->user->is_admin) {
            $buttons[] = [$this->text['analytic'], $this->text['create_admin']];
        }

        $this->tg->sendMessageWithKeyboard($par ?: $this->text['main_menu'], $buttons);
    }

}