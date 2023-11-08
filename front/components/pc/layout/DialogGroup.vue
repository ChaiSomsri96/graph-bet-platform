<template>
  <div>
    <tabbed-dialog title="LOGIN" dialog-id="login">
      <div slot="tab-content-box-0">
        <login-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog
      title="ACCOUNT"
      dialog-id="profile"
      :ary-tab-name="aryAccountBtn"
    >
      <div slot="tab-content-box-0">
        <account-overview />
      </div>
      <div slot="tab-content-box-1">
        <account-stats-view />
      </div>
      <div slot="tab-content-box-2">
        <account-security-view />
      </div>
      <div slot="tab-content-box-3">
        <account-settings-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog
      title="CASHIER"
      dialog-id="cashier"
      :ary-tab-name="aryCashierBtn"
    >
      <div slot="tab-content-box-0">
        <cashier-deposits-view />
      </div>
      <div slot="tab-content-box-1">
        <cashier-withdrawals-view />
      </div>
      <div slot="tab-content-box-2">
        <cashier-tips-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="STATISTICS" dialog-id="statistics">
      <div slot="tab-content-box-0">
        <statistics-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="LEADERBOARD" dialog-id="leaderboard">
      <div slot="tab-content-box-0">
        <leaderboard-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="REGISTER" dialog-id="register">
      <div slot="tab-content-box-0">
        <register-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="GAME INFORMATION" dialog-id="gameinfo">
      <div slot="tab-content-box-0">
        <game-info-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="BET INFORMATION" dialog-id="betinfo">
      <div slot="tab-content-box-0">
        <bet-info-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog
      title="CONFIRM PASSWORD"
      dialog-id="confirmpwd"
      modal-class="sm"
    >
      <div slot="tab-content-box-0">
        <confirm-pwd-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="FEQUENTLY ASKED QUESTION" dialog-id="faq">
      <div slot="tab-content-box-0">
        <faq-view />
      </div>
    </tabbed-dialog>
    <tabbed-dialog title="SUPPORT" dialog-id="support">
      <div slot="tab-content-box-0">
        <support-view />
      </div>
    </tabbed-dialog>
  </div>
</template>
<script>
import TabbedDialog from '@/components/pc/dialog/TabbedDialog.vue'
import LoginView from '@/components/pc/dialog/LoginView.vue'
import AccountOverview from '@/components/pc/dialog/AccountOverview.vue'
import AccountStatsView from '@/components/pc/dialog/AccountStatsView.vue'
import AccountSecurityView from '@/components/pc/dialog/AccountSecurityView.vue'
import AccountSettingsView from '@/components/pc/dialog/AccountSettingsView.vue'
import CashierDepositsView from '@/components/pc/dialog/CashierDepositsView.vue'
import CashierWithdrawalsView from '@/components/pc/dialog/CashierWithdrawalsView.vue'
import CashierTipsView from '@/components/pc/dialog/CashierTipsView.vue'
import RegisterView from '@/components/pc/dialog/RegisterView.vue'
import StatisticsView from '@/components/pc/dialog/StatisticsView.vue'
import LeaderboardView from '@/components/pc/dialog/LeaderboardView.vue'
import GameInfoView from '@/components/pc/dialog/GameInfoView.vue'
import BetInfoView from '@/components/pc/dialog/BetInfoView.vue'
import ConfirmPwdView from '@/components/pc/dialog/ConfirmPwdView.vue'
import FaqView from '@/components/pc/dialog/FaqView.vue'
import SupportView from '@/components/pc/dialog/SupportView.vue'
export default {
  name: 'DialogGroup',
  components: {
    TabbedDialog,
    LoginView,
    AccountOverview,
    AccountStatsView,
    AccountSecurityView,
    AccountSettingsView,
    CashierDepositsView,
    CashierWithdrawalsView,
    CashierTipsView,
    RegisterView,
    StatisticsView,
    LeaderboardView,
    GameInfoView,
    BetInfoView,
    ConfirmPwdView,
    FaqView,
    SupportView
  },
  data() {
    return {
      aryCashierBtn: ['DEPOSITS', 'WITHDRAWALS', 'TIPS'],
      aryAccountBtn: ['OVERVIEW', 'STATS', 'SECURITY', 'SETTINGS']
    }
  }
}
</script>
<style lang="scss" scoped></style>
