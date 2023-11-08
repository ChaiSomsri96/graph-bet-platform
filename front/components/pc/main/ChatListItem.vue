<template>
  <div class="flex-row-hl-vba h-full chat-item" :class="classObj">
    <div class="fc-grayb3 fs11">{{ timestamp | parseTime('{h}:{i}') }}</div>
    <div class="p-l-5 fw500 flex-row-hl-vb fs13">
      <span class="fc-light-brown-yellow m-r-5">{{ name }}</span>
      <div class="fc-white">{{ chatData }}</div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'ChatListItem',
  props: {
    timestamp: {
      type: Number,
      default: 0
    },
    name: {
      type: String,
      default: 'colly2'
    },
    chatData: {
      type: String,
      default: 'I am busy.'
    }
  },
  computed: {
    classObj: {
      get() {
        return {
          'main-gray-background': true
        }
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';
.main-gray-background {
  background-color: $gray1a;
}
.chat-item {
  padding-left: 10px;
  padding-right: 5px;
  min-height: 24px;
}
</style>
