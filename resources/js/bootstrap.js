import Echo from 'laravel-echo'
import socketio from 'socket.io-client'

let url = window.location.protocol + '//' + window.location.hostname;

// env = production
if (process.env.NODE_ENV != 'production') {
    console.log('Development mode')
	 if (process.env.NODE_LES_PORT) {
		 url = url + ':' + (process.env.NODE_LES_PORT || "6010")
	 } else {
		 url = url + ':6010'
	 }
}

console.log('url: ', url);

window.Echo = new Echo({
    client: socketio,
    broadcaster: 'socket.io',
    host: url
});

console.log('Echo is listening...')
