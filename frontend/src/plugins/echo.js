import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios'

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: false,
    enabledTransports: ['ws'],



    // url utiliséé pour auth les canaux privés
//     authEndpoint: 'http://localhost:8000/broadcasting/auth',
// auth: {
//   headers: {
//     'X-Requested-With': 'XMLHttpRequest', 
//   },
//   withCredentials: true,
// },


authEndpoint: 'http://localhost:8000/broadcasting/auth',
auth: {
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json' // Force Laravel à répondre en JSON au lieu de rediriger
    }
},
// C'est ICI qu'il faut configurer Axios ou Pusher pour envoyer les cookies de session
authorizer: (channel, options) => {
    return {
        authorize: (socketId, callback) => {
            axios.post('http://localhost:8000/broadcasting/auth', {
                socket_id: socketId,
                channel_name: channel.name
            }, {
                withCredentials: true // Permet d'envoyer le cookie laravel_session et XSRF-TOKEN
            })
            .then(response => {
                callback(false, response.data);
            })
            .catch(error => {
                callback(true, error);
            });
        }
    };
},

});

window.Echo.connector.pusher.connection.bind('connected' , ()=>{
  console.log('connection');
  
})


window.Echo.connector.pusher.connection.bind('error' , (err)=>{
  console.log('error' , err);
  
})






//*****************************************************************//
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';

// window.Pusher = Pusher;

// const echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     forceTLS: false,
//     cluster: 'mt1',
//     enabledTransports: ['ws', 'wss'],


//     authEndpoint: 'http://localhost:8000/broadcasting/auth',
// auth: {
//   headers: {
//     'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')
//   },
//   withCredentials: true,
// },

// });

// function getCookie (name) {
//   const value = `; ${document.cookie}`
//   const parts = value.split(`; ${name}=`)
//   if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift())
// }



// export default echo;