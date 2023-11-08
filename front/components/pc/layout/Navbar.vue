<template>
  <div class="navbar h-full fc-white">
    <div class="navbar-large">
      <menubar ref="menubar">
        <div slot="menubar-slot">
          <div class="navbar-btn-group" @click="hideMenu()">
            <span
              v-b-modal.login
              class="link-text text-center padding-box pointer"
              :class="clsVisible"
            >
              LOGIN
            </span>
            <span
              v-b-modal.register
              class="link-text text-center padding-box pointer"
              :class="clsVisible"
            >
              新規登録
            </span>
            <span
              v-b-modal.profile
              class="fc-darkorange text-center padding-box pointer username-box"
              :class="clsInvisible"
            >
              {{ userName }}
            </span>
            <span
              class="fc-darkorange text-center padding-box bits-box"
              :class="clsInvisible"
            >
              BITS: {{ walletInfo | toThousandFilter }}
            </span>
            <span
              v-if="isLogIn"
              class="removable-box link-text padding-box pointer"
              @click="onLogOut"
            >
              Logout
            </span>
          </div>
        </div>
      </menubar>
    </div>
  </div>
</template>

<script>
import Menubar from '@/components/pc/layout/Menubar.vue'

export default {
  components: {
    Menubar
  },
  props: {},
  data() {
    return {
      isLogIn: false,
      userName: '',
      walletInfo: 0
    }
  },
  computed: {
    token() {
      return this.$store.state.user.token
    },
    storeUserName() {
      return this.$store.state.user.name
    },
    wallet() {
      return this.$store.state.user.walletInfo
    },
    clsVisible: {
      get() {
        return {
          invisible: this.isLogIn
        }
      }
    },
    clsInvisible: {
      get() {
        return {
          invisible: !this.isLogIn
        }
      }
    }
  },
  watch: {
    token: {
      handler(val, oldVal) {
        this.isLogIn = val !== undefined && val !== ''
      },
      immediate: true
    },
    storeUserName: {
      handler(val, oldVal) {
        this.userName = val !== undefined && val !== '' ? val : 'Player'
      },
      immediate: true
    },
    wallet: {
      handler(val, oldVal) {
        this.walletInfo = val !== undefined && val !== '' ? val : 0
      },
      immediate: true
    }
  },
  mounted() {},
  methods: {
    async onLogOut() {
      await this.$store.dispatch('user/logout', {})
      this.hideMenu()

      this.$bus.$emit('on-change-login', { isLogIn: false })
    },
    hideMenu() {
      this.$bus.$emit('hide-menu')
    }
  }
}
</script>

<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.link-text {
  color: $grayb3;
}

.link-text:hover {
  color: $darkorange;
}

.navbar-btn-group {
  font-size: 18px;
  font-weight: bold;

  span {
    margin-right: 50px;
  }
  span:last-child {
    margin-right: 0px;
  }

  .username-box {
    font-size: 23px;
  }
  .bits-box {
    font-size: 13px;
  }
}

.invisible {
  display: none;
}

.padding-box {
  padding-top: 15px;
  padding-bottom: 15px;
}

@media (max-width: $menuVisibleWidth) {
  .removable-box {
    display: none;
  }
}

@media (max-width: 640px) {
  .navbar-btn-group {
    font-size: 14px;

    span {
      margin-right: 20px;
    }
  }
}

@media (max-width: 480px) {
  .navbar-btn-group {
    span {
      margin-right: 10px;
    }
  }

  .padding-box {
    padding-top: 0px;
    padding-bottom: 0px;
  }
}
</style>
