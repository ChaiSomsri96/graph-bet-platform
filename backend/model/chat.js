var db = require('../utils/db')
let moment = require('moment');

var add = function (data) {
    let items = []
    items.push("'" + data.curTime + "'")
    items.push("'" + data.curTime + "'")
    items.push(data.userId)
    items.push(db.escape(data.msg))
    items.push("'N'")
    var statement = db.statement("insert into", "chats", "(CREATE_TIME, UPDATE_TIME, USER_ID, MSG, DEL_YN)", "",
        "VALUES (" + items.join(',') + ")")
    db.cmd(statement)
}

var list = function () {
    let timeRange = []
    // 1 day before (24 hours)
    timeRange.push(Math.floor(moment(new Date()) / 1000) - 60 * 60 * 24)
    timeRange.push(Math.floor(moment(new Date()) / 1000) + 60 * 60 * 24)

    return db.list(db.statement("select chats.ID as id, chats.USER_ID as user_id, chats.CREATE_TIME, chats.MSG as message, users.USERNAME as username from", "chats",
        'LEFT JOIN users ON users.ID = chats.USER_ID',
        db.itemClause("chats.DEL_YN", "N") + " AND " + "chats.CREATE_TIME BETWEEN " + timeRange.join(' AND '), ""), true).then((chatItems) => {
        return chatItems
    })
}

module.exports = {
    add,
    list
}