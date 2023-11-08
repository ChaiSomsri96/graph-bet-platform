const jwt = require('jsonwebtoken');
var config = require('../config')

function verifyUser(req, res, next) {
    if (req.headers.authorization && req.headers.authorization.toLowerCase().indexOf("bearer ") != -1) {
        let token = req.headers.authorization.split(' ')[1];

        if (token !== undefined && token.trim().length >= 1) {
            // Verify JWT token
            jwt.verify(token, config.access_token_secret, function (err, decoded) {
                if (err) next()
                // if everything good, save to request for use in other routes
                req.user_from_token = decoded;
                next();
            });
        } else {
            next()
        }
    } else {
        next()
    }
}

module.exports = verifyUser