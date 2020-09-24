<?php

namespace App\Commands\Keyboard;

use App\Commands\BaseCommand;
use App\Commands\CreateRecord\Contact;
use App\Commands\CreateRecord\DepartureCity;
use App\Commands\CreateRecord\DepartureDate;
use App\Commands\CreateRecord\DestinationCity;
use App\Commands\CreateRecord\FlightNumber;
use App\Commands\CreateRecord\HotelTitle;
use App\Commands\CreateRecord\PeopleCount;
use App\Commands\CreateRecord\UserName;
use App\Commands\MainMenu;
use App\Services\UserStatusService;

class Back extends BaseCommand {

    function processCommand($par = false)
    {
        switch ($this->user->status) {
            case UserStatusService::DESTINATION:
                $this->triggerCommand(DepartureCity::class);
                break;
            case UserStatusService::DEPARTURE:
            case UserStatusService::DEPARTURE_DATE:
                $this->triggerCommand(DestinationCity::class);
                break;
            case UserStatusService::PEOPLE_COUNT:
                $this->triggerCommand(DepartureDate::class);
                break;
            case UserStatusService::HOTEL_TITLE:
                $this->triggerCommand(FlightNumber::class);
                break;
            case UserStatusService::USER_NAME:
                $this->triggerCommand(PeopleCount::class);
                break;
            case UserStatusService::FLIGHT_NUMBER:
                $this->triggerCommand(CarOrder::class);
                break;
            case UserStatusService::CONTACT:
                $this->triggerCommand(UserName::class);
                break;
            case 'phone':
            case 'email':
            case UserStatusService::CAR_ORDER:
                $this->triggerCommand(Contact::class);
                break;
            case UserStatusService::TRIP_TYPE:
                $this->triggerCommand(HotelTitle::class);
                break;
            default:
                $this->triggerCommand(MainMenu::class);
                break;
        }
    }

}