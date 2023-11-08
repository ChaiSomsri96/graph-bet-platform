var db = require('../utils/db')
var list = function () {
    const whereItems = [{
        key: 'DEL_YN',
        val: 'N'
    }];
    return db.list(db.statement('select * from', 'settings', '', db.lineClause(whereItems, 'and')))
}
var calcCheck = function(user_id) {
    let sql = "SELECT CALC_YN, AFFILIATE_CODE_P \
	            FROM users \
	            WHERE ID = " + user_id + " \
	            AND DEL_YN = 'N' \
              ";
    return new Promise((resolve , reject) => {
        db.con.query(sql , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                result = JSON.stringify(result);
                result = JSON.parse(result);
                if(result.length < 1)
                    resolve(false);
                else {
                    if(result[0]['CALC_YN'] == 'Y')
                        resolve(true);
                    else if(result[0]['AFFILIATE_CODE_P'] == '' || result[0]['AFFILIATE_CODE_P'] == null)
                        resolve(true);
                    else
                        resolve(false);
                }
            }
        });
    })
}
var updateCheck = function(user_id) {
    let sql = "UPDATE users \
               SET CALC_YN = 'Y' \
               WHERE ID = " + user_id + " \
               AND DEL_YN = 'N' \
            ";
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
var calcTransSum = function(user_id) {
    let sql = "SELECT IFNULL(SUM(game_log.BET), 0) AS trans_sum \
                FROM game_log \
                WHERE DEL_YN = 'N' \
                AND USER_ID = " + user_id + " \
                AND BOT_YN = 'N'";
    return new Promise((resolve , reject) => {
        db.con.query(sql , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                result = JSON.stringify(result);
                result = JSON.parse(result);
                if(result.length < 1)
                    resolve(0);
                else {
                    resolve(result[0]['trans_sum']);
                }
            }
        });
    })
}
var calcFirstDepositSum = function(wallet_id) {
    let sql = "SELECT AMOUNT_BITS \
            FROM deposit_withdraw_log \
            WHERE TYPE = 1 \
            AND WALLET_ID = '" + wallet_id + "' \
            ORDER BY CREATE_TIME \
            LIMIT 0,1";

    return new Promise((resolve , reject) => {
        db.con.query(sql , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                result = JSON.stringify(result);
                result = JSON.parse(result);
                if(result.length < 1)
                    resolve(0);
                else {
                    resolve(result[0]['AMOUNT_BITS']);
                }
            }
        });
    })
}
var getSettingValue = function(variable) {
    let sql = "SELECT `VALUE` \
                FROM settings \
                WHERE VARIABLE = '" + variable + "'";
    return new Promise((resolve , reject) => {
        db.con.query(sql , function(err , result , fields) {
            if(err)
                reject(err)
            else {
                result = JSON.stringify(result);
                result = JSON.parse(result);
                if(result.length < 1)
                    resolve(0);
                else {
                    resolve(result[0]['VALUE']);
                }
            }
        });
    })
}
module.exports = {
    list,
    calcCheck,
    updateCheck,
    calcTransSum,
    calcFirstDepositSum,
    getSettingValue
}