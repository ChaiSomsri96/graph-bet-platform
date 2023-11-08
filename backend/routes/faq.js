var express = require('express');
var router = express.Router();

var model = require('../model/faq');

router.post('/list', async function (req, res, next) {
    const {
        success,
        data
    } = await model.list()

    if (success) {
        var ret = []
        for (let i = 0; i < data.length; i++) {  
            if (ret.length > 0 && ret[ret.length - 1].cat_id == data[i].faq_category_id && data[i].faqs_del_yn == 'N') {
                ret[ret.length - 1].questions.push({
                    id: data[i].ID,
                    title: data[i].QUESTION,
                    answer: data[i].ANSWER
                })
            } else {
                ret.push({
                    cat_id: data[i].faq_category_id,
                    title: data[i].CATEGORY,
                    questions: data[i].ID == null || data[i].faqs_del_yn !== 'N' ? [] : [{
                        id: data[i].ID,
                        title: data[i].QUESTION,
                        answer: data[i].ANSWER
                    }]
                })
            }
        }
        return res.json({
            status: 'success',
            data: ret
        })
    } else {
        return res.json({
            status: 'fail',
            data: 'Failed to loading faq data'
        })
    }
});

module.exports = router;