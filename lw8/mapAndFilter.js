function mapAndFilterCheck(arr) {
    return arr.map(el => el * 3).filter(el => el < 10);
}

const numbers = [2, 5, 8, 10, 3];
console.log(mapAndFilterCheck(numbers));
