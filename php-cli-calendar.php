<?php

require_once("calendar.php");

$year = isset($argv[1]) ? $argv[1] : date("Y");
$calendar = new Calendar($year);

echo $calendar->render();
