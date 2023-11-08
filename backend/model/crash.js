var db = require('../utils/db')

function nextGameID() {
  return db.list(db.statement('select max(GAME_ID) as GAMEID from', 'game_total', '', '')).then(res => {
    if (res.success && res.data != null && res.data.length > 0) {
        return res.data[0].GAMEID + 1;
    }
    return 1;
  }).catch(res => {
      throw res.data;
  })
}

function getGameHash(gameid) {
  var whereItems = []
  whereItems.push(
    {
      key: 'GAME_ID',
      val: gameid
    }
  );
  return db.list(db.statement('select HASH as hash from', 'game_hashes', '', db.lineClause(whereItems, 'and'))).then(res => {
    if (res.success && res.data != null && res.data.length > 0) {
        return res.data[0].hash;
    }
    return 1;
  }).catch(res => {
      throw res.data;
  })
}

var getCrashGameProfit = function (gameID) {
  var whereItems = []
  whereItems.push(
    {
      key: 'GAME_ID',
      val: gameID
    },
    {
      key: 'BOT_YN',
      val: 'N'
    }
  );
  return db.list(db.statement('select sum(PROFIT) as total_profit from', 'game_log', '', db.lineClause(whereItems, 'and'))).then(res => {
      if (res.success && res.data != null && res.data.length > 0) {
          return res.data[0].total_profit;
      }
      return 0;
  }).catch(res => {
      return 0;
  })
}

module.exports = {
  nextGameID,
  getGameHash,
  getCrashGameProfit
}