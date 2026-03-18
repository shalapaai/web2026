<?php

const DIGITS  = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

function findAllLuckyTickets() {

    function getStrLenght(string $str): int {
        $lenght = 0;
        $str .= PHP_EOL;
        for ($i = 0; $str[$i] !== PHP_EOL; $i++) {
            $lenght++;
        }
        return $lenght;
    }

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
        if (getStrLenght($str) !== 6) $isValid = false;
        return $isValid;
    }

    $firstTicket = $_POST['first-ticket'];
    $secondTicket = $_POST['second-ticket'];
    // $firstTicket = '000000';
    // $secondTicket = '999999';

    if (!isValidCheck($firstTicket)) {
        echo 'Неправильный ввод (первый билет)' . '</br>';
        return;
    }
    if (!isValidCheck($secondTicket)) {
        echo 'Неправильный ввод (второй билет)' . '</br>';
        return;
    }
    
    $firstTicketInt = (int)$firstTicket;
    $secondTicketInt = (int)$secondTicket;

    if ($firstTicketInt > $secondTicket) {
        $a = $firstTicket;
        $firstTicketInt = $secondTicket;
        $secondTicket = $a;

        $firstTicketInt = (int)$firstTicket;
        $secondTicketInt = (int)$secondTicket;
    }

    $luckyTickets = [];
    for ($i = $firstTicketInt; $i <= $secondTicketInt; $i++) {
        $ticket = (string)$i;
        $lenght = getStrLenght($ticket);
        for ($j = $lenght; $j < 6; $j++) {
            $ticket = '0' . $ticket;
        }
        $sumFirstThree = (int)($ticket[0]) + (int)($ticket[1]) + (int)($ticket[2]);
        $sumSecondThree = (int)($ticket[3]) + (int)($ticket[4]) + (int)($ticket[5]);
        if ($sumFirstThree === $sumSecondThree) {
            $luckyTickets[] = $ticket;
            echo $ticket . '</br>';
        }
    }

    // print_r($luckyTickets);
    return;
}

findAllLuckyTickets();
?>