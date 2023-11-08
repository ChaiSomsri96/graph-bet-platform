var db = require('../utils/db')

var list = function () {
    const whereItems = [{
        key: 'faq_category.DEL_YN',
        val: 'N'
    }];
    let showItems = [
        'faqs.ID',
        'faqs.TYPE',
        'faqs.QUESTION',
        'faqs.ANSWER',
        'faqs.`ORDER`',
        'faqs.DEL_YN as faqs_del_yn',
        'faq_category.ID as faq_category_id',
        'faq_category.`NAME` as CATEGORY'
    ]
    return db.list(db.statement('select' + ' ' + showItems.join(',') + ' ' + 'from', 'faq_category left join faqs on faqs.DEL_YN = "N" and faq_category.ID = faqs.TYPE', '', db.lineClause(whereItems, 'and'), 'order by faq_category.`ORDER` ASC, faqs.`ORDER` ASC'))
}

module.exports = {
    list
}