var http = {
    post: function (url, params, successCallback) {
        $.post(url, params, function (data) {
            if (data.code == 200) {
                successCallback(data.data);
            } else {
                alert(data.msg);
            }
        })
    }
};