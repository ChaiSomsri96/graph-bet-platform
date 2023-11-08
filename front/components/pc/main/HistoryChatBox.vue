<template>
  <div class="flex-col-ht-vc top-line-box h-full">
    <div
      class="flex-row-hc-vc title-bar fs14 h-full pd-5"
      :style="titleBarStyleObj"
    >
      <div class="text-center fc-white">JAPANESE</div>
      <div class="flex-row-hc-vc leave-box pointer">
        <svg-icon icon-class="close_red" class="ic_size_7" />
        <div class="m-l-5">Leave</div>
      </div>
    </div>
    <div
      ref="vertical_tab_box"
      class="flex-row-hc-vt h-full value-box"
      :style="mainContentStyleObj"
    >
      <div class="flex-col-hc-vc v-full">
        <div
          class="flex-row-hc-vc tab-box v-full pointer"
          :class="curTabID === 0 ? 'selected' : ''"
          @click="btnChatClick"
        >
          <div
            class="rotate-text fs16 bold"
            :style="'width: ' + tabHeight + 'px;'"
          >
            CHAT
          </div>
        </div>
        <div
          class="flex-row-hc-vc tab-box v-full pointer"
          :class="curTabID === 1 ? 'selected' : ''"
          @click="btnHistoryClick"
        >
          <div
            class="rotate-text fs16 bold"
            :style="'width: ' + tabHeight + 'px;'"
          >
            HISTORY
          </div>
        </div>
        <div
          v-if="isUserListShow"
          class="flex-row-hc-vc tab-box v-full pointer"
          :class="curTabID === 2 ? 'selected' : ''"
          @click="btnUserListClick"
        >
          <div
            class="rotate-text fs16 bold"
            :style="'width: ' + tabHeight + 'px;'"
          >
            USERLIST
          </div>
        </div>
        <div
          v-if="isLogIn"
          class="flex-row-hc-vc tab-box v-full pointer visible-option"
          :class="curTabID === 3 ? 'selected' : ''"
          @click="btnAutoClick"
        >
          <div
            class="rotate-text fs16 bold"
            :style="'width: ' + tabHeight + 'px;'"
          >
            AUTO
          </div>
        </div>
      </div>
      <chat-list class="h-full" :class="curTabID === 0 ? '' : 'invisible'" />
      <history-list
        class="h-full"
        :class="curTabID === 1 ? '' : 'invisible'"
        :history-infos="historyInfos"
      />
      <user-list
        :user-infos="userInfos"
        :clear-min-height="true"
        :style="listHeightStyleObj"
        class="h-full"
        :class="curTabID === 2 ? '' : 'invisible'"
      />
      <auto-betting-value-box
        class="h-full visible-option"
        :class="curTabID === 3 ? '' : 'invisible'"
        :title="autoBetBtnTitle"
        @changeAuto="changeAuto"
        @clickAutoBetting="clickAutoBetting"
      />
    </div>
  </div>
</template>
<script>
import HistoryList from '@/components/pc/main/HistoryList.vue'
import ChatList from '@/components/pc/main/ChatList.vue'
import AutoBettingValueBox from '@/components/pc/main/AutoBettingValueBox.vue'
import UserList from '@/components/pc/main/UserList.vue'
import variables from '@/assets/styles/variables.scss'

