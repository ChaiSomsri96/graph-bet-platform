var crypto = require('crypto');
var mysql = require('mysql');
var config = require('./config');
var con;

function handleDisconnect() 
{
  con = mysql.createConnection(config.mysql);
  con.connect(function(err){
    if(err){
      console.log('error when connecting to db:', err);
      setTimeout(handleDisconnect, 2000);
    }
  });
  con.on('error',  function(err){
    console.log('db error', err);
    if(err.code == 'PROTOCOL_CONNECTION_LOST'){
      handleDisconnect();
    }else{
      throw err;
    }
  });
}
handleDisconnect();

exports.connect = con;
var public_seed_crash = "828384856628";
exports.genGameHash = function(serverSeed , gameid) {
      return crypto.createHash('sha256').update(serverSeed + '-' + public_seed_crash + '-' + gameid).digest('hex');
}
exports.insertHash = function(game_id , hash , callback) {
    var table_name = 'game_hashes';
    con.query("INSERT INTO " + table_name + "(GAME_ID , HASH) VALUES(" + game_id + " , '" + hash + "')" , function (err, result) {
        if (err)
            return callback(err);
        callback(null , result);
    });
}
