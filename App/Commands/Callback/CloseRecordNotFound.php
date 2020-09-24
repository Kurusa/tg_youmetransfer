<?php

namespace App\Commands\Callback;

use App\Commands\BaseCommand;
use App\Models\Record;
use App\TgHelpers\GoogleClient;

/**
 * Class CloseRecordNotFound
 * @package App\Commands\Callback
 */
class CloseRecordNotFound extends BaseCommand {

    /**
     * delete record from google calendar
     * delete record from db
     * mark a canceled in google sheets
     * cancel all requests from and to this record
     * @param bool $par
     */
    function processCommand($par = false)
    {
        $record = Record::find($this->parser::getByKey('id'));
        $google = new GoogleClient();
        $google->cancelCalendarEvent($record->event_id);
        $google->cancelSheetRecord($record->spreadsheet_id);

        foreach ($this->user->requests as $request) {
            if ($request['creator_record_id'] == $record->id || $request['requested_record_id'] == $record->id) {
                $this->triggerCommand(CancelRider::class, $request->id);
            }
        }

        $record->delete();
    }
}