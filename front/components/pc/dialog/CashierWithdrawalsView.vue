<template>
  <b-form class="flex-col-hc-vc fs14 fc-white p-t-35 p-b-25" @submit="onSubmit">
    <span>
      クレジットが確認されていないデポジットがあるか、現在のゲームに参加している場合、残高の一部を引き出すことができない場合があります。また、引き出しに掛かる所要時間は最低24時間となっておりますが、即時引き出しをご希望の場合はフォームしたにチェックをお願いします。（即時引き出しには別途手数料がかかります）
    </span>
    <input-with-label-box
      title="Bitcoin Address:"
      :is-round="false"
      :small-text="true"
      :is-required="true"
      class="m-t-25"
      @inputChanged="changeAddress"
    />
    <div class="input-set h-full m-t-15 m-b-15">
      <input-with-label-box
        title="出金金額:"
        :is-round="false"
        :small-text="true"
        :var-link="form.bitsTemp"
        :is-required="true"
        @inputChanged="changeBits"
      >
        <div slot="button-content">
          <bits-btc-button
            :mode="bettingValueIsBits"
            @modeChanged="bitsModeChanged"
          />
        </div>
      </input-with-label-box>
      <div class="skyblue-btn flex-row-hc-vc pointer" @click="setMax">
        <span class="fs14">MAX</span>
      </div>
    </div>
    <!--
    <div class="flex-row-hl-vc m-t-15 h-full">
      <input type="checkbox" class="m-r-5" />
      <span class="fs13">即時引き出しを行う（追加手数料44.67bits）</span>
    </div>
    -->
    <data-list :ary-data="aryWithdrawalsData" class="m-t-25" />
    <button3-d
      type="submit"
      title="手続きを完了する"
      class="m-t-25"
      :status="sendBtnStatus"
    />
  </b-form>
</template>
<script>
import InputWithLabelBox from '@/components/pc/dialog/InputWithLabelBox.vue'
import DataList from '@/components/pc/dialog/DataList.vue'
import BitsBtcButton from '@/components/pc/dialog/BitsBtcButton.vue'
import Button3D from '@/components/pc/common/Button3D.vue'
import GlobalMixin from '~/mixins/global'
export default {
  name: 'CashierWithdrawalsView',
  components: {
    InputWithLabelBox,
    DataList,
    BitsBtcButton,
    Button3D
  },
  mixins: [GlobalMixin],
  props: {},
  data() {
    return {
      bettingValueIsBits: true,
      sendBtnStatus: 'bet',
      form: {
        address: '',
        bits: '0'
      }
    }
  },
  computed: {
    wallet() {
      const wallet = this.$store.getters['user/getWalletInfo']
      return wallet !== undefined && wallet !== '' ? wallet : 0
    },
    aryWithdrawalsData() {
      return [
        {
          content: '引き出し可能残高',
          value: this.wallet
        },
        {
          content: '引き出す金額',
          value: isNaN(parseInt(this.form.bits)) ? 0 : parseInt(this.form.bits)
        },
        {
          content: '出金手数料',
          value: 0
        },
        {
          content: '未払いのデポジット料金',
          value: 0
        },
        {
          content: '即時引き出し手数料',
          value: 0
        },
        {
          content: 'トータル',
          value: isNaN(parseInt(this.form.bits)) ? 0 : parseInt(this.form.bits)
        }
      ]
    }
  },
  methods: {
    bitsModeChanged(isBits) {
      this.bettingValueIsBits = isBits
      this.form.bitsTemp =
        '' + this.form.bits / (this.bettingValueIsBits ? 1 : 1000000)
    },
    changeAddress(inputValue) {
      this.form.address = inputValue
    },
    changeBits(inputValue) {
      this.form.bitsTemp = inputValue
      this.form.bits =
        '' +
        Math.floor(
          parseFloat(inputValue) * (this.bettingValueIsBits ? 1 : 1000000)
        )
    },
    setMax() {
      this.form.bits = this.wallet
      this.form.bitsTemp =
        '' + this.form.bits / (this.bettingValueIsBits ? 1 : 1000000)
    },
    async onSubmit(evt) {
      evt.preventDefault()
      this.sendBtnStatus = 'disabled'
      this.$bus.$emit('show-overlay')
      try {
        const response = await this.$axios.post('/Bitcoin/withdraw_request', {
          to_address: this.form.address,
          amount: isNaN(parseInt(this.form.bits)) ? 0 : parseInt(this.form.bits)
        })
        if (response.status === 'success') {
          this.$store.dispatch('user/getWalletInfo')
          this.showToast('Success', '出金申請されました。', 'success')
          this.$bvModal.hide('cashier')
        } else {
          this.showToast('Error', response.data, 'error')
        }
        this.$bus.$emit('hide-overlay')
        this.sendBtnStatus = 'bet'
      } catch (err) {
        this.errors = null
        this.sendBtnStatus = 'bet'
      }
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
  height: 38px;
  background: $skyblue;
  padding: 0px 35px;
  margin-left: 20px;
}

.skyblue-btn:hover {
  background: $skybluehover;
}

.input-set {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
}

@media (max-width: 640px) {
  .input-set {
    flex-direction: column;
  }

  .skyblue-btn {
    margin-left: 0px;
    margin-top: 15px;
    margin-bottom: 15px;
    width: 100%;
  }
}
</style>
