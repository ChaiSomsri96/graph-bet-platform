var db = require('../utils/db')
var utils = require('../utils/index')

let moment = require('moment');

var getGameHash = function (game, gameId) {
    return db.list(db.statement('select HASH from', game + "_game_hashes", '', db.itemClause('GAME_ID', gameId))).then(res => {
        if (res.success && res.data != null && res.data.length > 0) {
            return res.data[0]['HASH']
        }
        return ''
    }).catch(res => {
        throw res.data
    })
}

var rollFromHash = function (hashServerSeed) {
    var roll = hashServerSeed.substr(hashServerSeed.length - 8, 8);
    var num = parseInt(roll, 16);
    return Math.abs(num) % 15;
}

var getLastGameId = function (gameTableName) {
    let whereItems = [];
    whereItems.push({
        key: 'DEL_YN',
        val: 'N'
    })

    let ret = {
        code: 403,
        data: {
            gameId: 0
        }
    }
    return db.list(db.statement('select max(ID) as GAME_ID from', gameTableName, '', db.lineClause(whereItems, 'and'))).then(res => {
        if (res.success) {
            ret.code = 200
            ret.data.gameId = res.data == null || res.data.length == 0 ? 0 : res.data[0].gameId
        }
        return ret
    }).catch(res => {
        return ret
    })
}

var getLastGameData = function (gameTableName) {
    var whereItems = []
    whereItems.push({
        key: 'DEL_YN',
        val: 'N'
    })
    return db.list(db.statement('select * from', gameTableName, '', db.lineClause(whereItems, 'and'), 'order by CREATE_TIME desc limit 1'))
}

var getGameDatabyCondition = function (gameTableName, whereItems) {
    return db.list(db.statement('select * from', gameTableName, '', db.lineClause(whereItems, 'and')))
}

var updateDBbyGameID = function (gameTableName, gameId, setVals) {
    var whereItems = []
    whereItems.push({
        key: 'ID',
        val: gameId
    })
    return db.cmd(db.statement('update', gameTableName, 'set ' + db.lineClause(setVals, ','), db.lineClause(whereItems, 'and')))
}

var updateDBbyCondition = function (gameTableName, whereItems, setVals) {
    return db.cmd(db.statement('update', gameTableName, 'set ' + db.lineClause(setVals, ','), db.lineClause(whereItems, 'and')))
}

var deleteDBbyCondition = function (gameTableName, whereItems) {
    return db.list(db.statement('delete from', gameTableName, '', db.lineClause(whereItems, 'and')))
}

var insertToDB = function (gameTableName, items) {
    let setKeys = []
    let vals = []
    for (let i = 0; i < items.length; i++) {
        setKeys.push(items[i].key)
        if (typeof (items[i].val) == 'string') {
            vals.push("'" + items[i].val + "'")
        } else {
            vals.push(items[i].val)
        }
    }
    return db.cmd(db.statement('insert into', gameTableName, '(' + setKeys.join(',') + ')', '', 'values (' + vals.join(',') + ')'))
}

var getCrashHistory = function () {
    //let curDateStart = new Date();
    //let curDateEnd = new Date();
    //curDateStart.setHours(0, 0, 0, 0);
    //curDateEnd.setHours(23, 59, 59, 0);
    //let timeStart = moment(curDateStart) / 1000;
    //let timeEnd = moment(curDateEnd) / 1000;
    return db.list(db.statement('select GAME_ID, BUST from',
        'game_total',
        '',
        "BUST > 0 AND STATE = 'BUSTED'",
        'order by UPDATE_TIME desc limit 0, 50'))
}

var getBetHistory = function (nUserID) {
    // let curDateStart = new Date();
    // let curDateEnd = new Date();
    // curDateStart.setHours(0, 0, 0, 0);
    // curDateEnd.setHours(23, 59, 59, 0);
    // let timeStart = moment(curDateStart) / 1000;
    // let timeEnd = moment(curDateEnd) / 1000;
    return db.list(db.statement('select GAME_ID, BET, CASHOUT, PROFIT from',
        'game_log',
        '',
        'USER_ID = ' + nUserID +
        ' AND BOT_YN = \'N\'',
        'order by UPDATE_TIME desc'))
}

module.exports = {
    getGameHash,
    rollFromHash,
    getLastGameId,
    getLastGameData,
    getGameDatabyCondition,
    insertToDB,
    deleteDBbyCondition,
    updateDBbyGameID,
    updateDBbyCondition,
    getCrashHistory,
    getBetHistory
}