/**
 * Created by KunGe on 2017/5/26.
 * 用户登录
 */

$(document).ready(function () {
    $.formValidator.initConfig({
        formID: "loginform",
        errorFocus: true,
        alertMessage: false,
        onError: function () {
            // console.log('未通过验证！');
        },
        onSuccess: function () {
            // console.log("验证通过，可以做其他事");
        }
    });

    $("#username")
        .formValidator({
            empty: false,
            onShow: "",
            onFocus: "",
            onCorrect: ""
        })
        .regexValidator({
            regExp: "username",
            dataType: "enum",
            onError: "用户名格式不正确"
        });

    $("#userpwd")
        .formValidator({
            empty: false,
            onShow: "",
            onFocus: "",
            onCorrect: ""
        }).regexValidator({
            regExp: "username",
            dataType: "enum",
            onError: "密码不正确"
        });

    $('#loginform .button').click(function () {
        init();
    });
    $(document).keydown(function(e){
        var e = e || window.event;
        switch(e.keyCode){
            case 13: init();break;
        }
    });

    function init() {
        var username = $("#username").val().trim();
        var userpwd = $('#userpwd').val().toString();
        if ($.formValidator.pageIsValid('1')) {
            $.ajax({
                url: API_ENV.API_URL,
                type: 'POST',
                headers: {
                    "Authorization": ''
                },
                data: {
                    module: 'service',
                    controller: 'Index',
                    action: 'checklogin',
                    username: username,
                    userpwd: userpwd
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status == '0') {
                        utiltool.feedback({
                            fontcolor: '#fff',
                            msg: '登录成功...',
                            bg: '#31AC65',
                            feedTime: '1000'
                        }, function () {
                            var userPrivilege = response.data.access_list;
                            delete response.data.access_list;
                            var userData = JSON.stringify( response.data);
                            var date = new Date();
                            date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));// 当前时间过后的7天过期
                            $.cookie('userMsg', userData, {path: '/'});
                            // $.cookie('apiurl', API_ENV.API_URL, {path: '/'});
                            window.localStorage.setItem('ProjectRoot', API_ENV.API_ProjectRoot);
                            window.localStorage.setItem('userMsg', userData );
                            window.localStorage.setItem('userPrivilege', window.utiltool.translateObj(userPrivilege) );
                            window.sessionStorage.setItem('auth_token', response.auth_token );
                            // window.location.href = `${API_ENV.API_ProjectRoot}index/main/index`;
                            window.location.href = `${API_ENV.API_STATIC}public/edumodel/croom/`;
                        });
                    } else {
                        utiltool.feedback({
                            fontcolor: '#fff',
                            msg: data.data || '登录失败！',
                            bg: '#BC3A3A'
                        });
                    }
                },
                error: function (err) {
                    $('body').html(err.responseText);
                    utiltool.feedback({
                        fontcolor: '#fff',
                        msg: '登录提交失败，请重新登录!',
                        bg: '#0099cc'
                    }, function () {
                        setTimeout(function () {
                            window.location.reload();
                        }, 100000);
                    });
                }
            });
        }
    };
});
