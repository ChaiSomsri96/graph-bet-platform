<template>
  <b-form class="flex-col-hc-vc p-b-30 fc-white" @submit="onSubmit">
    <input-with-label-box
      title="Username:"
      class="m-t-60"
      @inputChanged="changeName"
    />
    <input-with-label-box
      title="Password:"
      input-type="password"
      class="m-t-20"
      @inputChanged="changePassword"
    />
    <button3-d
      type="submit"
      title="ログイン"
      class="m-t-35"
      :status="logInBtnStatus"
    />
    <div class="m-t-30 captcha-parent">
      <VueRecaptcha
        ref="recaptcha"
        sitekey="6Ld_ctQUAAAAAMR_O7wB0RF1MPXZmqq1IiInH5ZH"
        theme="dark"
        :load-recaptcha-script="true"
        @verify="onVerify"
        @expired="expired"
      >
      </VueRecaptcha>
      <span v-if="captchaRequired" class="captcha-label fs12"
        >Captcha is required</span
      >
    </div>
    <nuxt-link to="#" class="m-t-40">
      パスワードを忘れた
    </nuxt-link>
  </b-form>
</template>
<script>
import VueRecaptcha from 'vue-recaptcha'
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import GlobalMixin from '~/mixins/global'

export default {
  name: 'LoginView',
  components: {
    InputWithLabelBox,
    Button3D,
    VueRecaptcha
  },
  mixins: [GlobalMixin],
  props: {},
  data() {
    return {
      userInf: {},
      logInBtnStatus: 'bet',
      captchaRequired: false,
      robot: true
    }
  },
  computed: {},
  methods: {
    onVerify(response) {
      if (response) {
        this.robot = false
        this.captchaRequired = false
      }
    },
    expired() {
      this.robot = true
    },
    changeName(inputValue) {
      this.userInf.username = inputValue
    },
    changePassword(inputValue) {
      this.userInf.password = inputValue
    },
    async onSubmit(evt) {
      evt.preventDefault()
      if (this.robot) {
        this.captchaRequired = true
        return
      }
      this.logInBtnStatus = 'disabled'
      this.$bus.$emit('show-overlay')
      try {
        const { status, data } = await this.$store.dispatch('user/login', {
          username: this.userInf.username,
          password: this.userInf.password
        })
        this.$bus.$emit('hide-overlay')
        if (status === 'success') {
          this.$bvModal.hide('login')
          this.$store.dispatch('user/getWalletInfo')
          this.$store.dispatch('user/getBettingData')
          this.$store.dispatch('user/getTotalProfit')
          this.$bus.$emit('on-change-login', { isLogIn: true })
        } else {
          this.showToast('Error', data, 'error')
        }
        this.logInBtnStatus = 'bet'
      } catch (err) {
        this.errors = null
        this.logInBtnStatus = 'bet'
      }
    }
  }
}
</script>
<style lang="scss">
.captcha-parent {
  position: relative;
}
.captcha-label {
  color: #dc3545;
  position: absolute;
  bottom: -18px;
  right: 0px;
}
// @media (min-width: 640px) {
//   .modal-dialog {
//     width: 80%;
//   }
// }

// @media (min-width: 752px) {
//   .modal-dialog {
//     width: 70%;
//   }
// }

// @media (min-width: 864px) {
//   .modal-dialog {
//     width: 60%;
//   }
// }
</style>
