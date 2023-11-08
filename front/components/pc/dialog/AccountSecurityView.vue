<template>
  <div class="flex-col-hc-vc fs16">
    <div
      v-if="securityType === -1"
      class="fc-orangeyellow h-full p-t-35 p-b-25"
    >
      <div class="fc-white bold">CHOOSE AN OPTION</div>
      <separator-line class="m-t-15 m-b-15" />
      <div @click="updatePassword()">
        <span class="fs13 p-r-5">▶</span>
        <span class="fs15 pointer">Update Password</span>
      </div>
      <separator-line class="m-t-15 m-b-15" />
      <div @click="updateEmail()">
        <span class="fs13 p-r-5">▶</span>
        <span class="fs15 pointer">Update Email</span>
      </div>
      <separator-line class="m-t-15 m-b-15" />
      <div @click="twoFactorAuth()">
        <span class="fs13 p-r-5">▶</span>
        <span class="fs15 pointer">Two Factor Authentication</span>
      </div>
      <separator-line class="m-t-15 m-b-15" />
      <div @click="knowYourCustomer()">
        <span class="fs13 p-r-5">▶</span>
        <span class="fs15 pointer">
          Know Your Customer（各種証明書提出）
        </span>
      </div>
    </div>
    <b-form
      v-if="securityType === 0"
      class="flex-col-hc-vc fc-white h-full p-t-35 p-b-25"
      @submit="onPwdSubmit"
    >
      <div class="flex-col-hc-vt h-full">
        <div class="fc-white bold m-b-20">UPDATE PASSWORD</div>
        <span class="fs14">
          新しいパスワードを保存するまでフォームを送信しないでください！
        </span>
        <input-with-label-box
          title="新しいパスワード"
          :is-round="false"
          :small-text="true"
          class="m-t-15 m-b-25"
          :var-link="edit_pwd.pwd"
          :is-required="true"
          :status="edit_pwd.submitStatus"
          @inputChanged="editPwdChanged"
        >
          <div slot="button-content" class="v-full">
            <div
              class="refresh-btn flex-row-hc-vc pointer v-full"
              @click="generatePassword"
            >
              <svg-icon
                icon-class="icon_refresh"
                class="ic-size-27 custom_size"
              />
            </div>
          </div>
        </input-with-label-box>
      </div>
      <button3-d type="submit" title="送信" />
    </b-form>
    <b-form
      v-if="securityType === 1"
      class="flex-col-hc-vc fc-white h-full p-t-35 p-b-25"
      @submit="onEmailSubmit"
    >
      <div class="flex-col-hc-vt h-full">
        <div class="fc-white bold m-b-20">EDIT EMAIL</div>
        <input-with-label-box
          title="Recovery Email"
          :is-round="false"
          :small-text="true"
          class="m-b-25"
          :var-link="edit_email.email"
          :is-required="true"
          :status="edit_email.submitStatus"
          @inputChanged="editEmailChanged"
        />
      </div>
      <button3-d type="submit" title="送信" />
    </b-form>
    <div
      v-if="securityType === 2"
      class="flex-col-hc-vc fc-white h-full p-t-35 p-b-25"
    >
      <div class="flex-col-hc-vt h-full">
        <div class="fc-white bold m-b-20">TWO-FACTOR AUTHENTICATION</div>
        <span class="fs14">
          1.QRコードをスキャンするか、Authenticatorアプリに手動でシークレットを入力します。
        </span>
        <span class="fs14">
          2.[送信]をクリックし、認証アプリから取得した2FAコードとパスワードを入力して確認します。
        </span>
        <div class="flex-row-hc-vc h-full">
          <img src="/imgs/dialog/qrcode.png" class="m-t-15" />
        </div>
        <input-with-label-box
          title="Secret:"
          :is-round="false"
          :small-text="true"
          class="m-t-15 m-b-10"
        />
        <span class="fc-redpink fs13">
          注意：アカウントからロックアウトされないように、秘密を紙に書き留めて安全な場所に保管することをお勧めします。
        </span>
      </div>
      <button3-d title="送信" class="m-t-25" />
    </div>
    <div
      v-if="securityType === 3"
      class="flex-col-hc-vc fc-white h-full p-t-35 p-b-50"
    >
      <div class="flex-col-hc-vt h-full">
        <div v-if="!kyc_reg" class="fc-white bold m-b-20">
          Know Your Customer（各種証明書提出）
        </div>
        <div v-if="kyc_reg" class="fc-white bold m-b-20">
          Know Your Customer<span style="color: #dc3545">（KYC認証済み）</span>
        </div>
        <div class="h-full scrollbar-wrapper">
          <separator-line class="m-t-15 m-b-5" />
          <know-your-customer-item
            title="免許証・パスポート"
            img-name="security_global"
            :upload-success="passport_upload"
          />
          <separator-line class="m-t-5 m-b-5" />
          <know-your-customer-item
            title="IDセルフィー"
            img-name="security_person"
            :upload-success="idcard_upload"
          />
          <separator-line class="m-t-5 m-b-15" />
        </div>
        <span class="fc-redpink fs13">
          ※免許証かパスポートのいずれか一つを写真に撮りアップロードして下さい。その際、四隅を必ず入れて文字が読めるようにお願いいたします。
        </span>
      </div>
    </div>
  </div>
