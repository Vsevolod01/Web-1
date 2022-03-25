<?php
date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL);
ini_set('display_errors', 'Off');

$startTime = microtime(true);

function greater_or_eq($a, $b)
{
    $b *= 10;
    if (strripos($a, ".") !== FALSE) {
        $a = rtrim($a, "0");
        $a = rtrim($a, ".");
        $dot_pos = strripos($a, ".");
        $tmp = $a[$dot_pos];
        $a[$dot_pos] = $a[$dot_pos + 1];
        $a[$dot_pos + 1] = $tmp;
        $a = rtrim($a, ".");
        $dot_pos++;
        if (substr($a, 0, max(0, $dot_pos)) == $b) {
            if (substr($a, max(0, $dot_pos)) == ""){
                return ($a[0] == "-");
            } else {
                return !($a[0] == "-");
            }
        }
    }
    return $a >= $b;
}

function less_or_eq($a, $b)
{
    $b *= 10;
    if (strripos($a, ".") !== FALSE) {
        $a = rtrim($a, "0"); 
        $a = rtrim($a, ".");
        $dot_pos = strripos($a, ".");
        $tmp = $a[$dot_pos];
        $a[$dot_pos] = $a[$dot_pos + 1];
        $a[$dot_pos + 1] = $tmp;
        $a = rtrim($a, ".");
        $dot_pos++;
        if (substr($a, 0, max(0, $dot_pos)) == $b) {
            if (substr($a, max(0, $dot_pos)) == ""){
                return !($a[0] == "-");
            } else {
                return ($a[0] == "-");
            }
        }
    }
    return $a <= $b;
}

function lite_json_print(array $result)
{
    $answer = "{";
    foreach ($result as $key => $item) {
        $answer .= "\"";
        $answer .= $key;
        $answer .= "\":\"";
        $answer .= $item;
        $answer .= "\",";
    }
    $answer = substr($answer, 0, strlen($answer) - 1);
    $answer .= "}";
    echo $answer;
}

function checkArgs($x, $y, $r)
{
    if (strripos($x, ".") !== FALSE) return FALSE;
    if (strripos($y, ".") !== FALSE) {
        $y = rtrim($y, "0");
        $y = rtrim($y, ".");
        $dot_pos = strripos($y, ".");
        if (substr($y, 0, max(0, $dot_pos)) == -4 ||
            substr($y, 0, max(0, $dot_pos)) == 4) {
            return true;
        }
    }
    return
        in_array($x, ["-3", "-2", "-1", "0", "1", "2", "3", "4", "5"], true) &&
        $y > -5 && $y < 5 &&
        in_array($r, ["1", "1.5", "2", "2.5", "3"], true) &&
        is_numeric($x) && is_numeric($y) && is_numeric($r);
}

function circle($x, $y, $r) {
    if ($x == 0 && $y == $r) {
        return less_or_eq($y, $r);
    }
    return TRUE;
}

function atArea($x, $y, $r)
{
    return
        (circle($x, $y, $r) && (0 <= $x) && ($y >= 0) && ($y ** 2 <= $r ** 2 - $x ** 2)) || // Четверть круга
        ((0 <= $x) && ($y <= 0) && greater_or_eq($y, $x - $r)) || // Треугольник
        (($x <= 0) && ($x >= -$r) && ($y <= 0) && greater_or_eq($y, -$r)); // Прямоугольник
}

$x = isset($_POST["x"]) ? $_POST["x"] : null;
$y = isset($_POST["y"]) ? str_replace(",", ".", $_POST["y"]) : null;
$r = isset($_POST["r"]) ? $_POST["r"] : null;

if (!checkArgs($x, $y, $r)) {
    http_response_code(400);
    return;
}
$coordinatesAtArea = atArea($x, $y, $r);

$currentTime = date("H:i:s");
$time = number_format(microtime(true) - $startTime, 10, ".", "") * 1000000;
$result = [
    'x' => $x,
    'y' => $y,
    'r' => $r,
    'currentTime' => $currentTime,
    'time' => (int)$time,
    'atArea' => $coordinatesAtArea ? "Попадание" : "Промах",
];

lite_json_print($result);