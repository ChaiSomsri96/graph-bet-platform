var express = require('express');
var router = express.Router();
let moment = require('moment');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const multer = require('multer');
const path = require('path');
const helpers = require('../helpers/helpers');
var md5 = require('md5');
var model = require('../model/users');
var commonModel = require('../model/common');
var settingModel = require('../model/setting');
var config = require('../config')
var util = require('../utils/index')
const verifyToken = require('../middleware/verify_token');

router.post('/genHash', function (req, res, next) {
  let password = 'password'

  var salt = bcrypt.genSaltSync(10);
  var hash = bcrypt.hashSync(password, salt);

  return res.json(hash)
});

router.post('/login', async function (req, res, next) {
  let username = req.body.username,
    password = req.body.password,
    ret = {
      status: 'fail',
      data: ''
    };

  if (!username || username.trim().length < 1) {
    ret.data = "ユーザー名の空白";
    return res.json(ret);
  }

  if (!password || password.trim().length < 1) {
    ret.data = "空のパスワード";
    return res.json(ret);
  }
  const users = await model.detailUsers([{
    key: 'USERNAME',
    val: username
  }])

  if ( users == null || users.length == 0 || ( md5(password) != users[0]['PASSWORD'] ) ) {
    ret.status = 'fail'
    ret.data = "ユーザー名またはパスワードが正しくありません。"
    return res.json(ret)
  }
  if( users[0]['BLOCK_YN'] == 'Y' ) {
    ret.status = 'fail'
    ret.data = "ブロックされたアカウントなので使用できません。"
    return res.json(ret)
  }
  // create a token
  let token = jwt.sign({
    id: users[0].ID,
    roles: 'normal'
  }, config.access_token_secret, {
    expiresIn: config.token_ttl * 60 * 60 * 24 * 1000
  });

  ret.status = 'success'
  ret.data = users[0]
  ret.data['jwt_token'] = token

  return res.json(ret)
});
router.post('/info', verifyToken, function (req, res, next) {
  return res.json({
    status: 'success',
    data: req.current_user
  })
});
router.post('/wallet', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id

  let nWallet = await model.getWalletInfo(user_id, 1);

  return res.json({
    status: 'success',
    data: nWallet
  })
});
router.post('/getTotalProfit', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id

  let nProfit = await model.getTotalProfit(user_id);

  return res.json({
    status: 'success',
    data: nProfit
  })
});
router.post('/getBettingData', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id
  let betData = await model.getBettingData(user_id);
  return res.json({
    status: 'success',
    data: betData
  })
});
router.post('/getUserCount', async function(req, res) {
  let userCount = await model.getUserCount('users');
  let botCount = await model.getUserCount('game_bots');
  return res.json({
    status: 'success',
    data: userCount + botCount
  })
})
router.post('/register', async function(req, res) {
  let username = req.body.username;
  let password = req.body.password;
  let email = req.body.email;
  let aff_code = req.body.aff_code;

  let users = await model.detailUsers([{
    key: 'USERNAME',
    val: username
  }])
  if (users != null && users.length > 0) {
    return res.json({
      status: 'failed',
      resMsg: '同じユーザー名がすでに存在します。'
    });
  }

  users = await model.detailBots([{
    key: 'NAME',
    val: username
  }])

  if (users != null && users.length > 0) {
    return res.json({
      status: 'failed',
      resMsg: '同じユーザー名がすでに存在します。'
    });
  }

  if (email.length > 0) {
    users = await model.detailUsers([{
      key: 'EMAIL',
      val: email
    }])
    if (users != null && users.length > 0) {
      return res.json({
        status: 'failed',
        resMsg: '同じEメール情報が既に存在します。'
      });
    }
  }

  let affiliate_code = 'Qb_' + util.makeAffiliateCode(8)
  do {
    const chID = 'abcdefghijklmnopqrstuvwxyz1234567890'
    let szWalletID = ''
    for (let i = 0; i < parseInt(Math.random() * 6 + 6); i++) {
      szWalletID += chID[parseInt(Math.random() * chID.length)]
    }
    users = await model.detailUsers([{
      key: 'WALLET_ID',
      val: szWalletID
    }])
    //let salt = bcrypt.genSaltSync(10);
    let hash = md5(password);
    if (users == null || users.length == 0) {
      let dt = new Date().getTime();
      dt = Math.round(dt / 1000);
      await commonModel.insertToDB('users', [{
          key: 'USERNAME',
          val: username
        },
        {
          key: 'PASSWORD',
          val: hash
        },
        {
          key: 'EMAIL',
          val: email
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
          key: 'DEL_YN',
          val: 'N'
        },
        {
          key: 'AFFILIATE_CODE',
          val: affiliate_code
        },
        {
          key: 'AFFILIATE_CODE_P',
          val: aff_code
        },
        {
          key: 'WALLET_ID',
          val: szWalletID
        }
      ]);
      await commonModel.insertToDB('user_wallets', [{
          key: 'CREATE_TIME',
          val: dt
        },
        {
          key: 'UPDATE_TIME',
          val: dt
        },
        {
          key: 'WALLET_ID',
          val: szWalletID
        },
        {
          key: 'WALLET',
          val: 0
        }
      ]);
      return res.json({
        status: 'success',
        resMsg: ''
      });
    }
  } while(users != null && users.length > 0)
})
router.post('/sendBits', verifyToken, async function (req, res) {
  let dt = moment(new Date()) / 1000;
  const admin_wallet_id = 'ADMIN'

  if (req.body.user === undefined || req.body.user === '') {
    return res.json({
      status: 'error',
      data: '`user` field is not defined'
    })
  }
  if (req.body.bits === undefined || isNaN(parseInt(req.body.bits))) {
    return res.json({
      status: 'error',
      data: '`bits` field is not defined'
    })
  }
  const users = await model.detailUsers([{
    key: 'USERNAME',
    val: req.body.user
  }])
  if (users === null || users.length === 0) {
    return res.json({
      status: 'error',
      data: '送信しようとするユーザーIDが存在しません。'
    })
  }
  const current_users = await model.detailUsers([{
    key: 'ID',
    val: req.current_user.id
  }])
  if (current_users === null || current_users.length === 0) {
    return res.json({
      status: 'error',
      data: '送信しようとするユーザーIDが存在しません。'
    })
  }
  const setting = await settingModel.list()
  let fee = 1
  if (setting.data != null && setting.data.length > 0) {
    for (let i = 0; i < setting.data.length; i++) {
      if (setting.data[i].VARIABLE === 'fee') {
        fee = parseInt(setting.data[i].VALUE)
      }
    }
  }
  const recv_user = users[0]
  const current_user = current_users[0]
  const send_user_wallet = await model.getWalletInfo(req.current_user.id)
  const recv_user_wallet = await model.getWalletInfo(recv_user.ID)
  const admin_wallet_info = await commonModel.getGameDatabyCondition(
    'user_wallets', [{
        key: 'WALLET_ID',
        val: admin_wallet_id
      }
    ]
  )
  let admin_wallet = 0
  if (admin_wallet_info == null || admin_wallet_info.length == 0) {
    admin_wallet = 0
  } else {
    admin_wallet = admin_wallet_info.data[0]['WALLET']
  }

  if (parseInt(req.body.bits) + fee > parseInt(send_user_wallet)) {
    return res.json({
      status: 'error',
      data: '送ろうとする金額があなたのWalletより大きいです。'
    })
  }
  await commonModel.updateDBbyCondition(
    'user_wallets', [{
      key: 'WALLET_ID',
      val: current_user.WALLET_ID
    }], [{
        key: 'WALLET',
        val: parseInt(send_user_wallet) - parseInt(req.body.bits) - fee
      },
      {
        key: 'UPDATE_TIME',
        val: dt
      }
    ]
  )
  await commonModel.updateDBbyCondition(
    'user_wallets', [{
      key: 'WALLET_ID',
      val: recv_user.WALLET_ID
    }], [{
        key: 'WALLET',
        val: parseInt(recv_user_wallet) + parseInt(req.body.bits)
      },
      {
        key: 'UPDATE_TIME',
        val: dt
      }
    ]
  );
  await commonModel.updateDBbyCondition(
    'user_wallets', [{
      key: 'WALLET_ID',
      val: admin_wallet_id
    }], [{
        key: 'WALLET',
        val: parseInt(admin_wallet) + fee
      },
      {
        key: 'UPDATE_TIME',
        val: dt
      }
    ]
  );

  await commonModel.insertToDB('wallet_history', [{
      key: 'CREATE_TIME',
      val: dt
    },
    {
      key: 'UPDATE_TIME',
      val: dt
    },
    {
      key: 'FROM_WID',
      val: current_user.WALLET_ID
    },
    {
      key: 'TO_WID',
      val: recv_user.WALLET_ID
    },
    {
      key: 'FROM_PRE_W',
      val: send_user_wallet
    },
    {
      key: 'TO_PRE_W',
      val: recv_user_wallet
    },
    {
      key: 'TYPE',
      val: 2
    },
    {
      key: 'AMOUNT',
      val: parseInt(req.body.bits)
    }
  ])

  await commonModel.insertToDB('wallet_history', [{
      key: 'CREATE_TIME',
      val: dt
    },
    {
      key: 'UPDATE_TIME',
      val: dt
    },
    {
      key: 'FROM_WID',
      val: current_user.WALLET_ID
    },
    {
      key: 'TO_WID',
      val: admin_wallet_id
    },
    {
      key: 'FROM_PRE_W',
      val: send_user_wallet
    },
    {
      key: 'TO_PRE_W',
      val: admin_wallet
    },
    {
      key: 'TYPE',
      val: 2
    },
    {
      key: 'AMOUNT',
      val: fee
    }
  ])
  return res.json({
    status: 'success',
    data: null
  })
})
router.post('/modify', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id
  if (req.body.pwd === undefined || req.body.pwd === '' || req.body.pwd.trim().length < 1) {
    return res.json({
      status: 'error',
      data: '`Pwd` field is not defined'
    })
  }
  const users = await model.detailUsers([{
    key: 'ID',
    val: user_id
  }])

  if (users == null || users.length == 0 || md5(req.body.pwd) != users[0]['PASSWORD']) {
    return res.json({
      status: 'error',
      data: '無効なパスワード'
    })
  }
  if (req.body.new_pwd !== undefined && req.body.new_pwd !== '' && req.body.new_pwd.trim().length < 6) {
    return res.json({
      status: 'error',
      data: '新しいパスワードは 6 文字以上です。'
    })
  }
  let setItems = []
  if (req.body.email !== undefined && req.body.email !== '' && req.body.email.trim().length >= 1) {
    setItems.push({
      key: 'EMAIL',
      val: req.body.email
    })
  }
  if (req.body.new_pwd !== undefined && req.body.new_pwd !== '' && req.body.new_pwd.trim().length >= 6) {
    setItems.push({
      key: 'PASSWORD',
      val: md5(req.body.new_pwd)
    })
  }
  const updateUsersRes = await model.updateUsers([{
    key: 'ID',
    val: user_id
  }], setItems)
  return res.json({
    status: updateUsersRes.success ? 'success' : 'error',
    data: updateUsersRes.success ? null : 'パスワードの変更に失敗しました。'
  })
});

