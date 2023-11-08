<template>
  <div class="info-box flex-space-between-vc fc-white">
    <span class="fs20 bold p-r-30">
      {{ storeUserName }}
    </span>
    <span class="fs16">
      {{ userInfo }}
    </span>
  </div>
</template>
<script>
import { formatTime } from '@/utils/index.js'

export default {
  name: 'UserInfoBox',
  components: {},
  props: {},
  data() {
    return {
      userName: ''
    }
  },
  computed: {
    storeUserName() {
      const name = this.$store.state.user.name
      return name !== undefined && name !== '' ? name : 'Player'
    },
    userInfo() {
      const createTime = this.$store.state.user.createTime
      let timeStr = 'JOINED: '
      timeStr = timeStr + new Date(createTime * 1000).toDateString()
      timeStr = timeStr + ' [' + formatTime(createTime) + ']'

      return timeStr
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.info-box {
  width: 100%;
  padding: 20px 15px 20px 30px;
  background: $gray35;
}

@media (max-width: 480px) {
  .info-box {
    flex-direction: column;
  }
}
</style>
