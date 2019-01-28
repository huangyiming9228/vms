function ui_alert(msg, callback) {
    bootbox.dialog({
        message : "<h5>" + msg + "<h5>",
        buttons : {
            danger : {
                label : "确定",
                className : "btn-primary",
                callback : function() {
                    if (callback != undefined) {
                        callback();
                    }
                }
            }
        }
    });
}

function ui_confirm(msg, callback,callback_para) {
    bootbox.dialog({
        message : "<h5>" + msg + "<h5>",
        buttons : {
            main : {
                label : "取消",
                className : "btn-default",
                callback : function() {
                    //
                }
            },
            danger : {
                label : "确定",
                className : "btn-primary",
                callback : function() {
                    callback(callback_para);
                }
            }
        }
    });
}