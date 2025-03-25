<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

if(!isset($_GET['start']) || !isset($_GET['end'])) {
    jsonErrorDie("No hay variables de entrada requeridas para fecha");
}

$dateStart = new DateTime($_GET["start"]);
$dateEnd = new DateTime($_GET["end"]);

$events = [
    [
        "title"=> "A backgroundColor #faa",
        "start"=> "2025-03-01",
        "backgroundColor" => "#faa"
    ],
    [
        "title"=> "Long Event",
        "start"=> "2025-03-07",
        "end"=> "2025-03-10"
    ],
    [
        "groupId"=> "999",
        "title"=> "Repeating Event",
        "start"=> "2025-03-09T16:00:00+00:00"
    ],
    [
        "groupId"=> "999",
        "title"=> "Repeating Event",
        "start"=> "2025-03-16T16:00:00+00:00"
    ],
    [
        "title"=> "Conference",
        "start"=> "2025-03-24",
        "end"=> "2025-03-26"
    ],
    [
        "title"=> "Meeting",
        "start"=> "2025-03-25T10:30:00+00:00",
        "end"=> "2025-03-25T12:30:00+00:00"
    ],
    [
        "title"=> "Lunch",
        "start"=> "2025-03-25T12:00:00+00:00"
    ],
    [
        "title"=> "Birthday Party",
        "start"=> "2025-03-26T07:00:00+00:00"
    ],
    [
        "url"=> "http:\/\/google.com\/",
        "title"=> "Click for Google",
        "start"=> "2025-03-28"
    ],
    [
        "title"=> "Meeting",
        "start"=> "2025-03-25T14:30:00+00:00"
    ],
    [
        "title"=> "Happy Hour",
        "start"=> "2025-03-25T17:30:00+00:00"
    ],
    [
        "title"=> "Dinner",
        "start"=> "2025-03-25T20:00:00+00:00"
    ]
];

jsonFullCalendarResponse($events);