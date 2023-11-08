var db = require('../utils/db')

var list = function () {
    const whereItems = [{
        key: 'DEL_YN',
        val: 'N'
    }];

    return db.list(db.statement('select * from', 'faq_category', '', db.lineClause(whereItems, 'and'), 'order by `ORDER` ASC'))
}

module.exports = {
    list
}