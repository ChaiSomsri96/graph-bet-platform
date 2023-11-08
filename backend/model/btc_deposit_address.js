var db = require('../utils/db')

exports.insertNewDepositAddress = function (insertData , callback) {
    var timestamp = Math.floor(Date.now() / 1000);
    var insertQuery =
    "INSERT INTO api_btc_deposit_address " +
    "(`USER_ID`, `INPUT_ADDRESS`, `INPUT_ADDRESS_ID`, `DESTINATION`, `CREATE_TIME`, `UPDATE_TIME`) " +
    "VALUES ('" + insertData.who + "', '" + insertData.input_address + "', '" + insertData.input_address_id + "', '" + insertData.destination + "', '" + timestamp + "', " + timestamp + ")";
    db.con.query(insertQuery, function(err, rows, fields) {
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

exports.getDepositAddressData = function (keyData, callback) {
    var query = "SELECT * FROM api_btc_deposit_address WHERE USER_ID=" + keyData.who;
    db.con.query(query, function (err, rows, fields) {
        if (err) {
            return callback(err, null);
        }else{
            var return_data = {
                res: true,
                content: rows[0],
                length: rows.length
            }
            return callback(null, return_data);
        }
    });
}
