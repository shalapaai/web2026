<?php

const DIGITS  = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
const STACK_MAX = 100;

function polishCount() {

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

    function arrayRemoveElement(array $array, int $indexToRemove): array {
        $newArray = [];
        $newIndex = 0;
        $currentIndex = 0;
        foreach ($array as $value) {
            if ($currentIndex !== $indexToRemove) {
                $newArray[$newIndex] = $value;
                $newIndex++;
            }
            $currentIndex++;
        }
        return $newArray;
    }

    $input = $_POST['equation'] . ' ' . PHP_EOL;
    // $input = '1 2 + ' . PHP_EOL;

    $elements = [];
    $str = '';
    $elementsCounter = 0;
    for ($i = 0; $input[$i] !== PHP_EOL; $i++) {
        if ($input[$i] === ' ' && $str !== '')  {
            $elements[] = $str;
            $str = '';
            $elementsCounter++;
        } else {
            $str .= $input[$i];
        }
        if ($elementsCounter === STACK_MAX) {
            echo 'Должно быть не более 100 элементов';
            return;
        }
    }

    $stack = [];
    $stackLenght = 0;
    foreach ($elements as $element) {
        if (isValidIntCheck($element)) {
            $stack[] = (int)$element;
            $stackLenght++;
        } else {
            $stackLenght--;
            $firstNumber = $stack[$stackLenght];
            if (!$firstNumber) {
                echo 'Неверный ввод (число 1)' . PHP_EOL;
                return;
            }
            $stack = arrayRemoveElement($stack, $stackLenght);

            $stackLenght--;
            $secondNumber = $stack[$stackLenght];
            if (!$secondNumber) {
                echo 'Неверный ввод (число 2)' . PHP_EOL;
                return;
            }
            $stack = arrayRemoveElement($stack, $stackLenght);

            switch ($element) {
                case '+':
                    $stack[] = $firstNumber + $secondNumber;
                    $stackLenght++;
                    break;
                case '-':
                    $stack[] = $secondNumber - $firstNumber;
                    $stackLenght++;
                    break;
                case '*':
                    $stack[] = $firstNumber * $secondNumber;
                    $stackLenght++;
                    break;
                default:
                    echo 'Неверный ввод (оператор)' . PHP_EOL;
                    return;
            }
        }
        // print_r($stack);            
    }
    echo 'Ответ: ' . $stack[0];
    return;
}

polishCount();

?>