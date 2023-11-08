<script>
function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
            textbox.addEventListener(event, function(eve) {
                if (inputFilter(this.value)) {
                    this.oldValue = this.value;
                    this.oldSelectionStart = this.selectionStart;
                    this.oldSelectionEnd = this.selectionEnd;
                } else if (this.hasOwnProperty("oldValue")) {
                    this.value = this.oldValue;
                    this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                } else {
                    this.value = "";
                }
            });
        });
}
function isNumber(n){
    return !isNaN(n);
}
// customize run wait color
function run_waitMe(el, num, effect)
{
    text = 'Please wait...'; fontSize = '';
    switch (num) {
        case 1:
            maxSize = '' , textPos = 'vertical';
            break;
        case 2:
            text = '' , maxSize = 30 , textPos = 'vertical';
            break;
        case 3:
            maxSize = 30 , textPos = 'horizontal' , fontSize = '18px';
            break;
    }
    el.waitMe({
        effect: effect,
        text: text,
        bg: 'rgba(86, 84, 84, 0.7)', // should be change
        color: '#e47297',
        maxSize: maxSize,
        waitTime: -1,
        source: 'img.svg',
        textPos: textPos,
        fontSize: fontSize,
        onClose: function(el) {}
    });
}
// show toast
function showToast(type, content) {
    toastr.options.closeButton = true;
    toastr.options.showDuration = 1000;
    toastr.options.positionClass = 'toast-top-right';
    toastr[type](content);
}
function getNumberFormat( _num )
{
    _num = parseInt(_num);
    _num = _num.toString();
    var num = _num.split("");
    num = num.reverse();
    _num = num.join("");
    var result = "";
    var gap_size = 3; //Desired distance between spaces

    while (_num.length > 0) { // Loop through string
        if(result != "")
            result = result + "," + _num.substring(0,gap_size); // Insert space character
        else
            result = result +  _num.substring(0,gap_size); // Insert space character
        _num = _num.substring(gap_size);  // Trim String
    }
    num = result.split("");
    num = num.reverse();
    result = num.join("");
    return result;
}
function getTimeString( timestamp ) {
    var d = new Date(timestamp * 1000);
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var yyyy = d.getFullYear();
    var hh = String(d.getHours()).padStart(2, '0');
    var min = String(d.getMinutes()).padStart(2, '0');
    return yyyy + '-' + mm + '-' + dd + ' ' + hh + ':' + min;
}
function getDateString( timestamp ) {
    var d = new Date(timestamp * 1000);
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var yyyy = d.getFullYear();
    return yyyy + '-' + mm + '-' + dd;
}
function isObject (value) {
    return value && typeof value === 'object' && value.constructor === Object;
}
function sendRequest(url, data, el, post_data_type) {
    return new Promise((resolve, reject) => {
        var ajax_obj = {
            url: url,
            type: "POST",
            data: data,
            dataType: 'json',
            beforeSend:function(){
                run_waitMe(el, 2, 'ios');
            },
            success:function(result) {
                el.waitMe('hide');
                if(result.status != 'success') {
                    if(result.hasOwnProperty('error_msg')) {
                        showToast('error', result.error_msg);
                    }
                    else {
                        showToast('error', '{{ __("message.server_error") }}');
                    }
                    reject(false);
                }
                else {
                    resolve(result);
                }
            },
            error(xhr, status, error) {
                el.waitMe('hide');
                showToast('error', '{{ __("message.network_error") }}');
                reject(false);
            }
        }
        if(post_data_type == 1) {
            ajax_obj.contentType = false;
		    ajax_obj.processData = false;
        }
        $.ajax(ajax_obj);
    })
}

function makeid(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

</script>
