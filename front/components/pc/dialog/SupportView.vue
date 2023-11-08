<template>
  <b-form class="flex-col-hc-vc fs16 fc-white m-t-20" @submit="onSubmit">
    <div class="fs20">We're here to help</div>
    <div class="fs14 m-t-15 m-b-15">
      Check our <span class="highlight pointer" @click="showFaq">FAQ</span>.
      Didn't find a answer? Submit a message through this form and we will email
      you back.
    </div>
    <div v-if="!isLogIn" class="fs14 m-b-15 not-logged-in">
      <span class="italic-highlight">Not logged in.</span> This support message
      won't be attached to an account. If it is about your account, please
      <span class="highlight pointer" @click="showLogIn">log in</span>.
    </div>
    <div class="h-full">
      <input-with-label-box
        v-if="isLogIn"
        title="Username:"
        :is-round="false"
        :small-text="true"
        :is-required="true"
        :var-link="form.user"
        @inputChanged="changeUserName"
      />
      <input-with-label-box
        class="m-t-15"
        title="Email"
        :is-round="false"
        :small-text="true"
        :var-link="form.email"
        :is-required="true"
        @inputChanged="changeEmail"
      />
      <input-with-label-box
        class="m-t-15"
        title="Message"
        :is-textarea="true"
        :is-round="false"
        :small-text="true"
        :var-link="form.content"
        :is-required="true"
        @inputChanged="changeContent"
      />
    </div>
    <button3-d
      class="m-t-15 m-b-35"
      title="送信"
      type="submit"
      :status="submitBtnStatus"
    />
  </b-form>
</template>
<script>
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'SupportView',
  components: {
    InputWithLabelBox,
    Button3D
  },
  mixins: [GlobalMixin],
  props: {},
  data() {
    return {
      form: {
        user: '',
        email: '',
        content: ''
      },
      submitBtnStatus: 'bet',
      isLogIn: false
    }
  },
  computed: {
    token() {
      return this.$store.state.user.token
    },
    storeUserName() {
      return this.$store.state.user.name
    },
    storeEmail() {
      return this.$store.state.user.email
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
        this.form.user = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    },
    storeEmail: {
      handler(val, oldVal) {
        this.form.email = val !== undefined && val !== '' ? val : ''
      },
      immediate: true
    }
  },
  methods: {
    showLogIn() {
      this.$root.$emit('bv::hide::modal', 'support')
      this.$root.$emit('bv::show::modal', 'login')
    },
    showFaq() {
      this.$root.$emit('bv::hide::modal', 'support')
      this.$root.$emit('bv::show::modal', 'faq')
    },
    changeUserName(inputValue) {
      this.form.user = inputValue
    },
    changeEmail(inputValue) {
      this.form.email = inputValue
    },
    changeContent(inputValue) {
      this.form.content = inputValue
    },
    async onSubmit(evt) {
      evt.preventDefault()

      this.submitBtnStatus = 'disabled'
      this.$bus.$emit('show-overlay')
      try {
        const response = await this.$axios.post('/User/support')
        if (response.status === 'success') {
          this.showToast(
            'Success',
            'メッセージは正常に送信されました。',
            'success'
          )
        }
        this.$bus.$emit('hide-overlay')
        this.submitBtnStatus = 'bet'
      } catch (err) {
        this.errors = null
        this.submitBtnStatus = 'bet'
      }
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';
.highlight {
  color: $midorangeyellow;
}
.italic-highlight {
  color: $darkbrownyellow;
  font-style: italic;
}
.not-logged-in {
  color: $redpink;
}
</style>
