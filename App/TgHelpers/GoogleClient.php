<?php

namespace App\TgHelpers;

use App\Models\Record;
use App\Models\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;

class GoogleClient {

    private $client;

    function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Trip list');
        $client->setScopes([Google_Service_Calendar::CALENDAR_READONLY, Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig('credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = __DIR__ . '/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        $this->client = $client;
    }

    /**
     * when one rider created travel request. default status 'waiting'
     * @param Request $data
     * @return false|string
     */
    function createSheetTravel(Request $data): string
    {
        $values = [
            $data->id, // separately cause it takes last inserted id
            $data->creator_record_id,
            $data->requested_record_id,
            $data->created_at,
            'в ожидании', // default status
        ];

        $service = new \Google_Service_Sheets($this->client);
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues(['values' => $values]);
        $result = $service->spreadsheets_values->append(
            env('TRAVEL_SHEET_ID'),
            'A1:P1',
            $valueRange,
            ['valueInputOption' => 'raw']
        );
        return substr($result['updates']['updatedRange'], -1);
    }

    /**
     * create sheet record. default status 'searching'
     * @param $id
     * @param Record $data
     * @return false|string
     */
    function createSheetRecord(Record $data): string
    {
        $user_name = $data->user_name ?: $data->chat_id;
        // use strval cause values can be null and google will throw exception
        $values = [
            $data->id, $data->dep_city, $data->dest_city,
            $user_name, date('Y-m-d H:i', $data->dep_date),
            strval($data->people_count), strval($data->hotel_title), $data->user_name,
            strval($data->flight_number), $user_name, strval($data->email),
            strval($data->phone), $data->car_order, $data->created_at, '0',
            'в поиске', '0', '0'
        ];
        // prepare data
        $service = new \Google_Service_Sheets($this->client);
        $valueRange = new Google_Service_Sheets_ValueRange();
        $valueRange->setValues(['values' => $values]);
        $result = $service->spreadsheets_values->append(
            env('RECORD_SHEET_ID'),
            'A1:P1',
            $valueRange,
            ['valueInputOption' => 'raw']
        );

        // number of new row
        return substr($result['updates']['updatedRange'], -1);
    }

    /**
     * mark (not delete) record as canceled in google sheets
     * @param $row_num
     */
    function cancelSheetRecord($row_num)
    {
        // it marks record in google sheets as canceled
        if ($row_num) {
            $service = new \Google_Service_Sheets($this->client);
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [[
                    '1',
                    'отменен', date('Y-m-d H:i', time())
                ]]
            ]);
            $service->spreadsheets_values->update(
                env('RECORD_SHEET_ID'),
                'O' . $row_num . ':Q' . $row_num,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /**
     * update travel status in google sheets
     * @param $row_num
     * @param string $status
     */
    function updateSheetTravelStatus($row_num, string $status)
    {
        if ($row_num) {
            $service = new \Google_Service_Sheets($this->client);
            $body = new Google_Service_Sheets_ValueRange([
                'values' => [[$status]]
            ]);
            $service->spreadsheets_values->update(
                env('TRAVEL_SHEET_ID'),
                'E' . $row_num . ':E' . $row_num,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /**
     * update record status in google sheets
     * @param $row_num
     * @param null $status
     * @param null $body
     */
    function updateSheetRecordStatus($row_num, $status = null, $body = null)
    {
        if ($row_num) {
            $service = new \Google_Service_Sheets($this->client);

            $values = $status ? [[$status]] : $body;
            $body = new Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
            $end_range = $status ? 'P' : 'R';
            $service->spreadsheets_values->update(
                env('RECORD_SHEET_ID'),
                'P' . $row_num . ':' . $end_range . $row_num,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /**
     * mark record as closed in google sheets
     * @param $row_num
     * @param null $status
     * @param null $body
     */
    function closeSheetRecordStatus($row_num)
    {
        if ($row_num) {
            $service = new \Google_Service_Sheets($this->client);

            $body = new Google_Service_Sheets_ValueRange([
                'values' => [['1', 'закрыли заказ']]
            ]);
            $service->spreadsheets_values->update(
                env('RECORD_SHEET_ID'),
                'R' . $row_num . ':S' . $row_num,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }

    /**
     * update google calendar event
     * @param $event_id
     * @param string $status
     */
    function updateCalendarEventStatus($event_id, string $status)
    {
        $service = new Google_Service_Calendar($this->client);
        $event = $service->events->get(env('CALENDAR_ID'), $event_id);
        $event->setSummary($event->summary . ' || заказ ' . $status);
        $service->events->update(env('CALENDAR_ID'), $event->getId(), $event);
    }

    /**
     * delete google calendar event by id
     * @param $event_id
     */
    function cancelCalendarEvent($event_id)
    {
        $service = new Google_Service_Calendar($this->client);
        $event = $service->events->get('primary', $event_id);
        // first check if event exists
        if ($event) {
            $service->events->delete(env('CALENDAR_ID'), $event_id);
        }
    }

    /**
     * create google calendar event
     * @param Record $data
     * @return mixed
     */
    function createCalendarEvent(Record $data): string
    {
        $service = new Google_Service_Calendar($this->client);
        $event = new Google_Service_Calendar_Event([
            'summary' => $data->info['title'],
            'description' => $data->info['description'],
            'start' => [
                'dateTime' => $data->formated_dep_date,
            ],
            'end' => [
                'dateTime' => $data->formated_dep_date
            ],
            'colorId' => '3'
        ]);
        $result = $service->events->insert(env('CALENDAR_ID'), $event);
        return $result->id;
    }

    /**
     * @param Record $data
     * @return array|bool
     */
    function searchForCoriders(Record $data, int $user_id)
    {
        $start_search = date('c', $data->dep_date - (60 * 60 * 4));
        $end_search = date('c', $data->dep_date + (60 * 60 * 4));
        $service = new Google_Service_Calendar($this->client);
        $results = $service->events->listEvents(env('CALENDAR_ID'), [
            'maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $start_search,
            'timeMax' => $end_search,
        ]);

        $events = $results->getItems();
        if ($events) {
            $result = [];
            foreach ($events as $event) {
                $title_exploded = explode('||', $event->summary);
                $city_data = explode(' ', $title_exploded[1]);

                $description_exploded = explode(' ', $event->description);
                if ($city_data[2] == $data->dep_city && $city_data[4] == $data->dest_city
                    && $description_exploded[7] != $user_id) {

                    $id = trim($title_exploded[0]);
                    $date = $description_exploded[2] . ' ' . substr($description_exploded[3], 0, -8);
                    $result[] = [
                        'user_id' => $description_exploded[7],
                        'dep' => $city_data[2],
                        'dest' => $city_data[4],
                        // found record id
                        'requested_id' => $data->id,
                        // record id for which the search is being performed
                        'creator_id' => substr($id, 1),
                    ];
                }

            }
            return $result;
        }

        return false;
    }

    /**
     * search for available records in google calendar
     * @param Record $data
     * @param int $chat_id
     * @return array|bool
     */
    function searchForCalendarEvent(Record $data, int $user_id)
    {
        $start_search = date('c', $data->dep_date - (60 * 60 * 4));
        $end_search = date('c', $data->dep_date + (60 * 60 * 4));
        $service = new Google_Service_Calendar($this->client);
        $results = $service->events->listEvents(env('CALENDAR_ID'), [
            'maxResults' => 100,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $start_search,
            'timeMax' => $end_search,
        ]);

        $events = $results->getItems();
        if ($events) {
            $result = [];
            foreach ($events as $event) {
                $title_exploded = explode('||', $event->summary);
                $city_data = explode(' ', $title_exploded[1]);

                $description_exploded = explode(' ', $event->description);
                if ($city_data[2] == $data->dep_city && $city_data[4] == $data->dest_city
                    && $description_exploded[7] != $user_id) {

                    $id = trim($title_exploded[0]);
                    $date = $description_exploded[2] . ' ' . substr($description_exploded[3], 0, -8);
                    $summary = $id . ', ' . $date;
                    $result[] = [
                        'start' => $event->start->dateTime,
                        'end' => $event->end->dateTime,
                        'summary' => $summary,
                        // found record id
                        'id' => substr($id, 1),
                        // record id for which the search is being performed
                        'creator_r_id' => $data->id,
                    ];
                }

            }
            return $result;
        }

        return false;
    }

}
