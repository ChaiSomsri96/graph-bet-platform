import Vue from 'vue'
import io from 'socket.io-client'
// import VueSocketIOExt from 'vue-socket.io-extended';

const crashSocket = io(process.env.BACKEND_URL + ':' + process.env.CRASH_PORT)
const chatSocket = io(process.env.BACKEND_URL + ':' + process.env.CHAT_PORT)

const crashSocketBus = new Vue()
crashSocket.on('onMessage', function(data) {
  crashSocketBus.$emit('onMessage', data)
})
crashSocket.on('disconnect', function() {
  crashSocketBus.$emit('disconnect')
})

const chatSocketBus = new Vue()
chatSocket.on('chat_message', function(data) {
  chatSocketBus.$emit('chat_message', data)
})
chatSocket.on('disconnect', function() {
  chatSocketBus.$emit('disconnect')
})

// export default ({ store }) => {
//   Vue.use(VueSocketIOExt, socket, {
//     store
//   });
// }

export default (ctx, inject) => {
  inject('crashSocketConn', crashSocketBus)
  inject('chatSocketConn', chatSocketBus)
}