</template>
<script>
import SeparatorLine from '@/components/pc/dialog/SeparatorLine.vue'
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import KnowYourCustomerItem from '@/components/pc/dialog/KnowYourCustomerItem.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import InputConfirmPwd from '@/components/pc/dialog/ConfirmPwd/InputConfirmPwd.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'AccountSecurityView',
  components: {
    SeparatorLine,
    InputWithLabelBox,
    KnowYourCustomerItem,
    Button3D
  },
  mixins: [GlobalMixin],
  props: {},
  data() {
    return {
      securityType: -1,
      edit_pwd: {
        pwd: '',
        submitStatus: 'bet'
      },
      edit_email: {
        email: '',
        submitStatus: 'bet'
      },
      passport_upload: false,
      idcard_upload: false,
      kyc_reg: false
    }
  },
  computed: {
    confirmPwd() {
      const val = this.$store.state.app.confirmPwd
      return val !== undefined ? val : ''
    }
  },
  beforeDestroy() {
    this.$root.$off('bv::modal::hidden')
  },
  mounted() {
    this.generatePassword()
    this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
      if (modalId === 'confirmpwd' && this.confirmPwd.trim().length >= 1) {
        if (this.securityType === 0) {
          this.onPwdChangeSubmit()
        } else if (this.securityType === 1) {
          this.onEmailChangeSubmit()
        }
      }
    })
  },
  methods: {
    generatePassword() {
      const chPassword = 'abcdefghijklmnopqrstuvwxyz1234567890'
      let pwd = ''
      for (let i = 0; i < parseInt(Math.random() * 6 + 6); i++) {
        pwd += chPassword[parseInt(Math.random() * chPassword.length)]
      }

      this.edit_pwd.pwd = pwd
    },
    updatePassword() {
      this.securityType = 0
    },
    updateEmail() {
      this.securityType = 1
    },
    twoFactorAuth() {
      this.securityType = 2
    },
    knowYourCustomer() {
      this.securityType = 3
      const self = this
      this.$axios.post('/User/get_kyc_info').then((response) => {
        const { status, data } = response
        if (status === 'success') {
          self.passport_upload = data.passport_upload
          self.idcard_upload = data.idcard_upload
          self.kyc_reg = data.kyc_reg
        }
      })
    },
    editPwdChanged(value) {
      this.edit_pwd.pwd = value
    },
    editEmailChanged(value) {
      this.edit_email.email = value
    },
    async onPwdSubmit(evt) {
      evt.preventDefault()
      await this.$store.dispatch('app/setConfirmPwd', '')
      this.showConfirmPwdDlg()
    },
    async onEmailSubmit(evt) {
      evt.preventDefault()
      await this.$store.dispatch('app/setConfirmPwd', '')
      this.showConfirmPwdDlg()
    },
    async onPwdChangeSubmit() {
      this.edit_pwd.submitStatus = 'disabled'
      this.$bus.$emit('show-overlay')
      try {
        const response = await this.$axios.post('/User/modify', {
          pwd: this.confirmPwd,
          new_pwd: this.edit_pwd.pwd
        })
        if (response.status === 'success') {
          this.$bus.$emit('hide-overlay')
          this.edit_pwd.submitStatus = 'bet'
          this.$bvModal.hide('profile')
          this.$bus.$emit('on-change-password', { type: 1 })
        } else {
          this.showToast('Error', response.data, 'error')
          this.$bus.$emit('hide-overlay')
          this.edit_pwd.submitStatus = 'bet'
        }
      } catch (err) {
        this.errors = null
        this.edit_pwd.submitStatus = 'bet'
      }
    },
    async onEmailChangeSubmit() {
      this.edit_email.submitStatus = 'disabled'
      this.$bus.$emit('show-overlay')
      try {
        const response = await this.$axios.post('/User/modify', {
          pwd: this.confirmPwd,
          email: this.edit_email.email
        })
        if (response.status === 'success') {
          this.$store.dispatch('user/setEmail', this.edit_email.email)
          this.$bus.$emit('hide-overlay')
          this.edit_email.submitStatus = 'bet'
          this.$bvModal.hide('profile')
          this.$bus.$emit('on-change-password', { type: 2 })
          /* this.$bvToast.toast('Updating email successed', {
            title: 'Quick bit',
            variant: 'success',
            autoHideDelay: 5000,
            appendToast: true
          }) */
        } else {
          /* this.$bvToast.toast(response.data, {
            title: 'Quick bit',
            variant: 'danger',
            autoHideDelay: 5000,
            appendToast: true
          }) */
          this.showToast('Error', response.data, 'error')
          this.$bus.$emit('hide-overlay')
          this.edit_email.submitStatus = 'bet'
        }
      } catch (err) {
        this.errors = null
        this.edit_email.submitStatus = 'bet'
      }
    },
    showConfirmPwdDlg() {
      this.$root.$emit('bv::show::modal', 'confirmpwd')
    },
    passwordConfirmMsg() {
      const h = this.$createElement
      // Using HTML string
      const titleVNode = h(
        'div',
        {
          class: ['confirm-password--title fc-darkorange']
        },
        [h('div', { class: ['fs28'] }, ['CONFIRM'])]
      )
      // More complex structure
      const messageVNode = h(
        'div',
        { class: ['confirm-password--content p-2'] },
        [
          h('p', { class: ['text-left bold fs16'] }, [
            'Please confirm that you have saved your new password by entering your old one.'
          ]),
          h('p', { class: ['text-left fs16'] }, ['Password']),
          h(InputConfirmPwd)
        ]
      )
      // We must pass the generated VNodes as arrays
      return this.$bvModal.msgBoxConfirm([messageVNode], {
        title: [titleVNode],
        headerClass: 'confirm-password--header',
        footerClass: 'p-2',
        buttonSize: 'sm',
        size: 'sm',
        okVariant: 'danger',
        okTitle: 'Ok',
        cancelTitle: 'Cancel',
        hideHeaderClose: false,
        centered: true
      })
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.refresh-btn {
  background: $green;
  padding: 0px 10px;
}

.refresh-btn:hover {
  background: $greenhover;
}

.ic-size-27 {
  width: 27px;
  height: 27px;
}

.custom_size {
  width: 20px !important;
  height: 25px !important;
}
</style>
