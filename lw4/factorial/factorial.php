<?php

const DIGIT_LENGHT = 10;
const DIGITS  = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

function factorialCount() {

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

    function factorial(int $number): int {
        if ($number === 0) return 1;
        $number *= factorial($number - 1);
        return $number;
    }

    $input = $_POST['number'];
    if (!isValidCheck($input)) {
        echo 'Некорректный ввод, ожидалось число' . '</br>';
        return;
    }
    $number = (int)$input;
    if ($number < 0) {
        echo 'Нельзя получить факториал от отрицательного числа' . '</br>';
        return;
    }
    $factorial = factorial($number);
    echo $factorial . '</br>';
    return;
}

factorialCount();

?>