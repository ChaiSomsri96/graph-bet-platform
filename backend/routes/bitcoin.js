var express = require('express');
var router = express.Router();
var request = require('request');
const verifyToken = require('../middleware/verify_token');
var config = require('../config')
var btcDepositAddressModel = require('../model/btc_deposit_address')
var usersModel = require('../model/users')
var commonModel = require('../model/common')
var txnModel = require('../model/txn')
var bitcoin = require("bitcoinjs-lib");
var bigi    = require("bigi");
var buffer  = require('buffer');
/* router.post('/list', async function (req, res, next) {
    const {
        success,
        data
    } = await model.list()
    if (success) {
        return res.json({
            status: 'success',
            data: data
        })
    } else {
        return res.json({
            status: 'fail',
            data: null
        })
    }
}); */
router.post('/get_deposit_address', verifyToken, function (req, res , next) {
    const user_id = req.current_user.id
    btcDepositAddressModel.getDepositAddressData({who: user_id}, function (err, modelResult) {
        if(err) {
            var resp = {
                status: 'fail',
                data: null
            };
            return res.json(resp);
        }
        else {
            if(modelResult.length > 0){
                var resp = {
                    status: 'success',
                    data: modelResult.content.INPUT_ADDRESS
                };
                return res.json(resp);
            }
            else {
                var params = {
                    "destination" : config.BTC_SITE_WALLET_ADDRESS,
                    "callback_url" : config.BLOCKCYPHER_CALLBACK_HOST_URL + "Bitcoin/deposit/" + user_id,
                    "mining_fees_satoshis" : 10000
                };
                var options = {
                    method: 'POST',
                    url: 'https://api.blockcypher.com/v1/btc/main/payments?token=' + config.BLOCKCYPHER_TOKEN,
                    body: params,
                    json: true
                };
                request(options , function(err, response, apiResult) {
                   /* apiResult.input_address = "1JAzMZyLLJ2rbce67DjkJSoKXLgtSPPYQN"
                    apiResult.destination = "19ejNRyHmqgotgztP7XeLsXbHD7K5tkTJo"
                    apiResult.id = '000b17ef-8713-47b6-9153-26ddf998e066' */
                    btcDepositAddressModel.insertNewDepositAddress({who:user_id, input_address:apiResult.input_address, destination:apiResult.destination, input_address_id:apiResult.id} , function(err , subModelResult) {
                        if (err) {
                            var resp = {
                                status: 'fail',
                                data: null
                            };
                            return res.json(resp);
                        } else {
                            var resp = {
                                status: 'success',
                                data: apiResult.input_address
                            };
                            return res.json(resp);
                        }
                    })
                });
            }
        }
    });
});

router.post('/deposit/:who', async function(req, res, next) {
    var who = req.params.who;
    var satoshi_amount = req.body.value;
    var amount = parseFloat(satoshi_amount / Math.pow(10, 8)).toFixed(8);
    var tx_hash = req.body.input_transaction_hash;
    var destination_txhash = req.body.transaction_hash;


    var options = {
        method: 'GET',
        url: 'https://api.blockcypher.com/v1/btc/main/txs/' + destination_txhash
    };
    request(options, function (error, response, body) {
        if (error) {
            var resp = {
                status: 'fail',
                data: null
            };
            return res.json(resp);
        } else {
            usersModel.getUserInfo(who).then((userInfo) => {
                if(userInfo == null) {
                    var resp = {
                        status: 'fail',
                        data: null
                    };
                    return res.json(resp);
                } else {
                    //userid  userInfo[0]['WALLET_ID']
                    let pre_w = userInfo[0]['WALLET'];
                    usersModel.updateWallet({wallet_id: userInfo[0]['WALLET_ID'], amount: amount}, function(err, modelResult) {
                        if(!err) {
                            var txnData = {
                                wallet_id : userInfo[0]['WALLET_ID'],
                                type : 1,
                                amount : amount,
                                fees : 0.0001,
                                detail : tx_hash,
                                txhash : destination_txhash,
                                pre_w : pre_w
                            }
                            txnModel.insertTxn(txnData, function(err, subModelResult) {
                                if (err) {
                                    var resp = {
                                        status: 'fail',
                                        data: null
                                    };
                                    return res.json(resp);
                                }
                                else {
                                    var resp = {
                                        status: 'success',
                                        data: null
                                    };
                                    return res.json(resp);
                                }
                            });
                        }
                        else {
                            var resp = {
                                status: 'fail',
                                data: null
                            };
                            return res.json(resp);
                        }
                    })
                }
            })
            .catch((err) => {
                var resp = {
                    status: 'fail',
                    data: null
                };
                return res.json(resp);
            })
        }
    });
});

router.post('/withdraw_request', verifyToken, async function (req, res , next) {
    const user_id = req.current_user.id
    var to_address = req.body.to_address;
    var amount = parseFloat(req.body.amount);

    try {
        let user_info = await usersModel.getUserInfo(user_id)
        if(user_info[0]['WALLET'] < amount) {
            var resp = {
                status: 'fail',
                data: 'あなたの財布が不足しています。 今引き出せません。'
            };
            return res.json(resp);
        }
        await usersModel.requestWithdraw(user_info[0]['WALLET_ID'], amount);
        let txnData = {
            'wallet_id': user_info[0]['WALLET_ID'],
            'amount': parseFloat(amount / Math.pow(10, 6)).toFixed(8),
            'amount_bits': amount,
            'fees': 0,
            'detail': to_address,
            'txhash': '',
            'pre_w': user_info[0]['WALLET'],
            'type': 2,
            'status': 0
        };
        await txnModel.insertData(txnData);
        var resp = {
            status: 'success',
            data: null
        };
        return res.json(resp);
    }
    catch (err) {
        console.log(err)
        var resp = {
            status: 'fail',
            data: 'API呼出し失敗'
        };
        return res.json(resp);
    }
});

module.exports = router;