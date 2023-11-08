<template>
  <div>
    <div class="bet-info--id fs20 fw500 p-t-15 p-b-15">ID #{{ gameLogId }}</div>
    <div class="bet-info--player fs14 m-t-10">
      PLAYER: <span class="fw600">{{ betPlayerName }}</span>
    </div>
    <div class="bet-info--game fs14 m-t-10">
      GAME: <span class="fw600">{{ betGameId }}</span>
    </div>
    <div class="bet-info--val fs14 m-t-10">
      BET: <span class="fw600">{{ betVal | toThousandFilter }} bits</span>
    </div>
    <!-- <div class="bet-info--payout fs17 m-t-10">
      PAYOUT: <span>{{ betPayout | toThousandFilter }}</span>
    </div> -->
    <div class="bet-info--bust fs14 m-t-10">
      BUSTED AT: <span class="fw600">{{ betBust }}</span>
    </div>
    <div class="bet-info--cash fs14 m-t-10">
      CASH OUT: <span class="fw600">{{ betCash }}</span>
    </div>
    <div class="bet-info--profit fs14 m-t-10">
      PROFIT: <span class="fw600">{{ betProfit | toThousandFilter }}</span>
    </div>
    <div class="bet-info--date fs14 m-t-10">
      DATE: <span class="fw600">{{ betDate | parseTime }}</span>
      <span class="ago fs13">{{ betDate | formatTime }}</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'BetInfoView',
  components: {},
  props: {},
  data() {
    return {
      betInfo: {
        user: '',
        GAME_ID: '',
        BET: '',
        payout: '',
        BUST: '',
        CASHOUT: '',
        PROFIT: '',
        CREATE_TIME: 0
      }
    }
  },
  computed: {
    gameLogId() {
      const gameLogId = this.$store.getters['app/getGameHistoryLogId']
      return gameLogId
    },
    betPlayerName() {
      return this.betInfo.USERNAME === undefined ? '' : this.betInfo.USERNAME
    },
    betGameId() {
      return this.betInfo.GAME_ID === undefined ? '' : this.betInfo.GAME_ID
    },
    betVal() {
      return this.betInfo.BET === undefined ? '' : this.betInfo.BET
    },
    // betPayout() {
    //   return this.betInfo.payout === undefined ? '' : this.betInfo.payout
    // },
    betBust() {
      return this.betInfo.BUST === undefined
        ? ''
        : (this.betInfo.BUST / 100).toFixed(2) + 'x'
    },
    betCash() {
      return this.betInfo.CASHOUT === undefined ? '' : this.betInfo.CASHOUT
    },
    betProfit() {
      return this.betInfo.PROFIT === undefined ? '' : this.betInfo.PROFIT
    },
    betDate() {
      return this.betInfo.CREATE_TIME === undefined
        ? ''
        : this.betInfo.CREATE_TIME
    }
  },
  mounted() {
    this.$bus.$emit('show-overlay')
    this.$axios
      .post('/Crash/betInfo', {
        id: this.gameLogId
      })
      .then((response) => {
        if (response.status === 'success') {
          this.betInfo =
            response.data === null || response.data.length === 0
              ? {}
              : response.data[0]
        }
        this.$bus.$emit('hide-overlay')
      })
      .catch((error) => {
        error = 0
        this.betInfo = {}
        this.$bus.$emit('hide-overlay')
      })
  },
  methods: {}
}
</script>
<style lang="scss">
@import '@/assets/styles/variables.scss';

.bet-info--id {
  letter-spacing: 3px;
  color: white;
  border-bottom: 1px solid $gray4a;
}
.bet-info--player,
.bet-info--game,
.bet-info--payout,
.bet-info--val,
.bet-info--bust,
.bet-info--cash,
.bet-info--profit,
.bet-info--date {
  color: #d6d8db;
}
.bet-info--player {
  span {
    color: $midorangeyellow;
  }
}
.bet-info--game {
  span {
    color: $midorangeyellow;
  }
}
.bet-info--payout {
  span {
    color: white;
  }
}
.bet-info--val {
  span {
    color: white;
  }
}
.bet-info--payout {
  span {
    color: white;
  }
}
.bet-info--bust {
  span {
    color: white;
  }
}
.bet-info--cash {
  span {
    color: $greenhover;
  }
}
.bet-info--profit {
  span {
    color: $greenhover;
  }
}
.bet-info--date {
  padding-bottom: 30px;
  span {
    color: white;
    &.ago {
      color: #d6d8db;
    }
  }
}
</style>
