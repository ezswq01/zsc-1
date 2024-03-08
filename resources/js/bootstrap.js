import Echo from 'laravel-echo'
import socketio from 'socket.io-client'

let url = window.location.protocol + '//' + window.location.hostname + ':6001'

window.Echo = new Echo({
    client: socketio,
    broadcaster: 'socket.io',
    host: url
});

console.log('Echo is listening...')
