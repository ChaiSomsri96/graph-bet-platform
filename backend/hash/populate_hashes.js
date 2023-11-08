var async = require('async');
var _ = require('lodash');
var engine = require('./hash_lib');
var games = 2e6; // you might want to make this 10M for a prod setting.
var offset = 1;
var game = games;

//roulette var serverSeed = `x2bet_tarobet_roulette_nobody_does_not_know_chain_${new Date().toString('yyyy_MM_DD')}`;
//ladder   var serverSeed = `x2bet_tarobet_ladder_nobody_does_not_know_chain_${new Date().toString('yyyy_MM_DD')}`;
//jackpot  var serverSeed = `x2bet_tarobet_jackpot_nobody_does_not_know_chain_${new Date().toString('yyyy_MM_DD')}`;  
//crash    var serverSeed = `x2bet_tarobet_crash_nobody_does_not_know_chain_${new Date().toString('yyyy_MM_DD')}`;

var serverSeed = `quickbit_bustabit_game_nobody_does_not_know_chain_${new Date().toString('yyyy_MM_DD')}`;

function loop(cb) {
    var parallel = Math.min(game, 1000);

    var inserts = _.range(parallel).map(function () {
        return function (cb) {
            game--;
            serverSeed = engine.genGameHash(serverSeed, offset + game);
            engine.insertHash(offset + game, serverSeed, cb);
        }
    });

    async.parallel(inserts, function (err) {
        if (err) throw err;
        var pct = 100 * (games - game) / games;
        process.stdout.clearLine();
        process.stdout.cursorTo(0);
        process.stdout.write(
            "Processed: " + (games - game) + ' / ' + games +
            ' (' + pct.toFixed(2) + '%)');

        if (game > 0)
            loop(cb);
        else {
            console.log(' Done');
            cb();
        }
    });

}

loop(function () {
    console.log('Finished with serverseed: ', serverSeed);
});