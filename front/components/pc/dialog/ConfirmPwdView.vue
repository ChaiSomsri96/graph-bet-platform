<template>
  <b-form class="flex-col-hc-vc p-b-30 fc-white" @submit="onSubmit">
    <div class="fs15 fw500 h-full m-t-15">
      古いパスワードを入力して、新しいパスワードを保存したことを確認してください。
    </div>
    <input-with-label-box
      title="Password:"
      class="m-t-20"
      input-type="password"
      :var-link="pwd"
      @inputChanged="changeConfirmPwd"
    />
    <button3-d type="submit" title="提出する" class="m-t-15" />
  </b-form>
</template>
<script>
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import Button3D from '@/components/pc/common/Button3D.vue'

export default {
  name: 'ConfirmPwdView',
  components: {
    InputWithLabelBox,
    Button3D
  },
  props: {},
  data() {
    return {
      pwd: this.confirmPwd
    }
  },
  computed: {
    confirmPwd() {
      const val = this.$store.state.app.confirmPwd
      return val !== undefined ? val : ''
    }
  },
  // watch: {
  //   pwd: {
  //     handler(val, oldVal) {
  //       this.$store.dispatch('app/setConfirmPwd', val)
  //     }
  //   }
  // },
  methods: {
    changeConfirmPwd(inputValue) {
      this.pwd = inputValue
    },
    async onSubmit(evt) {
      evt.preventDefault()
      try {
        await this.$store.dispatch('app/setConfirmPwd', this.pwd)
        this.$bvModal.hide('confirmpwd')
      } catch (err) {
        this.errors = null
      }
    }
  }
}
</script>
<style lang="scss"></style>
