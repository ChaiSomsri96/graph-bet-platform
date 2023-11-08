var express = require('express');
var app = express();
var router = express.Router();
let crypto = require('crypto');
let assert = require('assert');
var socketIO = require('socket.io');
var io = null;
var axios = require('axios')
var model = require('../model/crash');
var commonModel = require('../model/common');
var usersModel = require('../model/users');
let rn = require('random-number');
const verifyToken = require('../middleware/verify_token');
const verifyUser = require('../middleware/verify_user');
let moment = require('moment');
let nCrashGameID = 0, nNextCrashGameID;
let nElapsedTime, nPrevTickTime, nDoTickInterval, nStartTime, nResetGameTime;
let nGameStatus = 0, nCurTick = 100, nCrashTick = 0, nIntervalTime = 10;
let szGameHash = '';
let szClientSeed = '000000000000000007a9a31ff7f07463d91af6b5454241d5faf282e5e0fe1b3a';
let aryPlayer = [], aryCashOut = [];
let hCrashTimer;
var config = require('./../config');

var instance = axios.create({
    baseURL: config.main_host_url + ":" + config.server_port + "/",
    timeout: 5000
});

/* bustabit value */
let isUUIDv4 = function(uuid) {
    return (typeof uuid === 'string') && uuid.match(/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i);
};
let isInt = function(nVal) {
    return typeof nVal === "number" && isFinite(nVal) && nVal > -9007199254740992 && nVal < 9007199254740992 && Math.floor(nVal) === nVal;
};

let divisible = function(hash, mod) {
    // We will read in 4 hex at a time, but the first chunk might be a bit smaller
    // So ABCDEFGHIJ should be chunked like  AB CDEF GHIJ
    var val = 0;
    var o = hash.length % 4;
    for (var i = o > 0 ? o - 4 : 0; i < hash.length; i += 4) {
        val = ((val << 16) + parseInt(hash.substring(i, i+4), 16)) % mod;
    }
    return val === 0;
}

let genGameHash = function (szSeed) {
    return crypto.createHash('sha256').update(szSeed).digest('hex');
};

let crashPointFromHash = function(serverSeed) {
    var hash = crypto.createHmac('sha256', serverSeed).update(szClientSeed).digest('hex');
    // In 1 of 101 games the game crashes instantly.
    if (divisible(hash, 21))
        return 0;
    // Use the most significant 52-bit from the hash to calculate the crash point
    var h = parseInt(hash.slice(0,52/4),16);
    var e = Math.pow(2,52);
    return Math.floor((100 * e - h) / (e - h));
};
/*
function gameResult(seed, salt) {
    const nBits = 52; // number of most significant bits to use
    // 1. HMAC_SHA256(key=salt, message=seed)
    const hmac = crypto.createHmac("sha256", salt);
    hmac.update(seed);
    seed = hmac.digest("hex");

    // 2. r = 52 most significant bits
    seed = seed.slice(0, nBits / 4);
    const r = parseInt(seed, 16);

    // 3. X = r / 2^52
    let X = r / Math.pow(2, nBits); // uniformly distributed in [0; 1)

    // 4. X = 99 / (1-X)
    X = 99 / (1 - X);
    // 5. return max(trunc(X), 100)
    const result = Math.floor(X);
    return Math.max(1, result);
}*/

function generateBustValue(szHash) {
    //szHash = genGameHash(szHash);
    let nBust = Math.floor(crashPointFromHash(szHash));
    assert(isInt(nBust));
    return {
        "hash": szHash,
        "crash": nBust
    };
}
/* ------ */

