function getNames(arr) {
    return arr.map(user => user.name)
}

const users = [
    { id: 1, name: "Alice" },
    { id: 2, name: "Bob" },
    { id: 3, name: "Charlie" }
];
console.log(getNames(users));