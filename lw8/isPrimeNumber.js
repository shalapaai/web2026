function primeCheck(value) {
    if (value > 0) {
        if (value === 1 || value === 2) return true;
        for (i = 2; i < value; i++) {
            if (value % i === 0) return false;
        }
        return true;
    } else {
        return false;
    }
}

function printIsPrime(value) {
    primeCheck(value) ? console.log(value, ' - простое число') : console.log(value, ' - не простое число');
}

function isPrimeNumber(value) {
    if (typeof value === 'number') {
        printIsPrime(value);
    } else if (typeof value === 'object') {
        value.forEach(element => {
            if (typeof element === 'number') {
                printIsPrime(element);
            } else {
                console.log(element, ' - неверный ввод');
            }
        });
    } else {
        console.log(value, ' - неверный ввод');
    }
}

isPrimeNumber(17);
isPrimeNumber(1);
isPrimeNumber([2, 4, 16, 15, true]);
isPrimeNumber(0);
isPrimeNumber('привет');