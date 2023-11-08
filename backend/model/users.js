var db = require('../utils/db')
var commonModel = require('./common')

var getAdminUsers = function () {
    return db.list(db.statement('select * from', 'admins', '', '')).then(res => {
        return res.data
    }).catch(res => {
        return null
    })
}

var detailUsers = function (whereItems) {
    return db.list(db.statement('select * from', 'users', '', db.lineClause(whereItems))).then(res => {
        return res.data
    }).catch(res => {
        return null
    })
}
var detailBots = function (whereItems) {
    return db.list(db.statement('select * from', 'game_bots', '', db.lineClause(whereItems))).then(res => {
        return res.data
    }).catch(res => {
        return null
    })
}
var updateUsers = function (whereItems, setVals) {
    return db.cmd(db.statement('update', 'users', 'set ' + db.lineClause(setVals, ','), db.lineClause(whereItems, 'and')))
}
var getWalletInfo = function (userID, walletType) {
    return detailUsers([{
        key: 'ID',
        val: userID
    }]).then(userInfo => {
        if (userInfo == null) {
            return null
        } else {
            return commonModel.getGameDatabyCondition(
                'user_wallets', [{
                        key: 'WALLET_ID',
                        val: userInfo[0]['WALLET_ID']
                    }/*,
                    {
                        key: 'WALLET_TYPE',
                        val: walletType
                    }*/
                ]
            )
        }
    }).then(walletData => {
        if (walletData == null || walletData.length == 0) {
            return 0
        } else {
            return walletData.data[0]['WALLET']
        }
    })
}

var getTotalProfit = function (userID/*, walletType*/) {
    return db.list(db.statement('select sum(profit) as total_profit from', 'game_log', '', 'USER_ID = ' + userID, ''))
    .then(res => {
        if (res.success && res.data != null && res.data.length > 0) {
            return res.data[0].total_profit;
        }
        return 0;
    }).catch(res => {
        return 0;
    })
}

var getBettingData = function (userID/*, walletType*/) {
    return db.list(db.statement('select sum(BET) as totalBet, count(BET) as totalCount, max(PROFIT) as maxProfit, min(PROFIT) as minProfit from', 
        'game_log', '', 'USER_ID = ' + userID, ''))
    .then(res => {
        if (res.success && res.data != null && res.data.length > 0) {
            return res.data[0];
        }
        return {
            totalBet: 0,
            totalCount: 0,
            maxProfit: 0,
            minProfit: 0
        };
    }).catch(res => {
        return {
            totalBet: 0,
            totalCount: 0,
            maxProfit: 0,
            minProfit: 0
        };
    })
}

var getUserCount = function(tableName) {
    return db.list(db.statement('select count(ID) as userCount from',
        tableName, '', '', ''))
        .then(res => {
            if (res.success && res.data != null && res.data.length > 0) {
                return res.data[0].userCount;
            }
            return 0
        })
        .catch(res=> {
            return 0
        })
}

var getBotInfo = function (botID) {
    return db.list(db.statement('select * from', 'game_bots', '', 'ID = ' + botID)).then(res => {
        return res.data
    }).catch(res => {
        return null
    })
}


var updateWallet = function (updateData , callback) {
    var amount = parseInt(updateData.amount * Math.pow(10 , 6));
    var updateQuery = "UPDATE user_wallets SET `WALLET`=`WALLET`+" + amount + " WHERE WALLET_ID='" + updateData.wallet_id + "'";
    console.log(updateQuery)
    db.con.query(updateQuery, function (err, rows, fields) {
        if(err) {
            return callback(err, null)
        } else {
            var return_data = {
                res: true,
                content: rows
            }
            return callback(null, return_data);
        }
    })
}

var updateWalletData = function(wallet_id, amount, type) {
    var updateQuery = '';
    if(type == 1)
        updateQuery = "UPDATE user_wallets SET `WALLET`=`WALLET`+" + amount + " WHERE WALLET_ID='" + wallet_id + "'";
    else if(type == 2)
        updateQuery = "UPDATE user_wallets SET `WALLET`=`WALLET`-" + amount + " WHERE WALLET_ID='" + wallet_id + "'";

    return new Promise((resolve , reject) => {
        db.con.query(updateQuery , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                resolve(true);
            }
        });
    })
}

var getUserInfo = function (userid) {
    let sql = "SELECT users.*, user_wallets.`WALLET` \
                FROM users \
                LEFT JOIN user_wallets \
                ON users.`WALLET_ID` = user_wallets.`WALLET_ID` \
                WHERE users.ID = " + userid + " \
              ";
    return new Promise((resolve , reject) => {
        db.con.query(sql , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                result = JSON.stringify(result);
                result = JSON.parse(result);
                resolve(result);
            }
        });
    })
}

var requestWithdraw = function(wallet_id, amount) {
  let sql = "update user_wallets \
            set WALLET = WALLET - " + amount + ", \
                WALLET_TEMP = WALLET_TEMP + " + amount + " \
                where WALLET_ID='" + wallet_id + "'";
  return new Promise((resolve , reject) => {
      db.con.query(sql , function(err , result , fields) {
          if(err)
              reject(err)
          else {
              resolve(true);
          }
      });
  })
}

module.exports = {
    getAdminUsers,
    detailUsers,
    detailBots,
    updateUsers,
    getWalletInfo,
    getTotalProfit,
    getBettingData,
    getUserCount,
    getBotInfo,
    updateWallet,
    getUserInfo,
    requestWithdraw,
    updateWalletData
}