let BotMaker = function () {
    let botData = null;
    let nBotID = 0;

    let addBots = async function () {
        let optProb = {
            min: parseInt(1),
            max: parseInt(100),
            integer: true
        };
        let optTick = {
            min: parseInt(100),
            max: parseInt(4000),
            integer: true
        }
        let cashRand = {
            min: parseInt(0),
            max: parseInt(1000),
            integer: true
        }
        let calcTick = {
            min: parseInt(1),
            max: parseInt(100)
        }
        let betValue = 0, nProb;
        let resQuery = await commonModel.getGameDatabyCondition('game_bots', [
            {
                key: 'DEL_YN',
                val: 'N'
            },
            {
                key: 'STATUS',
                val: 1
            }
        ])

        if (!resQuery.success && resQuery.data == null && resQuery.data.length == 0) return;
        botData = resQuery.data;
        let nCashOutValue = 0;
        let count = botData.length;
        nBotID = 0;

        let pros = [
            {
                'min': parseInt(1), 'max': parseInt(2), 'pros': parseFloat(85)
            },
            {
                'min': parseInt(2), 'max': parseInt(4), 'pros': parseFloat(10)
            },
            {
                'min': parseInt(4), 'max': parseInt(7), 'pros': parseFloat(4)
            },
            {
                'min': parseInt(7), 'max': parseInt(20), 'pros': parseFloat(0.5)
            },
            {
                'min': parseInt(20), 'max': parseInt(40), 'pros': parseFloat(0.4)
            },
            {
                'min': parseInt(40), 'max': parseInt(100), 'pros': parseFloat(0.1)
            }
        ];

        for (i = 0; i < count; i++) {
            setTimeout(function() {
                nProb = parseInt(rn(optProb));
                //take rate
                if (nProb <= botData[nBotID]['TAKE_RATE']) {
                    nProb = parseInt(rn(cashRand));
                    let min = 0; max = 0, before_value = 0;
                    before_value = 0;
                    for(let index = 0; index < pros.length; index ++) {
                        if(index > 0) {
                            before_value = before_value + parseFloat(pros[index-1]['pros']);
                        }
                        if(nProb > before_value * 10 && nProb <= (before_value + pros[index]['pros']) * 10) {
                            min = pros[index]['min'];
                            max = pros[index]['max'];
                            break;
                        }
                    }
                    if(min * 100 > nCrashTick) {
                        nCashOutValue = nCrashTick + 100;
                    }
                    else if(max * 100 < nCrashTick) {
                        nCashOutValue = parseFloat(rn(calcTick) * (max-min) / 100 + min).toFixed(2) * 100;
                    }
                    else {
                        nProb = parseInt(rn(optProb));
                        if (nProb <= botData[nBotID]['WIN_RATE'] && nCrashTick > 105) {
                            nCashOutValue = parseFloat(rn(calcTick) * ( parseFloat(nCrashTick/100).toFixed(2) - min ) / 100 + min).toFixed(2) * 100;
                        } else
                            nCashOutValue = nCrashTick + 100;
                    }
                    //bet value
                    betValue = botData[nBotID]['MAX_BET'];
                    if (betValue == 0) betValue = 10000;
                    if (botData[nBotID]['MIN_BET'] == 0) betValue -= 1000;
                    else betValue -= botData[nBotID]['MIN_BET'];
                    nProb = rn(optProb);
                    betValue = betValue * nProb / 100;
                    betValue = parseInt(betValue / 500) * 500 + botData[nBotID]['MIN_BET'];
                    let data = {
                        userid: botData[nBotID]['ID'],
                        value: betValue,
                        username: botData[nBotID]['NAME'],
                        cashout: nCashOutValue,
                        isBot: 'Y'
                    }
                    aryPlayer.push(data);
                    BotBet(data);
                }
                nBotID ++;
            }, rn(optTick));
        }
    };

    return {
        makeBots: function () {
            addBots();
        }
    }
}();

async function BotBet(data) {
    let formatted = new Date().getTime();
    formatted = Math.round(formatted / 1000);

    const nCashOutVal = data.cashout >= nCrashTick ? 0 : data.cashout;
    const nProfitVal = nCashOutVal ? parseInt(data.value * (nCashOutVal / 100 - 1) + 0.5) : -1 * data.value;
    const crashedYN = nCashOutVal ? 'N' : 'Y';

    await commonModel.insertToDB('game_log', [{
            key: 'GAME_ID',
            val: nCrashGameID
        },
        {
            key: 'USER_ID',
            val: data.userid
        },
        {
            key: 'CREATE_TIME',
            val: formatted
        },
        {
            key: 'UPDATE_TIME',
            val: formatted
        },
        {
            key: 'BOT_YN',
            val: 'Y'
        },
        {
            key: 'CASHOUT',
            val: nCashOutVal
        },
        {
            key: 'BET',
            val: data.value
        },
        {
            key: 'PROFIT',
            val: nProfitVal
        },
        {
            key: 'CRASHED_YN',
            val: crashedYN
        }
    ]);
    io.emit('onMessage', {
        code: 'Bet',
        userid: 0,
        value: data.value,
        username: data.username,
        cashout: data.cashout,
        isBot: 'Y'
    });
}

var initializeSocketIO = function (server) {
    io = socketIO(server)
    io.on('connection', async function (socket) {

        if (nCrashGameID == 0) initGame();
        else { // Send game status to client
            if (nGameStatus == 2) {
                socket.emit('onMessage', {
                    code: 'WaitGame',
                    current_users: aryPlayer,
                    game_id: nCrashGameID,
                    time_left: (nResetGameTime + 5000 - Date.now())
                })
            } else if(nGameStatus == 3) {
                socket.emit('onMessage', {
                    code: 'GameStart',
                    game_id: nCrashGameID,
                    tick: nCurTick
                });
            } else if (nGameStatus == 4) {
                socket.emit('onMessage', {
                    code: 'GameCrash',
                    crash: nCrashTick
                });
            }
        }
    });
}