const storage = multer.diskStorage({
  destination: function(req, file, cb) {
      cb(null, config.LOCATION_PATH);
  },
  // By default, multer removes file extensions so let's add them back
  filename: function(req, file, cb) {
      cb(null, file.fieldname + '-' + Date.now() + path.extname(file.originalname));
  }
});

function uploadProcess(upload, req, res, user_id, type) {
  upload(req, res, async function(err) {
    // req.file contains information of uploaded file
    // req.body contains information of text fields, if there were any
    if (req.fileValidationError) {
        //return res.send(req.fileValidationError);
        var resp = {
            status: 'fail',
            data: null
        };
        return res.json(resp);
    }
    else if (!req.file) {
        //return res.send('Please select an image to upload');
      var resp = {
          status: 'fail',
          data: null
      };
      return res.json(resp);
    }
    else if (err instanceof multer.MulterError) {
        // return res.send(err);
      var resp = {
          status: 'fail',
          data: null
      };
      return res.json(resp);
    }
    else if (err) {
        //return res.send(err);
        var resp = {
          status: 'fail',
          data: null
        };
        return res.json(resp);
    }
    let setItems = []
    if(type == 1) {
      setItems.push({
        key: 'PASSPORT',
        val: 'imgs/' + req.file.filename
      })
    }
    else {
      setItems.push({
        key: 'ID_CARD',
        val: 'imgs/' + req.file.filename
      })
    }
    const updateUsersRes = await model.updateUsers([{
      key: 'ID',
      val: user_id
    }], setItems)
    return res.json({
      status: updateUsersRes.success ? 'success' : 'fail',
      data: null
    })
  });
}

