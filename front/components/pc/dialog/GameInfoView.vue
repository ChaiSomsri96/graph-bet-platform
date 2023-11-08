<template>
  <div>
    <div class="flex-space-between-vc m-t-10">
      <b-button :disabled="prevGameDisable" color="success" @click="onPrev()">
        Prev Game
      </b-button>
      <b-button :disabled="nextGameDisable" color="success" @click="onNext()">
        Next Game
      </b-button>
    </div>
    <div class="game-info--id fs20 fw500 p-t-15 p-b-15">
      GAME #{{ curGameID }}
    </div>
    <div class="game-info--bust fs14 m-t-10">
      BUSTED AT: <span class="fw600">{{ bustValue }}</span>
    </div>
    <div class="game-info--date fs14">
      DATE:
      <span class="fw600">{{ bustTime | parseTime }}</span>
      <span class="ago fs13">{{ bustTime | formatTime }}</span>
    </div>
    <div class="game-info--list m-t-20 m-b-10">
      <div class="game-info--players fs17">Players</div>
      <user-list
        :user-infos="histories"
        :mode="1"
        :is-crashed="true"
        class="m-t-10"
      />
    </div>
  </div>
</template>
<script>
import UserList from '@/components/pc/main/UserList'

export default {
  name: 'GameInfoView',
  components: {
    UserList
  },
  props: {},
  data() {
    return {
      prevGameDisable: true,
      nextGameDisable: true,
      histories: [],
      curGameID: 0,
      prevGameID: 0,
      bustValue: 0,
      currentGameId: 0,
      bustTime: 0
    }
  },
  computed: {
    gameId() {
      const gameId = this.$store.getters['app/getGameHistoryId']
      return gameId
    },
    bust() {
      const gameBust = this.$store.getters['app/getGameHistoryBust']
      if (gameBust === 0) {
        return '-'
      }
      return Math.floor(gameBust / 100) + '.' + (gameBust % 100) + 'x'
    }
  },
  mounted() {
    this.curGameID = this.gameId
    this.prevGameID = this.curGameID
    this.refreshGameInfo()
  },
  methods: {
    onPrev() {
      this.prevGameID = this.curGameID
      this.curGameID--
      this.refreshGameInfo()
    },
    onNext() {
      this.prevGameID = this.curGameID
      this.curGameID++
      this.refreshGameInfo()
    },
    refreshGameInfo() {
      this.$bus.$emit('show-overlay')
      this.$axios
        .post('/Crash/gameInfo', {
          id: this.curGameID
        })
        .then((response) => {
          if (response.status === 'success') {
            if (response.data === null || response.data.length === 0) {
              this.curGameID = this.prevGameID
            } else {
              this.histories = response.data
              this.bustValue = (parseInt(response.bust) / 100).toFixed(2) + 'x'
              this.bustTime = parseInt(response.bust_time)
              this.currentGameId = response.cur_game_id
              this.nextGameDisable = this.curGameID + 1 >= this.currentGameId
              this.prevGameDisable = this.curGameID - 1 < 0

              this.histories.sort(function(itemA, itemB) {
                if (itemA.cashout !== 0 && itemB.cashout !== 0) {
                  if (itemA.cashout < itemB.cashout) return 1
                  else if (itemA.cashout > itemB.cashout) return -1
                  else return 0
                } else if (itemA.cashout === 0 && itemB.cashout !== 0) return 1
                else if (itemA.cashout !== 0 && itemB.cashout === 0) return -1
                else if (itemA.value < itemB.value) return 1
                else if (itemA.value > itemB.value) return -1
                else return 0
              })
            }
          }
          this.$bus.$emit('hide-overlay')
        })
        .catch((error) => {
          error = 0
          this.histories = {}
          this.$bus.$emit('hide-overlay')
        })
    }
  }
}
</script>
<style lang="scss">
@import '@/assets/styles/variables.scss';

.game-info--id {
  color: white;
  border-bottom: 1px solid $gray4a;
  letter-spacing: 3px;
}
.game-info--bust {
  color: #d6d8db;
  span {
    color: white;
  }
  letter-spacing: 2px;
}
.game-info--date {
  color: #d6d8db;
  span {
    color: white;
    &.ago {
      color: #d6d8db;
    }
  }
}
.game-info--players {
  color: white;
}
</style>