async function initGame() {

    let gameData = await commonModel.getGameDatabyCondition('game_total', [{
        key: 'STATE',
        val: 'STARTED'
    }]);

    if (gameData.success && gameData.data != null && gameData.data.length != 0)
        bustGame(gameData.data[0], 100);
    
    gameData = await commonModel.getGameDatabyCondition('game_total', [{
        key: 'STATE',
        val: 'WAITING'
    }]);

    if (gameData.success && gameData.data != null && gameData.data.length != 0) {
        nNextCrashGameID = parseInt(gameData.data[0]['GAME_ID'])
    } else {
        // call next game and return jon
        nNextCrashGameID = await model.nextGameID();
        nCrashGameID = nNextCrashGameID;

        let dt = moment(new Date()) / 1000;
        await commonModel.insertToDB('game_total', [{
                key: 'GAME_ID',
                val: nCrashGameID
            },
            {
                key: 'CREATE_TIME',
                val: dt
            },
            {
                key: 'UPDATE_TIME',
                val: dt
            },
            {
                key: 'STATE',
                val: 'WAITING'
            }
        ]);
    }

    waitGame();
}

async function waitGame() {

    // start game after 5 seconds
    nResetGameTime = Date.now();
    nGameStatus = 2;
    nCrashGameID = nNextCrashGameID;
    nNextCrashGameID = 0;
    nCurTick = 100; // start value
    szGameHash = await model.getGameHash(nCrashGameID);
    let crashInfo = generateBustValue(szGameHash);
    nCrashTick = crashInfo['crash'];
    szGameHash = crashInfo['hash'];

    BotMaker.makeBots();

    io.emit('onMessage', {
        code: 'WaitGame',
        game_id: nCrashGameID,
        time_left: 5000
    });

    console.log('Wait game - (GameID: ' + nCrashGameID + ")");
    setTimeout(function () {
        startGame();
    }, 5000);
}

function startGame() {
    // when game starts, we calc crash value first

    if (nCrashTick <= 100) {
        // we don't need to start game, because it finishes when it starts
        // we make exception for this case
        console.log('Crash Game Finished with Start!');

        gameFinishStart();
        return;
    }

    console.log('Crash Game Start: ', nCrashGameID, nCrashTick);

    gameStart(nCrashGameID, nCrashTick);
}

async function gameStart(nGameID, bustValue) {

    const gameData = await commonModel.getGameDatabyCondition(
        'game_total',
        [
            {
                key: 'GAME_ID',
                val: nGameID
            },
            {
                key: 'STATE',
                val: 'WAITING'
            }
        ]
    );
    if (!gameData.success || gameData.data == null || gameData.data.length == 0) {
        return 0;
    }

    let gameInfo = gameData.data[0];
    console.log("Started Game ID: ", gameInfo['ID']);
    if (gameInfo['STATE'] != 'WAITING')
        return 0;

    // get started next game no
    let dt = moment(new Date()) / 1000;
    await commonModel.updateDBbyGameID('game_total',
        gameInfo['ID'], [{
                key: 'STATE',
                val: 'STARTED'
            },
            {
                key: 'BUST',
                val: bustValue
            },
            {
                key: 'HASH',
                val: szGameHash
            },
            {
                key: 'START_TIME',
                val: dt
            },
            {
                key: 'UPDATE_TIME',
                val: dt
            }
        ]
    );

    nNextCrashGameID = await model.nextGameID();
    await commonModel.insertToDB('game_total', [{
            key: 'GAME_ID',
            val: nNextCrashGameID
        },
        {
            key: 'CREATE_TIME',
            val: dt
        },
        {
            key: 'UPDATE_TIME',
            val: dt
        },
        {
            key: 'STATE',
            val: 'WAITING'
        }
    ]);

    if (nNextCrashGameID) {
        aryCashOut = [];        // clear cashout list
        nLaunchTime = Date.now();
        nGameStatus = 3;
        nPrevTickTime = 0;
        nDoTickInterval = 0;

        setTimeout(function () {
            nElapsedTime = 0;
            nStartTime = Date.now();
            nResetGameTime = 0;
            hCrashTimer = setInterval(timerProc, nIntervalTime);
        }, 200);

        io.emit('onMessage', {
            code: 'GameStart',
            game_id: nCrashGameID,
            next_game_id: nNextCrashGameID,
            tick: 100, // we send 100 for tick
        });
    }
}

