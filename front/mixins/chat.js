export default {
  name: 'Chat',
  data() {
    return {
      chatMsgs: []
    }
  },
  beforeDestroy() {
    this.$chatSocketConn.$off()
  },
  mounted() {
    const self = this

    this.$chatSocketConn.$on('chat_message', function(data) {
      const obj = JSON.parse(data)
      const username = self.$store.getters['user/getName']
      if (username !== obj.username) {
        self.chatMsgs.push({
          CREATE_TIME: obj.curTime,
          id: 1,
          user_id: 1,
          username: obj.username,
          message: obj.msg
        })
      }
    })

    this.$chatSocketConn.$on('disconnect', function() {})
    this.reload()
  },
  methods: {
    sendMsg(data) {
      const username = this.$store.getters['user/getName']
      const curTime = Math.floor(Date.now() / 1000)
      this.chatMsgs.push({
        CREATE_TIME: curTime,
        id: 1,
        user_id: 1,
        username,
        message: data.msg
      })
      const emitData = {
        msg: data.msg,
        curTime
      }
      this.$axios.post('/Chat/post_msg', emitData).then((response) => {
        if (response.code !== 20000) {
          // Error
        } else {
          // this.reload()
        }
      })
    },
    reload() {
      const self = this
      this.overlayVisible = true
      this.$axios
        .post('/Chat/list', null, { withoutToken: true })
        .then((response) => {
          self.chatMsgs = []
          for (let i = 0; i < response.data.length; i++) {
            self.chatMsgs.push(response.data[i])
          }
          self.overlayVisible = false
        })
    }
  }
}
