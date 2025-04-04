// utils/dateUtils.ts
export function parseBackendDate(dateString: string): Date {
    if (!dateString) return new Date();
    const [datePart, timePart] = dateString.split(' ');
    const [day, month, year] = datePart.split('/').map(Number);
    const [hours, minutes, seconds] = timePart ? timePart.split(':').map(Number) : [0, 0, 0];
    return new Date(year, month - 1, day, hours, minutes, seconds);
}
export function backendDateToHtmlDate(dateString: string): string {
    if (!dateString) return '';
    const date = parseBackendDate(dateString);
    return formatDateForInput(date);
}
export function formatDateForInput(date: Date): string {
    const year = date.getFullYear();
    // Mes y día con padding de ceros
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
export function formatForBackend(date: Date): string {
    const pad = (num: number) => num.toString().padStart(2, '0');
    return (
        `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ` +
        `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
    );
}
