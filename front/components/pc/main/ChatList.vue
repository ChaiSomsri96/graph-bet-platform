<template>
  <div class="v-full">
    <!-- <b-overlay
      :show="overlayVisible"
      variant="dark"
      class="flex-col-space-between-vc h-full v-full"
    > -->
    <div class="flex-col-space-between-vc h-full v-full">
      <div
        ref="chatlist"
        class="flex-col-hl-vt h-full v-full beauty-scrollbar-wrapper"
      >
        <chat-list-item
          v-for="(item, i) in chatMsgs"
          :key="i"
          :timestamp="item.CREATE_TIME"
          :name="item.username"
          :chat-data="item.message"
        />
      </div>
      <input
        ref="chatText"
        type="text"
        class="h-full input-box fc-grayb3 p-l-10"
        value=""
        placeholder="Message or  /help..."
        @change="sendChatData()"
      />
    </div>
    <!-- </b-overlay> -->
  </div>
</template>
<script>
import ChatListItem from '@/components/pc/main/ChatListItem.vue'

import ChatMixin from '~/mixins/chat'

export default {
  name: 'ChatList',
  components: {
    ChatListItem
  },
  mixins: [ChatMixin],
  props: {},
  data() {
    return {
      isLogIn: false,
      editObj: null,
      overlayVisible: false
    }
  },
  computed: {
    token() {
      return this.$store.state.user.token
    }
  },
  watch: {
    token: {
      handler(val, oldVal) {
        this.isLogIn = val !== undefined && val !== ''
        this.enableEdit(this.isLogIn)
      },
      immediate: true
    }
  },
  updated() {
    if (this.$refs.chatlist !== undefined)
      this.$refs.chatlist.scrollTop = this.$refs.chatlist.scrollHeight
  },
  mounted() {
    const self = this

    this.editObj = this.$refs.chatText
    this.enableEdit(this.isLogIn)
    this.$bus.$on('on-change-login', function(param) {
      self.enableEdit(param.isLogIn)
    })
  },
  methods: {
    enableEdit(bEnable) {
      if (this.editObj !== null) {
        if (bEnable) this.editObj.disabled = false
        else this.editObj.disabled = true
      }
    },
    sendChatData() {
      const txt = this.$refs.chatText.value
      this.$refs.chatText.value = ''
      this.sendMsg({
        msg: txt
      })
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.input-box {
  border-left: none;
  border-right: 1px solid $gray4f;
  border-top: 1px solid $gray4f;
  border-bottom: 1px solid $gray4f;
  background-color: $gray1a;
  outline: none;
  font-size: 13px;
  font-weight: 500;
  padding: 8px 0px 8px 10px;
}
</style>
