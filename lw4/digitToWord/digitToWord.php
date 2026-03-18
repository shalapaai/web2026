<?php

function DigitToWord() {
    $input = $_POST["digit"];
    switch ($input) {
        case '0':
            $value = 'Ноль';
            break;
        case '1':
            $value = 'Один';
            break;
        case '2':
            $value = 'Два';
            break;
        case '3':
            $value = 'Три';
            break;
        case '4':
            $value = 'Четыре';
            break;
        case '5':
            $value = 'Пять';
            break;
        case '6':
            $value = 'Шесть';
            break;
        case '7':
            $value = 'Семь';
            break;
        case '8':
            $value = 'Восемь';
            break;
        case '9':
            $value = 'Девять';
            break;
        default:
            $value = 'Невалидные данные';
            break;
    }
    echo $value . '</br>';
    return;
}

DigitToWord()

?>