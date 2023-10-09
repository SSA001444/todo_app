import "./bootstrap";
import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo.channel(`todos`)
    .listen("TodoUpdated", (e) => {
        console.log("TodoUpdated event received:", e.todo);
        // Обновите ваш интерфейс для отображения обновленной задачи в реальном времени
    })
    .listen("TodoDeleted", (e) => {
        console.log("TodoDeleted event received:", e.todo);
        // Обновите ваш интерфейс для удаления задачи в реальном времени
    });
