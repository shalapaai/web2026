function mapObject(obj, callback) {
    result = {};
    for (key in obj) {
        result[key] = callback(obj[key]);
    }
    return result;
}

console.log(mapObject({ a: 1, b: 2, c: 3 }, x => x * 2));
console.log(mapObject({ a: 1, b: 2, c: 3 }, x => x + 1));
console.log(mapObject({ a: 1, b: 2, c: 3 }, x => x / 2));
