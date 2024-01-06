import Echo from 'laravel-echo'
import socketio from 'socket.io-client'

window.Echo = new Echo({
    client: socketio,
    broadcaster: 'socket.io',
    host: 'localhost:6001'
});

console.log('Echo is listening...')
