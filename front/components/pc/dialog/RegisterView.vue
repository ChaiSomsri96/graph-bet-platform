<template>
  <b-form class="flex-col-hc-vc fc-white fs13" @submit="onSubmit">
    <input-with-label-box
      title="Username:"
      :is-round="false"
      class="m-t-60"
      :var-link="szUserName"
      @inputChanged="onNameChange"
    />
    <input-with-label-box
      title="Password:"
      :is-round="false"
      class="m-t-25"
      :var-link="szPassword"
      @inputChanged="onPasswordChange"
    >
      <div slot="button-content" class="v-full">
        <div
          class="refresh-btn flex-row-hc-vc pointer v-full"
          @click="generatePassword()"
        >
          <svg-icon icon-class="icon_refresh" class="ic-size-27 custom_size" />
        </div>
      </div>
    </input-with-label-box>
    <input-with-label-box
      title="Affiliate Code:"
      :is-round="false"
      class="m-t-25"
      :var-link="szAffiliateCode"
      :is-required="false"
      @inputChanged="onPasswordChange"
    />
    <div class="m-t-35 h-full">
      アカウントを復旧する際のメールアドレスを入力してください
    </div>
    <div class="flex-row-hl-vc m-t-15 h-full">
      <input
        type="radio"
        name="email"
        value=""
        class="input-box m-r-10"
        checked
        @click="UseEmail(true)"
      />
      <input-with-label-box
        title="Email:"
        :is-round="false"
        :is-disable="isEmailDisable"
        input-type="email"
        :var-link="szEmail"
        @inputChanged="onEmailChange"
      />
    </div>
    <div class="flex-row-hl-vc m-t-25 h-full">
      <input
        id="use_email_no"
        type="radio"
        name="email"
        value=""
        class="input-box m-r-10"
        @click="UseEmail(false)"
      />
      <label for="use_email_no" class="label_text">
        アカウントを復旧する手段は必要無いので、メールアドレスを登録しない。
      </label>
    </div>
    <div class="splitter m-t-30 h-full"></div>
    <div class="flex-row-hl-vc m-t-20 h-full">
      <input
        id="accept_terms_service"
        ref="isRegisterEnable"
        type="checkbox"
        value=""
        class="input-box m-r-10"
        @click="onEnable()"
      />
      <label for="accept_terms_service" class="label_text">
        利用規約を読み、同意します。
      </label>
    </div>
    <button3-d
      title="進む"
      type="submit"
      :status="isEnable ? 'bet' : 'disabled'"
      class="m-t-50 m-b-35"
    />
  </b-form>
</template>
<script>
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'RegisterView',
  components: {
    InputWithLabelBox,
    Button3D
  },
  mixins: [GlobalMixin],
  props: {
    topUsers: {
      type: Array,
      default() {
        return []
      }
    }
  },
  data() {
    return {
      isEnable: false,
      isEmailDisable: false,
      szPassword: '',
      szUserName: '',
      szAffiliateCode: '',
      szEmail: ''
    }
  },
  mounted() {
    if (
      this.$route.query.aff_code !== undefined &&
      this.$route.query.aff_code !== ''
    ) {
      this.szAffiliateCode = this.$route.query.aff_code
    }
  },
  methods: {
    onEnable() {
      this.isEnable = this.$refs.isRegisterEnable.checked
    },
    generatePassword() {
      const chPassword = 'abcdefghijklmnopqrstuvwxyz1234567890'
      let szPassword = ''
      for (let i = 0; i < parseInt(Math.random() * 6 + 6); i++) {
        szPassword += chPassword[parseInt(Math.random() * chPassword.length)]
      }

      this.szPassword = szPassword
    },
    onPasswordChange(newPassword) {
      this.szPassword = newPassword
    },
    onNameChange(newName) {
      this.szUserName = newName
    },
    onEmailChange(newEmail) {
      this.szEmail = newEmail
    },
    UseEmail(newValue) {
      this.isEmailDisable = !newValue
    },
    async onSubmit(evt) {
      evt.preventDefault()

      let szEmail = this.szEmail
      // const self = this

      if (this.isEmailDisable) szEmail = ''

      this.$bus.$emit('show-overlay')
      try {
        const { status, resMsg } = await this.$axios.post('/User/register', {
          username: this.szUserName,
          password: this.szPassword,
          aff_code: this.szAffiliateCode,
          email: szEmail
        })
        this.$bus.$emit('hide-overlay')
        if (status === 'success') {
          this.$bvModal.hide('register')
          this.$bus.$emit('on-register-success')
        } else {
          /* this.$bvToast.toast(resMsg, {
            title: 'Quick bit',
            variant: 'danger',
            autoHideDelay: 5000,
            appendToast: true
          }) */

          this.showToast('Error', resMsg, 'error')
        }
      } catch (err) {}
      /*
      this.$axios
        .post('/User/register', {
          username: this.szUserName,
          password: this.szPassword,
          email: szEmail
        })
        .then((response) => {
          self.$bus.$emit('hide-overlay')
          if (response.status === 'success') {
            self.$bvModal.hide('register')
            this.$bus.$emit('on-register-success')
          } else {
            self.$bvToast.toast(response.res_msg, {
              title: 'Quick bit',
              variant: 'danger',
              autoHideDelay: 5000,
              appendToast: true
            })
          }
        })
        .catch((error) => {
          error = 0
        }) */
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

.splitter {
  border-top: 1px solid $gray4a;
}

.input-box {
  min-width: 13px;
  min-height: 13px;
}

label {
  margin-bottom: 0rem;
}

.label_text {
  cursor: pointer;
}

.custom_size {
  width: 20px !important;
  height: 25px !important;
}
</style>