function timerProc() {

    nElapsedTime = Date.now() - nStartTime;
    nCurTick = Math.floor(100 * Math.pow(Math.E, 0.00006 * nElapsedTime));
    if (nElapsedTime - nPrevTickTime > nDoTickInterval) {
        io.emit('onMessage', {
            code: 'Tick',
            tick: nCurTick
        })
        nPrevTickTime = nElapsedTime;
        if (nDoTickInterval < 500) nDoTickInterval = 500
    }

    // check for bot to be cashed out
    for (let i = 0; i < aryPlayer.length; i += 1) {
        if (aryPlayer[i].isBot == 'N')
            continue;
        if (aryPlayer[i].cashout <= nCurTick) {
            let playerCashOut = aryPlayer[i];

            io.emit('onMessage', {
                code: 'CashOut',
                userid: playerCashOut.userid,
                username: playerCashOut.username,
                cashout: playerCashOut.cashout,
                value: playerCashOut.value,
                isBot: 'Y'
            });

            aryCashOut.push(playerCashOut);
            aryPlayer.splice(i, 1);
            i -= 1;
        }
    }

    let nAfterTick = Math.floor(100 * Math.pow(Math.E, 0.00006 * (nElapsedTime + 200)));

    if (nAfterTick >= nCrashTick) {
        clearInterval(hCrashTimer);
        nGameStatus = 4; // game crashed
        gameBust(nCrashGameID, nCrashTick);
        aryPlayer = [];
        aryCashOut = [];
        nElapsedTime = 0;

        setTimeout(function () {
            waitGame();
        }, 3000);
        
        io.emit('onMessage', {
            code: 'GameCrash',
            crash: nCrashTick
        });
    }
}