router.post('/upload_kyc_passport', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id
  let upload = multer({ storage: storage, fileFilter: helpers.imageFilter }).single('file');
  uploadProcess(upload, req, res, user_id, 1)
});

router.post('/upload_kyc_idcard', verifyToken, async function (req, res, next) {
  const user_id = req.current_user.id
  let upload = multer({ storage: storage, fileFilter: helpers.imageFilter }).single('file');
  uploadProcess(upload, req, res, user_id, 2)
});

router.post('/get_kyc_info', verifyToken, async function(req, res, next) {
  const user_id = req.current_user.id
  let user_info = await model.detailUsers([{key: 'ID', val: user_id}]);
  if( user_info == null) {
    return res.json({
      status: 'fail',
      data: null
    });
  }
  else {
    return res.json({
      status: 'success',
      data: {
          'kyc_reg': user_info[0]['KYC_YN'] == 'Y' ? true : false,
          'passport_upload': ( user_info[0]['PASSPORT'] != '' && user_info[0]['PASSPORT'] != null ) ? true : false,
          'idcard_upload': ( user_info[0]['PASSPORT'] != '' && user_info[0]['PASSPORT'] != null ) ? true : false
      }
    });
  }
});

router.post('/support', async function (req, res, next) {
  return res.json({
    status: 'success',
    data: null
  })
});

module.exports = router;