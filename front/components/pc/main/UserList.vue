<template>
  <div class="list-container flex-col-ht-vc top-line-box" :class="classObj">
    <div class="flex-row-hc-vc title-bar fc-grayb3 fs16 fw500 h-full">
      <div class="flex3 text-left fw500">
        {{ mode == 0 ? 'USER' : 'PLAYER' }}
      </div>
      <div class="flex3 text-center">
        {{ mode == 0 ? '@' : 'CASHED OUT' }}
      </div>
      <div class="flex3 text-center fw500">BET</div>
      <div class="flex3 text-center fw500">PROFIT</div>
      <div v-if="mode == 1" class="flex3 text-center fw500">ID</div>
    </div>
    <div
      class="item-container beauty-scrollbar-wrapper h-full"
      :class="classObj"
    >
      <user-list-item
        v-for="(item, i) in userInfos"
        :key="i"
        :mode="mode"
        :index="i"
        :user-info="item"
        :profit-val="item.profit"
        :is-crashed="isCrashed"
      />
    </div>
    <div
      v-if="mode == 0"
      class="user-list-footer flex-space-between-vc h-full p-l-15 p-r-15"
    >
      <div>
        <span>Online:</span>
        <span>{{ onlineUserCount }}</span>
      </div>
      <div>
        <span>Playing:</span>
        <span>{{ userInfos.length | toThousandFilter }}</span>
      </div>
      <div>
        <span>Betting:</span>
        <span class="nowrap">{{ getBetAmount | toThousandFilter }} bits</span>
      </div>
    </div>
  </div>
</template>
<script>
import UserListItem from '@/components/pc/main/UserListItem.vue'

export default {
  name: 'UserList',
  components: {
    UserListItem
  },
  props: {
    onlineUserCount: {
      type: Number,
      default: 0
    },
    userInfos: {
      type: Array,
      default() {
        return []
      }
    },
    clearMinHeight: {
      type: Boolean,
      default: false
    },
    isCrashed: {
      type: Boolean,
      default: false
    },
    mode: {
      type: Number,
      default: 0
    }
  },
  computed: {
    classObj: {
      get() {
        return {
          'set-min': !this.clearMinHeight
        }
      }
    },
    getBetAmount() {
      let i = 0
      let betAmount = 0
      for (i = 0; i < this.userInfos.length; i++) {
        betAmount += parseInt(this.userInfos[i].value)
      }
      return betAmount
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.title-bar {
  background-color: black;
  min-height: $bettingUserListHeaderHeight;
  padding-left: 10px;
  padding-right: 5px;
}

.list-container {
  height: 611px;
}

.list-container.set-min {
  min-height: calc(
    100vh - #{$navbarHeightLg} - #{$footerbarHeight} - #{$mainContainerPaddingTop} -
      #{$mainContainerPaddingBottom} - #{$topLineBoxBorderHeight} + 2px
  );
}

@media (max-height: 1150px) {
  .list-container {
    height: calc(
      1000px - #{$navbarHeightLg} - #{$mainContainerPaddingTop} - #{$mainContainerPaddingBottom} -
        #{$topLineBoxBorderHeight} + 2px
    );
  }
}

@media (max-height: 1000px) {
  .list-container {
    height: calc(
      100vh - #{$navbarHeightLg} - #{$mainContainerPaddingTop} - #{$mainContainerPaddingBottom} -
        #{$topLineBoxBorderHeight} + 2px
    );
  }
}

@media (max-height: 734px) {
  .list-container {
    height: 605px;
  }
}

.user-list-footer {
  font-size: 14px;
  height: $bettingUserListFooterHeight;
  line-height: 1;
  text-align: center;
  color: white;
}

.item-container {
  height: calc(
    100% - #{$bettingUserListHeaderHeight} - #{$bettingUserListFooterHeight}
  );
  background: $gray2b;
}

.item-container.set-min {
  min-height: calc(
    100vh - #{$navbarHeightLg} - #{$footerbarHeight} - #{$bettingUserListHeaderHeight} -
      #{$bettingUserListFooterHeight} - #{$mainContainerPaddingTop} - #{$mainContainerPaddingBottom} -
      #{$topLineBoxBorderHeight}
  );
}
</style>
