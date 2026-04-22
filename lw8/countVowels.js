function countVowels(str) {
    const vowels = [
        'а', 'е', 'ё', 'и', 'о', 'у', 'ы', 'э', 'ю', 'я',
        'А', 'Е', 'Ё', 'И', 'О', 'У', 'Ы', 'Э', 'Ю', 'Я'
    ]; 
    let counter = 0;
    if (typeof str === 'string') {
        for (i = 0; i < str.length; i++) {
            if (vowels.includes(str[i])) counter++;
        }
    } else {
        console.log('Неверный формат')
    }
    return counter;
}

console.log(countVowels('Привет, мир'));
console.log(countVowels('123'));
console.log(countVowels(123));
console.log(countVowels('ввв'));
console.log(countVowels('ААА'));