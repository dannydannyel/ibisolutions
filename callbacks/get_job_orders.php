<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

$idEmployer = $_SESSION['id'];


if(!isset($_GET['start']) || !isset($_GET['end'])) {
    jsonErrorDie("No hay variables de entrada requeridas para fecha");
}

$dateStart = new DateTime($_GET["start"]);
$dateEnd = new DateTime($_GET["end"]);

//Take all the events data including useful join informatin for employers and names
$jobOrderData = $db->getFullJobOrder($idEmployer);

$events = [];
foreach($jobOrderData as $row) {
    $idJob = $row['idjob'];
    $villa = $row['villa'];
    $employee = $row['name'] . " " . $row['surname'];
    $idEmployee = $row['idemployee'];
    $checkIn = $row['check_in'];
    $checkOut = $row['check_out'];
    $eCheckIn = $row['check_in_employee'];
    $eCheckOut = $row['check_out_employee'];

    if(is_null($eCheckIn) && is_null($eCheckOut)) { // No started
        $backgroundColor = "#a52a2a";
    }
    elseif(!is_null($eCheckIn) && is_null($eCheckOut)) { // Is working
        $backgroundColor = "#ffe4c4";
    }
    elseif(!is_null($eCheckIn) && !is_null($eCheckOut)) { // Task finished
        $backgroundColor = "#7fffd4";
    }
    else {
        $backgroundColor = "#6495ed";
    }
    $events[] = [
        'id' => $idJob,
        'url' => genUrl('admin/job_order/show.php?idjob=' . $row['idjob']),
        'title' => "Villa: " . $villa . ", Empleado: " . $employee,
        'start'=> $row['check_in'],
        'end' => $row['check_out'],
        'backgroundColor' => $backgroundColor,
        'extendedProps' => ['idEmp' => $idEmployee]
    ];
}
/*
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
];*/

jsonFullCalendarResponse($events);