async function bustGame(gameInfo, nBustTick) {
    // bust game
    // set game busted ---> because it has bust already
    let dt = moment(new Date()) / 1000;
    const gameData = await commonModel.getGameDatabyCondition('game_log', [{
            key: 'GAME_ID',
            val: gameInfo['GAME_ID']
        }
    ])
    let bustedBets = gameData.data;
    // set profit
    await commonModel.updateDBbyCondition('game_log', [{
            key: 'GAME_ID',
            val: gameInfo['GAME_ID']
        },
        {
            key: 'CASHOUT',
            val: 0
        }
    ], [
        {
            key: 'PROFIT = -BET',
            val: ''
        },
        {
            key: 'CRASHED_YN',
            val: 'Y'
        },
        {
            key: 'UPDATE_TIME',
            val: dt
        }
    ])

    let nTotalBet = 0;
    let nTotalRealBet = 0;
    let nUserCnt = 0;
    let nBotCnt = 0;

    if (bustedBets && bustedBets.length > 0) {
        let bustBet, nWallet;
        for (let i = 0; i < bustedBets.length; i++) {
            nTotalBet += bustedBets[i]['BET'];
            if (bustedBets[i]['BOT_YN'] == 'N') {
                nTotalRealBet += bustedBets[i]['BET'];
                nUserCnt ++;
            } else nBotCnt ++;
        }
        if(nUserCnt > 0)
            await updateWalletInfo(0, 1, 0, nTotalRealBet, 3, gameInfo['GAME_ID']);
    }

    const profit = await model.getCrashGameProfit(gameInfo['GAME_ID']);

    await commonModel.updateDBbyGameID('game_total',
        gameInfo['ID'], [
            {
                key: 'PROFIT',
                val: -1 * profit
            },
            {
                key: 'TOTAL',
                val: nTotalBet
            },
            {
                key: 'TOTAL_REAL',
                val: nTotalRealBet
            },
            {
                key: 'USERS',
                val: nUserCnt
            },
            {
                key: 'BOTS',
                val: nBotCnt
            },
            {
                key: 'STATE',
                val: 'BUSTED'
            },
            {
                key: 'BUST_TIME',
                val: dt
            },
            {
                key: 'UPDATE_TIME',
                val: dt
            }
        ]
    )

    io.emit('onMessage', {
        code: 'CrashUpdate',
        game_id: gameInfo['ID'],
        bust: nBustTick
    });
}
// type = 1 ; bet
// type = 2 ; cashout
// type = 3 ; bust game
async function updateWalletInfo(userID, walletType, walletValue, amount, type, gameId = 0) {
    let dt = moment(new Date()) / 1000;
    let userInfo , userInfo_1;
    let walletID;

    if (type != 3) {
        userInfo = await usersModel.detailUsers([{
            key: 'ID',
            val: userID
        }])

        if (userInfo == null || userInfo.length == 0) return;

        walletID = userInfo[0]['WALLET_ID'];

        await commonModel.updateDBbyCondition(
            'user_wallets',
            [
                {
                    key: 'WALLET_ID',
                    val: walletID
                }
            ],
            [
                {
                    key: 'WALLET',
                    val: walletValue
                },
                {
                    key: 'UPDATE_TIME',
                    val: dt
                }
            ]
        );
        userInfo_1 = userInfo;
    }
 // get wallet of admin , wallet temp of admin
    userInfo = await usersModel.getAdminUsers();
    if (userInfo == null || userInfo.length == 0) return;
    let fromID, toID, fromPreW, toPreW;
    let walletInfo = await commonModel.getGameDatabyCondition('user_wallets', [{
        key: 'WALLET_ID',
        val: userInfo[0]['WALLET_ID']
    }]);

    if (walletInfo.data == null || walletInfo.data.length == 0) return
    let nWallet = walletInfo.data[0]['WALLET'];

    walletInfo = await commonModel.getGameDatabyCondition('user_wallets', [{
        key: 'WALLET_ID',
        val: userInfo[0]['WALLET_TEMP_ID']
    }]);

    if (walletInfo.data == null || walletInfo.data.length == 0) return
    let nWalletTemp = walletInfo.data[0]['WALLET'];
//
    if (type == 1) {
        fromID = walletID;
        toID = userInfo[0]['WALLET_TEMP_ID'];
        fromPreW = parseInt(walletValue) + parseInt(amount);
        toPreW = nWalletTemp;
        await commonModel.updateDBbyCondition(
            'user_wallets',
            [
                {
                    key: 'WALLET_ID',
                    val: toID
                }
            ],
            [
                {
                    key: 'WALLET',
                    val: parseInt(nWalletTemp) + parseInt(amount)
                },
                {
                    key: 'UPDATE_TIME',
                    val: dt
                }
            ]
        );
    } else if (type == 2) {
        fromID = userInfo[0]['WALLET_ID'];
        fromPreW = nWallet;
        toID = walletID;
        toPreW = parseInt(walletValue) - parseInt(amount);
        await commonModel.updateDBbyCondition(
            'user_wallets',
            [
                {
                    key: 'WALLET_ID',
                    val: fromID
                }
            ],
            [
                {
                    key: 'WALLET',
                    val: parseInt(nWallet) - parseInt(amount)
                },
                {
                    key: 'UPDATE_TIME',
                    val: dt
                }
            ]
        );
    } else if (type == 3) {
        fromID = userInfo[0]['WALLET_TEMP_ID'];
        toID = userInfo[0]['WALLET_ID'];
        fromPreW = nWalletTemp;
        toPreW = nWallet;
        // console.log(fromID, fromPreW, toID, toPreW);
        await commonModel.updateDBbyCondition(
            'user_wallets',
            [
                {
                    key: 'WALLET_ID',
                    val: fromID
                }
            ],
            [
                {
                    key: 'WALLET',
                    val: parseInt(nWalletTemp) - parseInt(amount)
                },
                {
                    key: 'UPDATE_TIME',
                    val: dt
                }
            ]
        );
        await commonModel.updateDBbyCondition(
            'user_wallets',
            [
                {
                    key: 'WALLET_ID',
                    val: toID
                }
            ],
            [
                {
                    key: 'WALLET',
                    val: parseInt(nWallet) + parseInt(amount)
                },
                {
                    key: 'UPDATE_TIME',
                    val: dt
                }
            ]
        );
    }
    let insert_items = [
        {
            key: 'CREATE_TIME',
            val: dt
        },
        {
            key: 'UPDATE_TIME',
            val: dt
        },
        {
            key: 'FROM_WID',
            val: fromID
        },
        {
            key: 'TO_WID',
            val: toID
        },
        {
            key: 'FROM_PRE_W',
            val: fromPreW
        },
        {
            key: 'TO_PRE_W',
            val: toPreW
        },
        {
            key: 'TYPE',
            val: 1
        },
        {
            key: 'AMOUNT',
            val: amount
        },{
            key: 'GAME_ID',
            val: gameId
        }
    ];
    if(type == 1) {
        insert_items.push({
            key: 'NOTE',
            val: userInfo_1[0]['USERNAME'] + ' betting'
        });
    }
    else if(type == 2) {
        insert_items.push({
            key: 'NOTE',
            val: userInfo_1[0]['USERNAME'] + ' cashout'
        });
    }
    else {
        insert_items.push({
            key: 'NOTE',
            val: 'Game is bust'
        });
    }
    await commonModel.insertToDB('wallet_history', insert_items)
}

async function gameBust(nGameID, nBustTick) {

    const gameData = await commonModel.getGameDatabyCondition(
        'game_total',
        [
            {
                key: 'GAME_ID',
                val: nGameID
            },
            {
                key: 'STATE',
                val: 'STARTED'
            }
        ]
    );
    if (!gameData.success || gameData.data == null || gameData.data.length == 0) {
        return 0;
    }

    let gameInfo = gameData.data[0];
    if (gameInfo['STATE'] != 'STARTED')
        return 0;

    bustGame(gameInfo, nBustTick);

    return 1;
}

