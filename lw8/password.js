function createPassword(len) {
    if (len < 4) {
        console.log('Password lenght must be at least 4');
        return;
    }
    const charSets = [
        'abcdefghijklmnopqrstuvwxyz',
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '0123456789',
        '!@#$%^&*()_"№;:'
    ];

    let password = charSets.map(charSet => charSet[Math.floor(Math.random() * charSet.length)]);

    const allChars = Object.values(charSets).join('');
    while (password.length < len) {
        password.push(allChars[Math.floor(Math.random() * allChars.length)]);
    }

    return password.join('')
}

console.log(createPassword(6));
console.log(createPassword(16));