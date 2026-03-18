<?php

const SEPARATORS = ['.', ',', '/', '|', ' ', '-', '_', '~'];
const DIGITS  = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];


function whichZodiac() {

    function isAmericanDateType(string $AmericanDateTypeAnswer): bool {
        if ($AmericanDateTypeAnswer === 'Y' || $AmericanDateTypeAnswer === 'y') {
            return true;
        }
        return false;
    }

    function getSeparator(string $date): string {
        $separator = '';
        for ($i = 0; $date[$i] !== PHP_EOL; $i++) {
            foreach(SEPARATORS as $value) {
                if ($date[$i] === $value) {
                    $separator = $value;
                    break;
                }
            }
            if ($separator !== '') break;
        }
        return $separator;
    }

    function isValidIntCheck(string $str): bool {
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

    function makeArrayDates(string $date, string $separator, bool $isAmericanDateType): array {

        function convertMonth(string $str): string {
            switch ($str) {
                case 'Января': case 'января': case 'Январь': case 'январь': case 'Янв': case 'янв': 
                case 'January': case 'january': case 'Jan': case 'jan':
                    return '01';
                
                case 'Февраля': case 'февраля': case 'Февраль': case 'февраль': case 'Фев':  case 'фев': 
                case 'February': case 'february': case 'Feb': case 'feb':
                    return '02';
                
                case 'Марта': case 'марта': case 'Март': case 'март': case 'Мар': case 'мар': 
                case 'March': case 'march': case 'Mar': case 'mar':
                    return '03';
                
                case 'Апреля': case 'апреля': case 'Апрель': case 'апрель': case 'Апр': case 'апр': 
                case 'April': case 'april': case 'Apr': case 'apr':
                    return '04';
                
                case 'Мая': case 'мая': case 'Май': case 'май': 
                case 'May': case 'may':
                    return '05';
                
                case 'Июня': case 'июня': case 'Июнь': case 'июнь': case 'Июн': case 'июн': 
                case 'June': case 'june': case 'Jun': case 'jun':
                    return '06';
                
                case 'Июля': case 'июля': case 'Июль': case 'июль': case 'Июл': case 'июл': 
                case 'July': case 'july': case 'Jul': case 'jul':
                    return '07';
                
                case 'Августа': case 'августа': case 'Август': case 'август': case 'Авг': case 'авг':
                case 'August': case 'august': case 'Aug': case 'aug':
                    return '08';
                
                case 'Сентября': case 'сентября': case 'Сентябрь': case 'сентябрь': case 'Сен': case 'сен':
                case 'September': case 'september': case 'Sep': case 'sep':
                    return '09';
                
                case 'Октября': case 'октября': case 'Октябрь': case 'октябрь': case 'Окт': case 'окт':
                case 'October': case 'october': case 'Oct': case 'oct':
                    return '10';
                
                case 'Ноября': case 'ноября': case 'Ноябрь': case 'ноябрь': case 'Ноя': case 'ноя':
                case 'November': case 'november': case 'Nov': case 'nov':
                    return '11';
                
                case 'Декабря': case 'декабря': case 'Декабрь': case 'декабрь': case 'Дек': case 'дек':
                case 'December': case 'december': case 'Dec': case 'dec':
                    return '12';
                
                default:
                    return $str;
            }
        }

        function pushDate(array &$datesArray, int $i, string $date, int $numLenght, int &$found, bool $isAmericanDateType) {
            $str = '';
            for ($j = 0; $j < $numLenght; $j++) {
                $str .= $date[$i - $numLenght + $j];
            }
            if (!isValidIntCheck($str)) {
                if (($isAmericanDateType && $found === 0) || (!$isAmericanDateType && $found > 0)) {
                    $convertedStr = convertMonth($str);
                    if ($str === $convertedStr) {
                        $datesArray = [];
                        return;
                    }
                    $str = $convertedStr;
                    $numLenght = 2;
                } else {
                    $datesArray = [];
                    return;
                }
            }
            if ($numLenght <= 2 && $numLenght > 0 && $found < 2) {
                if ($isAmericanDateType) {
                    if ($found === 0) $datesArray['month'] = (int)$str;
                    else $datesArray['day'] = (int)$str;
                } else {
                    if ($found === 0) $datesArray['day'] = (int)$str;
                    else $datesArray['month'] = (int)$str;
                }
                $found++;
            } elseif ($numLenght <= 4 && $numLenght > 0) {
                if (!isValidIntCheck($str)) {
                    $datesArray = [];
                    return;
                }
                $datesArray['year'] = (int)$str;
            } else {
                $datesArray = [];
            } 
        }

        $numLenght = 0;
        $found = 0;
        $foundAll = 0;
        $datesArray = [];
        for ($i = 0; $date[$i] !== PHP_EOL; $i++) {
            if ($date[$i] === $separator) {
                pushDate($datesArray, $i, $date, $numLenght, $found, $isAmericanDateType);
                $numLenght = 0;
                $foundAll++;
            } else {
                $numLenght++;
                if ($date[$i + 1] === PHP_EOL) {
                    pushDate($datesArray, $i + 1, $date, $numLenght, $found, $isAmericanDateType);
                    $foundAll++;
                }
            } 
        }
        if ($found !== 2) return [];
        return $datesArray;
    }

    function convertDateToZodiac(array $datesArray): string {
        $day = $datesArray['day'];
        $month = $datesArray['month'];

        return match(true) {
            ($month == 1 && $day >= 20) || ($month == 2 && $day <= 18) => 'Водолей',
            ($month == 2 && $day >= 19) || ($month == 3 && $day <= 20) => 'Рыбы',
            ($month == 3 && $day >= 21) || ($month == 4 && $day <= 19) => 'Овен',
            ($month == 4 && $day >= 20) || ($month == 5 && $day <= 20) => 'Телец',
            ($month == 5 && $day >= 21) || ($month == 6 && $day <= 20) => 'Близнецы',
            ($month == 6 && $day >= 21) || ($month == 7 && $day <= 22) => 'Рак',
            ($month == 7 && $day >= 23) || ($month == 8 && $day <= 22) => 'Лев',
            ($month == 8 && $day >= 23) || ($month == 9 && $day <= 22) => 'Дева',
            ($month == 9 && $day >= 23) || ($month == 10 && $day <= 22) => 'Весы',
            ($month == 10 && $day >= 23) || ($month == 11 && $day <= 21) => 'Скорпион',
            ($month == 11 && $day >= 22) || ($month == 12 && $day <= 21) => 'Стрелец',
            ($month == 12 && $day >= 22) || ($month == 1 && $day <= 19) => 'Козерог',
            default => 'Неизвестно',
        };
    }

    $americanDateTypeAnswer = $_POST["is-american"];
    // $americanDateTypeAnswer = 'n';
    $isAmericanDateType = isAmericanDateType($americanDateTypeAnswer);

    $date = $_POST["date"] . PHP_EOL;
    // $date = '09 11 2002' . PHP_EOL;

    $separator = getSeparator($date);
    if (!$separator) {
        echo 'Неправильный ввод (разделитель)' . '</br>';
        return;
    }
    $datesArray = makeArrayDates($date, $separator, $isAmericanDateType);
    if (!$datesArray) {
        echo 'Неправильный ввод (дата)' . '</br>';
        return;
    }
    // print_r($datesArray);
    $zodiac = convertDateToZodiac($datesArray);

    if ($zodiac === 'Неизвестно') {
        echo 'Неправильный ввод (значение даты)' . '</br>';
    } else {
        echo 'Ваш знак зодиака: ' . $zodiac . '</br>';
    }
    return;
}

whichZodiac();

?>