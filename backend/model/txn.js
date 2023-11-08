var db = require('../utils/db')
exports.insertData = function(data) {
    var timestamp = Math.floor(Date.now() / 1000);
    var insertQuery =
    "INSERT INTO deposit_withdraw_log ( " +
    " `WALLET_ID`, " +
    "`TYPE`, " +
    "`AMOUNT_BTC`, " +
    "`AMOUNT_BITS`, " +
    "`FEE`, " +
    "`DETAIL`, " +
    "`TXHASH`, " +
    "`STATUS`, " +
    "`PRE_W`, " +
    "`CREATE_TIME`, " +
    "`UPDATE_TIME`) " +
    "VALUES ('" +
    data.wallet_id + "', '" +
    data.type + "', '" +
    data.amount + "', '" +
    data.amount_bits + "', '" +
    data.fees + "', '" +
    data.detail + "', '" +
    data.txhash + "', " + data.status + " , " +
    data.pre_w + ", " +
    timestamp + ", " +
    timestamp + ")";

    return new Promise((resolve, reject) => {
        db.con.query(insertQuery, function (err, result) {
            if (err)
                reject(err)
            else {
                resolve(result.insertId);
            }
        });
    })
}
exports.insertTxn = function (insertData, callback) {
    var timestamp = Math.floor(Date.now() / 1000);
    var amount_coins = parseInt(Math.pow(10 , 6) * insertData.amount);
    var insertQuery =
    "INSERT INTO deposit_withdraw_log ( " +
    " `WALLET_ID`, " +
    "`TYPE`, " +
    "`AMOUNT_BTC`, " +
    "`AMOUNT_BITS`, " +
    "`FEE`, " +
    "`DETAIL`, " +
    "`TXHASH`, " +
    "`STATUS`, " +
    "`PRE_W`, " +
    "`CREATE_TIME`, " +
    "`UPDATE_TIME`) " +
    "VALUES ('" +
    insertData.wallet_id + "', '" +
    insertData.type + "', '" +
    insertData.amount + "', '" +
    amount_coins + "', '" +
    insertData.fees + "', '" +
    insertData.detail + "', '" +
    insertData.txhash + "', 1 , " +
    insertData.pre_w + ", " +
    timestamp + ", " +
    timestamp + ")";

    db.con.query(insertQuery, function (err, rows, fields) {
        if (err) {
            return callback(err, null);
        }
        var return_data = {
            res: true,
            content: rows
        }
        return callback(null, return_data);
    });
}