async function gameFinishStart() {

    let gameData = await commonModel.getLastGameData('game_total');

    if (!gameData.success || gameData.data == null || gameData.data.length == 0) {
        return 0;
    }

    let gameInfo = gameData.data[0];
    if (gameInfo['STATE'] != 'WAITING') {
        return 0;
    }

    let dt = moment(new Date()) / 1000;
    await commonModel.updateDBbyGameID('game_total',
        gameInfo['ID'], [{
                key: 'STATE',
                val: 'STARTED'
            },
            {
                key: 'BUST',
                val: 100
            },
            {
                key: 'START_TIME',
                val: dt
            },
            {
                key: 'UPDATE_TIME',
                val: dt
            }
        ]
    );
    bustGame(gameInfo, 100);

    nNextCrashGameID = await model.nextGameID();
    // if (nNextCrashGameID > 1) nNextCrashGameID --;
    await commonModel.insertToDB('game_total', [{
            key: 'GAME_ID',
            val: nNextCrashGameID
        },
        {
            key: 'CREATE_TIME',
            val: dt
        },
        {
            key: 'UPDATE_TIME',
            val: dt
        },
        {
            key: 'STATE',
            val: 'WAITING'
        }
    ]);

    if (nNextCrashGameID) {
        aryCashOut = [];
        aryPlayer = [];
        nGameStatus = 4;
        nResetGameTime = 0;
        setTimeout(function () {
            waitGame();
        }, 3000);

        io.emit('onMessage', {
            code: 'GameCrash',
            crash: 100
        });
    }
}

router.post('/bet', verifyToken, async function (req, res) {

    if (req.current_user === undefined || req.current_user === null || req.current_user.id === undefined || req.current_user.id === 0)
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });

    const userInfo = await usersModel.detailUsers([{
        key: 'ID',
        val: req.current_user.id
    }]);

    if (userInfo == null || userInfo.length == 0) {
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });
    }

    let formatted = new Date().getTime();
    formatted = Math.round(formatted / 1000);
    const betAmount = req.body.bet;
    let nWallet = await usersModel.getWalletInfo(userInfo[0]['ID'], 1);

    if (nWallet < betAmount)
        return res.json({
            status: "error",
            res_msg: "あなたのウォレットが十分ではありません。"
        });

    nWallet -= betAmount;
    await updateWalletInfo(userInfo[0]['ID'], 1, nWallet, betAmount, 1, nCrashGameID);
    const betUserInfo = {
        userid: userInfo[0]['ID'],
        value: betAmount,
        username: userInfo[0]['USERNAME'],
        cashout: 0,
        isBot: 'N'
    }
    aryPlayer.push(betUserInfo);

    await commonModel.insertToDB('game_log', [{
            key: 'GAME_ID',
            val: nCrashGameID
        },
        {
            key: 'USER_ID',
            val: userInfo[0].ID
        },
        {
            key: 'CREATE_TIME',
            val: formatted
        },
        {
            key: 'UPDATE_TIME',
            val: formatted
        },
        {
            key: 'BOT_YN',
            val: 'N'
        },
        {
            key: 'CASHOUT',
            val: 0
        },
        {
            key: 'BET',
            val: betAmount
        }
    ]);
    instance.post("Setting/check_affiliate", {user_id: req.current_user.id})
    .then((response) => {
        console.log(response.data)
    })
    .catch((error) => {
        console.log(error)
    })
    io.emit('onMessage', {
        code: 'Bet',
        username: userInfo[0].USERNAME,
        userid: userInfo[0].ID,
        value: req.body.bet,
        isBot: 'N'
    });
    return res.json({
        status: "success",
        gameid: nCrashGameID,
        value: req.body.bet
    });
});

router.post('/cancelBet', verifyToken, async function (req, res) {

    if (req.current_user === undefined || req.current_user === null || req.current_user.id === undefined || req.current_user.id === 0) {
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });
    }

    const userInfo = await usersModel.detailUsers([{
        key: 'ID',
        val: req.current_user.id
    }]);

    if (userInfo == null || userInfo.length == 0) {
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });
    }

    let formatted = new Date().getTime();
    formatted = Math.round(formatted / 1000);
    const betAmount = req.body.bet;
    let nWallet = await usersModel.getWalletInfo(userInfo[0]['ID'], 1);

    nWallet = parseInt(nWallet) + parseInt(betAmount);
    await updateWalletInfo(userInfo[0]['ID'], 1, nWallet, betAmount, 1, nCrashGameID);

    let i;
    for(i = 0; i < aryPlayer.length; i ++) {
        if (aryPlayer[i].userid == userInfo[0]['ID']) {
            aryPlayer.splice(i, 1);
            break;
        }
    }

    await commonModel.deleteDBbyCondition('game_log', [{
            key: 'GAME_ID',
            val: nCrashGameID
        },
        {
            key: 'USER_ID',
            val: userInfo[0].ID
        },
        {
            key: 'BOT_YN',
            val: 'N'
        },
        {
            key: 'CASHOUT',
            val: 0
        },
        {
            key: 'BET',
            val: betAmount
        },
    ]);

    io.emit('onMessage', {
        code: 'CancelBet',
        username: userInfo[0].USERNAME,
        userid: userInfo[0].ID,
        value: req.body.bet,
        isBot: 'N'
    });
    return res.json({
        status: "success",
    });
});