export default {
  name: 'HistoryChatBox',
  components: {
    HistoryList,
    ChatList,
    AutoBettingValueBox,
    UserList
  },
  props: {
    activeTabId: {
      type: Number,
      default: 0
    },
    isUserListShow: {
      type: Boolean,
      default: false
    },
    chatInfos: {
      type: Array,
      default() {
        return [
          {
            name: 'colly2',
            chatData: 'I am busy.'
          }
        ]
      }
    },
    historyInfos: {
      type: Array,
      default() {
        return [
          {
            timeStamp: '00:00',
            historyData: 'Daniel !bust100'
          }
        ]
      }
    },
    userInfos: {
      type: Array,
      default() {
        return [
          {
            name: 'colly2',
            amp: 2.11,
            bet: 1000,
            profit: '-'
          }
        ]
      }
    },
    autoBetBtnTitle: {
      type: String,
      default: 'Bet'
    }
  },
  data() {
    return {
      curTabID: this.activeTabId,
      tabHeight: 0,
      isLogIn: false
    }
  },
  computed: {
    titleBarStyleObj: {
      get() {
        return {
          height: variables.historyChatBoxHeaderHeight
        }
      }
    },
    mainContentStyleObj: {
      get() {
        const bettingBoxHeight = this.$store.getters['app/getBettingBoxHeight']
        const windowBoxWidth = this.$store.getters['app/getWindowBoxWidth']
        const windowBoxHeight = this.$store.getters['app/getWindowBoxHeight']
        const subPxs = []

        if (windowBoxWidth < 768) {
          if (windowBoxHeight < 1000) {
            subPxs.push('1000px')
            subPxs.push('' + '372px')
            // } else if (windowBoxHeight < 1000) {
            //   subPxs.push('100vh')
            //   subPxs.push('' + '372px')
          } else if (windowBoxHeight < 1150) {
            subPxs.push('1000px')
            subPxs.push('' + '372px')
          } else {
            subPxs.push('100vh')
            subPxs.push('' + variables.footerbarHeight)
            subPxs.push('' + '372px')
          }
        } else if (windowBoxHeight < 722) {
          subPxs.push('722px')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else if (windowBoxHeight < 1000) {
          subPxs.push('100vh')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else if (windowBoxHeight < 1150) {
          subPxs.push('1000px')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else {
          subPxs.push('100vh')
          subPxs.push('' + variables.footerbarHeight)
          subPxs.push('' + bettingBoxHeight + 'px')
        }
        subPxs.push('' + variables.navbarHeightLg)
        subPxs.push('' + variables.mainContainerPaddingTop)
        subPxs.push('' + variables.mainContainerPaddingBottom)
        subPxs.push('' + variables.historyChatBoxMarginTop)
        subPxs.push('' + variables.historyChatBoxHeaderHeight)
        subPxs.push('' + variables.topLineBoxBorderHeight)
        subPxs.push('' + variables.topLineBoxBorderHeight)

        return {
          'min-height': 'calc(' + subPxs.join(' - ') + ')'
        }
      }
    },
    listHeightStyleObj: {
      get() {
        const bettingBoxHeight = this.$store.getters['app/getBettingBoxHeight']
        const windowBoxWidth = this.$store.getters['app/getWindowBoxWidth']
        const windowBoxHeight = this.$store.getters['app/getWindowBoxHeight']
        const subPxs = []

        if (windowBoxWidth < 768) {
          if (windowBoxHeight < 1000) {
            subPxs.push('1000px')
            subPxs.push('' + '372px')
            // } else if (windowBoxHeight < 1000) {
            //   subPxs.push('100vh')
            //   subPxs.push('' + '372px')
          } else if (windowBoxHeight < 1150) {
            subPxs.push('1000px')
            subPxs.push('' + '372px')
          } else {
            subPxs.push('100vh')
            subPxs.push('' + variables.footerbarHeight)
            subPxs.push('' + '372px')
          }
        } else if (windowBoxHeight < 722) {
          subPxs.push('722px')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else if (windowBoxHeight < 1000) {
          subPxs.push('100vh')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else if (windowBoxHeight < 1150) {
          subPxs.push('1000px')
          subPxs.push('' + bettingBoxHeight + 'px')
        } else {
          subPxs.push('100vh')
          subPxs.push('' + variables.footerbarHeight)
          subPxs.push('' + bettingBoxHeight + 'px')
        }
        subPxs.push('' + variables.navbarHeightLg)
        subPxs.push('' + variables.mainContainerPaddingTop)
        subPxs.push('' + variables.mainContainerPaddingBottom)
        subPxs.push('' + variables.historyChatBoxMarginTop)
        subPxs.push('' + variables.historyChatBoxHeaderHeight)
        subPxs.push('' + variables.topLineBoxBorderHeight)
        subPxs.push('' + variables.topLineBoxBorderHeight)

        return {
          'min-height': 'calc(' + subPxs.join(' - ') + ') !important',
          height: '233px !important'
        }
      }
    },
    token() {
      return this.$store.state.user.token
    }
  },
  watch: {
    isUserListShow(newVal) {
      if (newVal === false && this.curTabID === 2) this.curTabID = 1
    },
    token: {
      handler(val, oldVal) {
        this.isLogIn = val !== undefined && val !== ''
      },
      immediate: true
    }
  },
  mounted() {
    const self = this

    window.addEventListener('resize', this.__resizeHandler)
    this.__resizeHandler()

    this.$bus.$on('on-change-login', function() {
      self.__resizeHandler()
    })
  },
  updated() {
    this.__resizeHandler()
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.__resizeHandler)
  },
  methods: {
    __resizeHandler() {
      let nTabCnt = 2
      const totalHeight =
        this.$refs.vertical_tab_box === undefined ||
        this.$refs.vertical_tab_box === null
          ? 0
          : this.$refs.vertical_tab_box.clientHeight
      const width = this.$store.getters['app/getWindowBoxWidth']

      if (this.isUserListShow) nTabCnt++
      if (this.isLogIn) nTabCnt++
      this.tabHeight = totalHeight / nTabCnt - 10

      if (width < 768 && this.curTabID === 3) this.curTabID = 2
    },
    btnChatClick() {
      this.curTabID = 0
    },
    btnHistoryClick() {
      this.curTabID = 1
    },
    btnUserListClick() {
      this.curTabID = 2
    },
    btnAutoClick() {
      this.curTabID = 3
    },
    changeAuto(autoData) {
      this.$emit('changeAuto', autoData)
    },
    clickAutoBetting() {
      const windowBoxWidth = this.$store.getters['app/getWindowBoxWidth']

      if (windowBoxWidth > 767) this.$emit('clickAutoBetting')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.title-bar {
  position: relative;
  background-color: $gray4f;
}

.leave-box {
  position: absolute;
  right: 10px;
}

.leave-box:hover {
  color: black;
}

.value-box {
  height: 233px;
}

.tab-box {
  position: relative;
  border: 2px solid $gray4f;
  border-top: 0px;
  color: $grayb3;
  background-color: $gray4f;
  width: 30px;
}

.tab-box.selected {
  color: $darkorange;
  background-color: $gray09;
}

.rotate-text {
  position: absolute;
  transform: rotate(-90deg);
  white-space: nowrap;
  text-align: center;
  overflow: hidden;
}

.ic_size_7 {
  width: 7px;
  height: 7px;
}

.invisible {
  display: none;
}

@media (max-width: 767px) {
  .visible-option {
    display: none;
  }
}
</style>
