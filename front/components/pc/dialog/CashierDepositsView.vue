<template>
  <div class="flex-col-hc-vc fs14 fc-white p-t-35 p-b-25">
    <input-with-label-box
      title="Bitcoin Deposit Address:"
      :is-round="false"
      :small-text="true"
      :var-link="value"
      :is-readonly="true"
      class="m-b-10"
    >
      <div
        slot="button-content"
        v-clipboard:copy="value"
        v-clipboard:success="onCopy"
        v-clipboard:error="onError"
        class="v-full"
      >
        <div class="skyblue-btn respn flex-row-hc-vc pointer v-full">
          <svg-icon icon-class="icon_copy" class="ic-size-27 m-r-5" />
          <span class="fs16">Copy</span>
        </div>
      </div>
    </input-with-label-box>
    <div class="h-full flex-row-hr-vc m-b-30">
      <div class="skyblue-btn respv flex-row-hc-vc pointer v-full">
        <svg-icon icon-class="icon_copy" class="ic-size-27 m-r-5" />
        <span class="fs16">Copy</span>
      </div>
    </div>
    <span>QRコード</span>
    <!--
    <img src="/imgs/dialog/qrcode.png" class="m-t-15 m-b-30" />
    -->
    <qrcode-vue
      :value="value"
      :size="size"
      level="H"
      class="qrcode_canvas m-t-15 m-b-30"
    />
    <div class="description-box flex-col-hl-vc">
      <span>
        Quick
        bitではビットコインを入金することができます。デポジットアドレスに送信された他の通貨（ビットコインキャッシュなど）はアカウントに入金することはできず、返金もできません。
      </span>
      <span>
        通常、デポジットを入金手続き後10分程度で入金が完了しますが、完了しない場合は十分な取引手数料が含まれていない可能性があります。また、最低入金額はありません。
      </span>
      <span>
        プライバシーを保護するために、預金ごとに新しい預金アドレスが生成されますが、古い預金アドレスは引き続きご利用可能です。
      </span>
    </div>
  </div>
</template>
<script>
import QrcodeVue from 'qrcode.vue'
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'CashierDepositsView',
  components: {
    InputWithLabelBox,
    QrcodeVue
  },
  mixins: [GlobalMixin],
  props: {},
  data() {
    return {
      value: '',
      size: 128
    }
  },
  mounted() {
    const self = this
    this.$axios.post('/Bitcoin/get_deposit_address').then((response) => {
      const { status, data } = response
      if (status === 'success') {
        self.value = data
      }
    })
  },
  methods: {
    onCopy() {
      this.showToast(
        'Success',
        'Quickbit BTCのウォレットアドレスがクリップボードにコピーされました。',
        'success'
      )
    },
    onError() {
      this.showToast('Error', 'クリップボードにはコピーできません。', 'error')
    }
  }
}
</script>
<style lang="scss" scoped>
@import '@/assets/styles/variables.scss';

.description-box {
  line-height: 2;
}

.ic-size-27 {
  width: 27px;
  height: 27px;
}

.skyblue-btn {
  background: $skyblue;
  padding: 0px 10px;
}

.skyblue-btn:hover {
  background: $skybluehover;
}

.skyblue-btn.respv {
  height: 38px;
  display: none;
  padding: 0px 30px;
}
.qrcode_canvas {
  background: #fff;
  padding-left: 8px;
  padding-top: 8px;
  padding-right: 8px;
  padding-bottom: 2px;
}
@media (max-width: 640px) {
  .skyblue-btn.respn {
    display: none;
  }

  .skyblue-btn.respv {
    display: flex;
  }
}
</style>