router.post('/cashOut', verifyToken, async function (req, res) {

    if (req.current_user === undefined || req.current_user === null || req.current_user.id === undefined || req.current_user.id === 0) {
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });
    }
    const userInfo = await usersModel.detailUsers([{
        key: 'ID',
        val: req.current_user.id
    }]);
    if (userInfo == null || userInfo.length == 0) {
        return res.json({
            status: "error",
            res_msg: "まず、ログインする必要があります。"
        });
    }
    let gameData = await commonModel.getGameDatabyCondition('game_total',
        [{
            key: 'GAME_ID',
            val: nCrashGameID
        }]
    );
    if (!gameData.success || gameData.data == null || gameData.data.length == 0) {
        return res.json({
            status: "error",
            res_msg: 'インターネット問題が発生しました。'
        })
    }
    let gameInfo = gameData.data[0];
    let userID = req.current_user.id;
    let cashRate = req.body.stopped_at / 100;
    /*
    if (gameInfo['STATE'] != 'STARTED')
        return res.json({
            status: "error",
            res_msg: '無効なゲーム ID'
        });
        */
    if (gameInfo['BUST'] < req.body.stopped_at) {
        return res.json({
            status: "error",
            res_msg: 'あなたのcashout値がゲームのbust値より大きいです。'
        });
    }

    gameData = await commonModel.getGameDatabyCondition('game_log', [{
            key: 'USER_ID',
            val: userID
        },
        {
            key: 'GAME_ID',
            val: nCrashGameID
        },
        {
            key: 'BOT_YN',
            val: 'N'
        }
    ]);
    if (!gameData.success || gameData.data == null || gameData.data.length == 0) {
        return res.json({
            status: "error",
            res_msg: '賭け情報がありません。'
        });
    }

    gameInfo = gameData.data[0];
    if (gameInfo['CASHOUT'] > 0)
        return res.json({
            status: "error",
            res_msg: 'すでにcashoutされました。'
        });

    const cashout = gameInfo['BET'] * cashRate;
    await commonModel.updateDBbyGameID('game_log',
        gameInfo['ID'],
        [
            {
                key: 'CASHOUT',
                val: req.body.stopped_at
            },
            {
                key: 'PROFIT',
                val: cashout - gameInfo['BET']
            },
            {
                key: 'CRASHED_YN',
                val: 'N'
            },
            {
                key: 'DEL_YN',
                val: 'N'
            }
        ]
    );

    let nWallet = await usersModel.getWalletInfo(userID, 1);
    nWallet += cashout;
    await updateWalletInfo(userID, 1, nWallet, cashout, 2, nCrashGameID);

    for (i = 0; i < aryPlayer.length; i ++) {
        let playerInf = aryPlayer[i]
        if(playerInf.isBot == 'N' && playerInf.userid == userID) {
            playerInf.cashout = req.body.stopped_at
            playerInf.profit = cashout - gameInfo['BET']
            aryCashOut.push(playerInf);
            aryPlayer.splice(i, 1);
            break;
        }
    }

    io.emit('onMessage', {
        code: 'CashOut',
        username: userInfo[0].USERNAME,
        userid: userInfo[0].ID,
        value: req.body.bet,
        cashout: cashRate * 100,
        isBot: 'N'
    });
    return res.json({
        status: "success",
        profit: parseInt(cashout) - parseInt(req.body.bet)
    });
});

router.post('/getHistory', verifyUser, async function(req, res) {
    let historyData = await commonModel.getCrashHistory();

    if (req.user_from_token !== undefined && req.user_from_token.id !== undefined && req.user_from_token.id !== -1) {
        const userInfo = await usersModel.detailUsers([{
            key: 'ID',
            val: req.user_from_token.id
        }]);

        if (userInfo == null || userInfo.length == 0) {
            return res.json({
                status: "error",
                res_msg: "Not authenticated user"
            });
        }

        const betData = await commonModel.getBetHistory(req.user_from_token.id);
        let i, j;
        if (historyData.data != null && historyData.data.length > 0 &&
            betData.data != null && betData.data.length != 0) {
            for (i = 0; i < historyData.data.length; i ++) {
                j = 0;
                for(j = 0; j < betData.data.length; j ++) {
                    if (betData.data[j]['GAME_ID'] == historyData.data[i]['GAME_ID']) {
                        historyData.data[i]['BET'] = betData.data[j]['BET'];
                        if( parseFloat(betData.data[j]['CASHOUT']) != 0)
                            historyData.data[i]['CASHOUT'] = parseFloat(parseFloat(betData.data[j]['CASHOUT']) / 100).toFixed(2) + 'x';
                        else
                            historyData.data[i]['CASHOUT'] = null;
                        historyData.data[i]['PROFIT'] = betData.data[j]['PROFIT'];
                    }
                }
            }
        }
    }

    if (historyData.data != null && historyData.data.length > 0) {
        return res.json({
            status: 'success',
            data: historyData.data
        });
    }
})

