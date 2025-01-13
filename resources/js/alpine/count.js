export default function count() {
    return {
        count: 0,
        increment() {
            this.count++;
        }
    }
}