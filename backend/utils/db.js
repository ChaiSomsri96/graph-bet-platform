var mysql = require('mysql');
var config = require('../config')

//database handle
var con;

con = mysql.createPool({
    host : config.mysql,
    user : config.mysql_user,
    password : config.mysql_pwd,
    database : config.mysql_db
});
//-
//- Establish a new connection
//-
con.getConnection(function(err){
    if(err) {
        console.log("\n\t *** Cannot establish a connection with the database. ***");
        handleDisconnect();
    }else {
        console.log("\n\t *** New connection established with the database. ***")
    }
});
//-
//- Reconnection function
//-
function handleDisconnect(){
    console.log("\n New connection tentative...");

    //- Create a new one
    con = mysql.createPool({
        host : config.mysql,
        user : config.mysql_user,
        password : config.mysql_pwd,
        database : config.mysql_db
    });

    //- Try to reconnect
    con.getConnection(function(err){
        if(err) {
            //- Try to connect every 2 seconds.
            setTimeout(handleDisconnect, 2000);
        }else {
            console.log("\n\t *** New connection established with the database. ***")
        }
    });
}
//-
//- Error listener
//-
con.on('error', function(err) {
    //-
    //- The server close the connection.
    //-
    if(err.code === "PROTOCOL_CONNECTION_LOST"){    
        console.log("/!\\ Cannot establish a connection with the database. /!\\ ("+err.code+")");
        return handleDisconnect();
    }
    else if(err.code === "PROTOCOL_ENQUEUE_AFTER_QUIT"){
        console.log("/!\\ Cannot establish a connection with the database. /!\\ ("+err.code+")");
        return handleDisconnect();
    }
    else if(err.code === "PROTOCOL_ENQUEUE_AFTER_FATAL_ERROR"){
        console.log("/!\\ Cannot establish a connection with the database. /!\\ ("+err.code+")");
        return handleDisconnect();
    }
    else if(err.code === "PROTOCOL_ENQUEUE_HANDSHAKE_TWICE"){
        console.log("/!\\ Cannot establish a connection with the database. /!\\ ("+err.code+")");
    }
    else{
        console.log("/!\\ Cannot establish a connection with the database. /!\\ ("+err.code+")");
        return handleDisconnect();
    }
});
////////////////////////////

var itemClause = function (key, val, opt = '') {
    if (typeof (val) == 'string') {
        if (val != '')
            return key + " " + (opt === '' ? "=" : opt) + " " + "'" + val + "'"
        else
            return key
    }
    return key + " " + (opt === '' ? "=" : opt) + " " + val
}
var extraClause = function (extra_option, val) {
    return extra_option + " " + val
}
var lineClause = function (items, delimiter) {
    var ret = ''
    for (var i = 0; i < items.length; i++) {
        ret += itemClause(items[i].key, items[i].val, items[i].opt === undefined || items[i].opt == null ? '' : items[i].opt)
        if (i != items.length - 1) {
            ret += ' ' + delimiter + ' '
        }
    }
    return ret
}

var extraLineClause = function (items, delimiter) {
    var ret = ''
    for (var i = 0; i < items.length; i++) {
        ret += extraClause(items[i].extra_option, items[i].val)
        if (i != items.length - 1) {
            ret += ' ' + delimiter + ' '
        }
    }
    return ret
}

var statement = function (cmd, tbl_name, set_c, where_c, extra = '') {
    // console.log("where_c: " + where_c)
    // console.log("extra: " + extra)
    return cmd + " " + tbl_name + " " + (set_c == undefined || set_c == '' ? '' : set_c) + (where_c == undefined || where_c == '' ? '' : ' where ' + where_c) + (extra == undefined || extra == '' ? '' : ' ' + extra)
}
var cmd = function (statement, shouldWait = false) {
    return new Promise((resolve, reject) => {
        console.log(statement)
        con.query(statement, function (err, rows, fields) {
            if (err) {
                reject({
                    success: false,
                    data: err
                })
                throw err
            }
            resolve({
                success: true,
                data: rows
            })
        });
    })
}
var list = function (statement, shouldWait = false) {
    return new Promise((resolve, reject) => {
        console.log(statement)
        con.query(statement, function (err, rows, fields) {
            if (err) {
                reject({
                    success: false,
                    data: err
                })
                throw err
            }
            resolve({
                success: true,
                data: rows
            })
        });
    })
}
var convFloat = function (val) {
    return val == undefined || val == null || isNaN(parseFloat(val)) ? 0 : parseFloat(val)
}

var convInt = function (val) {
    return val == undefined || val == null || isNaN(parseInt(val)) ? 0 : parseInt(val)
}

var escape = function(val) {
    return mysql.escape(val)
}

module.exports = {
    con,
    itemClause,
    lineClause,
    extraClause,
    extraLineClause,
    statement,
    cmd,
    list,
    convFloat,
    convInt,
    escape
}