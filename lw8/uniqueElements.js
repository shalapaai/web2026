function uniqueElements(arr) {
    const obj = {};
    arr.forEach(element => {
        !obj[element] ? obj[element] = 1 : obj[element] += 1;
    });
    return obj;
}

console.log(uniqueElements(['привет', 'hello', '1', 1]));