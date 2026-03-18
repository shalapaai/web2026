<?php

const DIGITS  = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

function isValidCheck(string $str): bool {
    $isValid = true;
    $str .= PHP_EOL;
    for ($i = 0; $str[$i] !== PHP_EOL; $i++) {
        $isValidNumber = false;
        foreach (DIGITS as $digit) {
            if ($str[$i] === $digit) {
                $isValidNumber = true;
                break;
            }
        }
        if (!$isValidNumber) {
            $isValid = false;
            break;
        }
    }
    return $isValid;
}

function isLeapYear() {
    $input = $_POST["leap-year"];
    if (!isValidCheck($input)) {
        echo 'Невалидные данные' . '</br>';
        return;
    }
    $value = (int)$input;

    if ($value > 0 && $value <= 30000) {
        if (($value % 4 === 0) && (($value % 100 !== 0) || ($value % 400 === 0))) {
            echo $value . ' - високосный год' . '</br>';
        } else {
            echo $value . ' - не високосный год' . '</br>';
        }
    } else {
        echo 'Невалидные данные' . '</br>';
    }
    return;
}

isLeapYear();

?>