router.post('/getStatus', verifyUser, async function (req, res) {
    if (req.user_from_token === undefined || req.user_from_token.id === undefined || req.user_from_token.id === -1) {
        return res.json({
            status: 'error',
            data: null
        })
    }
    if (nGameStatus == 2) {
        return res.json({
            status: 'success',
            data: {
                gamestat: 'WaitGame',
                game_id: nCrashGameID,
                curUser: aryPlayer,
                cashoutUser: aryCashOut,
                time_left: (nResetGameTime + 5000 - Date.now())
            }
        })
    } else if(nGameStatus == 3) {
        console.log('Game is Started.');
        /*
        socket.emit('onMessage', {
            code: 'GameStart',
            game_id: nCrashGameID,
            tick: nCurTick
        });*/
        // we send who's the game players bet
        return res.json({
            status: 'success',
            data: {
                gamestat: 'GameStart',
                game_id: nCrashGameID,
                curUser: aryPlayer,
                cashoutUser: aryCashOut,
                tick: nCurTick
            }
        })
    } else if (nGameStatus == 4) {
        return res.json({
            status: 'success',
            data: {
                gamestat: 'GameCrash',
                crash: nCrashTick
            }
        })
    }
})
router.post('/betInfo', async function (req, res) {
    let betInfo = await commonModel.getGameDatabyCondition('game_log', [{
        key: 'id',
        val: req.body.id
    }]);
    if (!betInfo.success || !betInfo.data || betInfo.data.length === 0) {
        return res.json({
            status: 'error',
            data: null
        });
    }
    const bustInfo = await commonModel.getGameDatabyCondition('game_total', [{
        key: 'GAME_ID',
        val: betInfo.data[0].GAME_ID
    }]);
    if (!bustInfo.success || !bustInfo.data || bustInfo.data.length === 0) {
        return res.json({
            status: 'error',
            data: null
        });
    }

    let userName = ''
    if (betInfo.data[0].BOT_YN === 'Y') {
        const userInfo = await usersModel.getBotInfo(betInfo.data[0]['USER_ID']);
        if (userInfo != null && userInfo.length > 0) {
            userName = userInfo[0]['NAME']
        }
    } else {
        const userInfo = await usersModel.detailUsers({
            key: 'ID',
            val: betInfo.data[0]['USER_ID']
        })
        if (userInfo != null && userInfo.length > 0) {
            userName = userInfo[0]['USERNAME'];
        }
    }
    betInfo.data[0]['BUST'] = bustInfo.data[0]['BUST']
    betInfo.data[0]['USERNAME'] = userName

    return res.json({
        status: 'success',
        data: betInfo.data
    });
})

router.post('/gameInfo', async function (req, res) {
    if (req.body.id === undefined)
        return res.json({
            status: 'error',
            data: null
        });
    const betInfo = await commonModel.getGameDatabyCondition('game_log', [{
        key: 'GAME_ID',
        val: req.body.id
    }]);
    if (!betInfo.success) {
        return res.json({
            status: 'error',
            data: null
        });
    }

    const bustInfo = await commonModel.getGameDatabyCondition('game_total', [{
        key: 'GAME_ID',
        val: req.body.id
    }]);
    const bustValue = bustInfo.data[0]['BUST'];
    const bustTime = bustInfo.data[0]['BUST_TIME']

    let userInfo, userName = ''
    let retData = new Array();
    for (let i = 0; i < betInfo.data.length; i ++) {
        if (betInfo.data[i]['BOT_YN'] == 'Y') {
            userInfo = await usersModel.getBotInfo(betInfo.data[i]['USER_ID']);
            if (userInfo != null && userInfo.length > 0) {
                userName = userInfo[0]['NAME'];
            }
        } else {
            userInfo = await usersModel.detailUsers({
                key: 'ID',
                val: betInfo.data[i]['USER_ID']
            })
            if (userInfo != null && userInfo.length > 0) {
                userName = userInfo[0]['USERNAME'];
            }
        }

        retData.push({
            username: userName,
            cashout: betInfo.data[i]['CASHOUT'],
            value: betInfo.data[i]['BET'],
            profit: betInfo.data[i]['PROFIT'].toFixed(0),
            betid: betInfo.data[i]['ID']
        });
    }

    return res.json({
        status: 'success',
        data: retData,
        bust: bustValue,
        cur_game_id: nCrashGameID,
        bust_time: bustTime
    });
})

module.exports = {
    router,
    initializeSocketIO
}