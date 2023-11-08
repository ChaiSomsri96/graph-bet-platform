var express = require('express');
var router = express.Router();
var moment = require('moment');
var model = require('../model/setting');
var user_model = require('../model/users');
var commonModel = require('../model/common');
router.post('/list', async function (req, res, next) {
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
});
router.post('/check_affiliate', async function(req, res, next) {
    let user_id = req.body.user_id
    let yes = await model.calcCheck(user_id)
    if(yes) {
        return res.json({
            status: 'success',
            data: null
        })
    }
    let calc_trans_sum = await model.calcTransSum(user_id)
    let user_info = await user_model.getUserInfo(user_id)
    let first_deposit_sum = await model.calcFirstDepositSum(user_info[0]['WALLET_ID'])
    if(calc_trans_sum == 0 || first_deposit_sum == 0) {
        return res.json({
            status: 'success',
            data: null
        })
    }
    let affiliate_fee = await model.getSettingValue('affiliate_fee')
    let transaction_limit = await model.getSettingValue('transaction_limit')
    if(parseFloat(calc_trans_sum) > parseFloat(first_deposit_sum) * parseFloat(transaction_limit)) {

        //get admin info
        let adminInfo = await user_model.getAdminUsers();
        if(adminInfo == null || adminInfo.length == 0) {
            return res.json({
                status: 'fail',
                data: null
            })
        }
        let fromId, toId, fromPreW, toPreW, walletInfo, amount = 0, to_user_info;

        amount = parseFloat(first_deposit_sum) * parseFloat(affiliate_fee) / 100;
        fromId = adminInfo[0]['WALLET_AFF_ID'];
        walletInfo = await commonModel.getGameDatabyCondition('user_wallets', [{
            key: 'WALLET_ID',
            val: fromId
        }]);
        if (walletInfo.data == null || walletInfo.data.length == 0) {
            return res.json({
                status: 'fail',
                data: null
            })
        }
        fromPreW = walletInfo.data[0]['WALLET'];
        to_user_info = await user_model.detailUsers([{
            key: 'AFFILIATE_CODE',
            val: user_info[0]['AFFILIATE_CODE_P']
        }]);
        toId = to_user_info[0]['WALLET_ID'];

        let to_walletInfo = await commonModel.getGameDatabyCondition('user_wallets', [{
            key: 'WALLET_ID',
            val: toId
        }]);
        toPreW = to_walletInfo.data[0]['WALLET'];
        let dt = moment(new Date()) / 1000;
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
                val: fromId
            },
            {
                key: 'TO_WID',
                val: toId
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
                val: 3
            },
            {
                key: 'AMOUNT',
                val: amount
            },{
                key: 'GAME_ID',
                val: 0
            },{
                key: 'NOTE',
                val: user_info[0]['USERNAME'] + ' affiliate calculation'
            }
        ];
        await commonModel.insertToDB('wallet_history', insert_items)
        await user_model.updateWalletData(fromId, amount, 2)
        await user_model.updateWalletData(toId, amount, 1)
        await model.updateCheck(user_id)
        return res.json({
            status: 'success',
            data: null
        })
    }
})
module.exports = router;