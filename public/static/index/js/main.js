/*
    主页
*/

//校验cookie保存时间
/*$(function(){
    $(document).ready(function(){
        if ($.cookie('userMsg') && JSON.parse($.cookie('userMsg')) !== null && window.localStorage.getItem('userPrivilege')) {
            var userData = JSON.parse( $.cookie('userMsg') );
            var date = new Date();
            date.setTime(date.getTime() + (1 * 1 * 60 * 60 * 1000));// 当前时间过后的一个小时过期
            $.cookie('userMsg', userData, {expires: date, path: '/'});
        } else {
            window.location.href = `${API_ENV.API_ProjectRoot}index/index/login`;
        }
    });
});*/

// 显示用户信息
$(function(){
    $(document).ready(function(){
        window.userMsg = JSON.parse( $.cookie('userMsg') );
        // console.log(userMsg);
        var name = $('.side-nav .user-msg .name');
        var avatar = $('.side-nav .user-msg img');
        name.text(window.userMsg.username+' '+window.userMsg.role);
        if(window.userMsg.picture){
            avatar.attr('src', window.userMsg.picture);
        }
    });
});

// 登出
$(function(){
    $('header .top-status span a').click(function(){
        $.ajax({
            url: `${API_ENV.API_ProjectRoot}index/index/logout`,
            type: 'POST',
            data: '',
            success: function(data){
                if (data){
                    $.cookie("userMsg", null, {path: '/'});
                    window.localStorage.clear();
                    window.location.href = `${API_ENV.API_ProjectRoot}index/index/login`;
                }else{
                    layer.msg("登出失败！请重试", {icon: 2, time: 1500});
                }
            },
            error: function(err){
                layer.msg("登出失败！请重试", {icon: 2, time: 1500});
            }
        });
        
    });
});


/* 侧边栏导航切换 */
$(function () {
    var asideNav = $('.side-nav .fn-list li');
    asideNav.click(function () {
        $(this).addClass('hover').siblings().removeClass('hover');
    });
});






// 公共函数
/* index 内容切换大标签 */
function mainTitleCut() {
    var classifyLi = $('.classify ul li');
    var classifyLiCkeck = $('.classify ul li:nth-child(1) .before');
    classifyLi.eq(0).find('a').addClass('click');
    classifyLi.click(function () {
        var w = $(this).offset().left - $('.classify ul').offset().left;
        var aWidth = $(this).find('a').width();
        var that = $(this);
        classifyLiCkeck.stop().animate({
            left: w + 'px',
            width: (aWidth)+'px'
        }, 300, 'swing', function () { that.find('a').addClass('click'); });
        for (var i = 0; i < classifyLi.length; i++) {
            classifyLi.eq(i).find('a').removeClass('click');
        }
    });
}



/* 数组删除指定元素 */
Array.prototype.removeByValue = function(val){
    // 比较两个json对象是否相等
    function compare(objA, objB) {
        function isObj(object) {// 判断是不是对象
            return object && typeof(object) == 'object' && Object.prototype.toString.call(object).toLowerCase() == "[object object]";
        }
        function isArray(object) {// 判断是不是数组
            return Object.prototype.toString.apply(object) === '[object Array]';
        }
        function getLength(object) {// 获取对象的长度
            var count = 0;
            for(var i in object) count++;
            return count;
        }
        function CompareObj(objA, objB, flag) {
            for(var key in objA) {
                if(!flag) //跳出整个循环
                    break;
                if(!objB.hasOwnProperty(key)) {
                    flag = false;
                    break;
                }
                if(!isArray(objA[key])) { //子级不是数组时,比较属性值
                    if(objB[key] != objA[key]) {
                        flag = false;
                        break;
                    }
                } else {
                    if(!isArray(objB[key])) {
                        flag = false;
                        break;
                    }
                    var oA = objA[key],
                        oB = objB[key];
                    if(oA.length != oB.length) {
                        flag = false;
                        break;
                    }
                    for(var k in oA) {
                        if(!flag) //这里跳出循环是为了不让递归继续
                            break;
                        flag = CompareObj(oA[k], oB[k], flag);
                    }
                }
            }
            return flag;
        }
        if(!isObj(objA) || !isObj(objB)) return false; //判断类型是否正确
        if(getLength(objA) != getLength(objB)) return false; //判断长度是否一致
        return CompareObj(objA, objB, true); //默认为true
    }
    for (var i = this.length - 1; i >= 0; i--) {
        if (this[i] === val || compare(val, this[i])) {
            this.splice(i, 1);
            break;
        }
    }
};


/* 课程库首页鼠标切换 */
function rollList(){
    $(".courselib-list ul li .msg h2").hover(function(){
        var This = $(this);
        This.siblings('.wrap-port').find('.detailes').stop().animate({marginTop: '-80px'}, 300);
    }, function(){
        var This = $(this);
        This.siblings('.wrap-port').find('.detailes').stop().animate({marginTop: '0px'}, 300);
    });
};



/* 页面翻页 (总页数, 页码最多显示的条数， 翻页html结构的ul)   return 当前切换的页码 */
function liAppendDom(allpage, maxPage, ul, pagesign, fn){
    pagesign.text(" " + allpage + " ");
    ul.append( pageNum(allpage, maxPage) );
    var li = ul.find("li");
    var linum = ul.find("li.num");
    var liprevfrist = ul.find("li.prev, li.first_page");
 //   var limore = ul.find("li.more");
 //   var liprevnext = ul.find("li.prev, li.next");
    linum.eq(0).addClass("hover");
    liprevfrist.find('a').addClass('disable');
    fn && fn();
    function pageNum(allPage, maxPage){
        var html = '';
        if(allPage <= 0){
            return html = "";
        } else if(allPage > maxPage){
            for(var i=0;i<maxPage-1;i++){
                html += "<li class='num' name='" + (i + 1) + "'><a href='javascript:void(0)'>"+ (i+1) +"</a></li>";
            }
            html = "<li class='first_page'><a href='javascript:void(0)'>首页</a></li><li class='prev'><a href='javascript:void(0)'>上一页</a></li>"+html+"<li class='more'><a href='javascript:void(0)'>...</a></li><li class='next'><a href='javascript:void(0)'>下一页</a></li><li class='last_page'><a href='javascript:void(0)'>尾页</a></li>";
        }else {
            for(var i=0;i<allPage;i++){
                html += "<li class='num' name='" + (i + 1) + "'><a href='javascript:void(0)'>"+ (i+1) +"</a></li>";
            }
            html = "<li class='first_page'><a href='javascript:void(0)'>首页</a></li><li class='prev'><a href='javascript:void(0)'>上一页</a></li>"+html+"<li class='next'><a href='javascript:void(0)'>下一页</a></li><li class='last_page'><a href='javascript:void(0)'>尾页</a></li>";
        }
        return html;
    };
};
// 上一页 下一页点击的页数计算
function prevNext(other, index, allPage){
    if(other == 'prev'){// 上一页
        index--;
        if(index <= 1){
            return index = 1;
        }
    }else {
        index++;
        if(index >= allPage){
            return index = allPage;
        }
    }
    return index;
}
//页数变化
function alterPage(next_page, last_page, max_page, liprev, linum, limore) {
    var more = "<li class='more'><a href='javascript:void(0)'>...</a></li>";
    linum.remove();
    limore.remove();
    var html = '';
    if (last_page > max_page)
    {
        if (next_page == 1)
        {
            for (var i = 1; i < max_page; i++)
            {
                html += "<li class='num' name='" + i + "'><a href='javascript:void(0)'>"+ i +"</a></li>";
            }
            html +=  more;
        } else if (next_page < (max_page - 1))//more在最后
        {
            for (var i = 1; i < max_page; i++)
            {
                 html += "<li class='num' name='" + i + "'><a href='javascript:void(0)'>"+ i +"</a></li>";
            }
            html += more;
        } else if (next_page >= (max_page - 1) && next_page < (last_page - 2))//more在最前和最后
        {
            html = more;
            for (var i = (next_page - 1); i <= (next_page + 1); i++)
            {
                html += "<li class='num' name='" + i + "'><a href='javascript:void(0)'>"+ i +"</a></li>";
            }
            html += more;
        } else //more在最前
        {
            html = more;
            for (var i = (last_page - 3); i <= last_page; i++)
            {
                html +="<li class='num' name='" + i + "'><a href='javascript:void(0)'>"+ i +"</a></li>";
            }
        }
    } else {
        for (var i = 1; i <= last_page; i++)
        {
            html += "<li class='num' name='" + i + "'><a href='javascript:void(0)'>"+ i +"</a></li>";
        }
    }
    liprev.after(html);
 //   callback && callback();
}
// hover的添加
function togClass(index, allPage, linum, limore){
    
    switch(index){
        case 1:
            addHover(0, linum);
            break;
        case 2:
            addHover(1, linum);
            break;
        case 3:
            addHover(2, linum);
            break;
        case 4:
            addHover(3, linum);
            break;
        case allPage:
            addHover(linum.length-1, linum);
            break;
        default :
            limore.eq(0).addClass("hover").siblings('li').removeClass("hover");
            break;
    }
}
function addHover(index, num){
    index.find("li.num[name = " + num + "]").addClass("hover").siblings().removeClass("hover");
  //  linum.eq(index).addClass("hover").siblings().removeClass("hover");
}
// 上一页 下一页的隐藏和显示
function showHide(index, li, allPage){
    if(index === 1){
        li.eq(0).find('a').addClass("disable");
        li.eq(1).find('a').addClass("disable");
        li.eq(li.length-1).find('a').removeClass("disable");
        li.eq(li.length-2).find('a').removeClass("disable");
    }else if(index === allPage){
        li.eq(li.length-2).find('a').addClass("disable");
        li.eq(li.length-1).find('a').addClass("disable");
        li.eq(0).find('a').removeClass("disable");
        li.eq(1).find('a').removeClass("disable");
    } else {
        li.find('a').removeClass('disable');
    }
}




// 机构选择
// 重选清空后面的控件
function clearSel(thisIndex, index){
    var oldSelArr = [];
    for(var i=thisIndex+1;i<= index;i++){
        $("#organization-"+i+"").off('change').remove();
    }
    index = thisIndex;
}
function getHtml(myjson, dom){
    var datalist = myjson.datalist,
        idName = myjson.idName;
    var options = "<option value=''>请选择</option>";
    for(var i=0;i<datalist.length;i++){
        options += "<option value="+ datalist[i].id +">"+ datalist[i].name +"</option>";
    }
    var select = document.getElementById(idName) ? $("#"+idName+"") : $('<select class="courseorganization con" name="department" id='+idName+'></select>');
    dom.append(select.html(options));
    myjson.fn && myjson.fn();
}


/*function funGetUrlByType(file, type){
    var url = null;
    switch(type){
        case 'image':
            url = getObjectURL(file);
            break;
        case 'text':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_text.jpg";
            break;
        case 'word':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_word.png";
            break;
        case 'excel':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_excel.png";
            break;
        case 'ppt':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_ppt.png";
            break;
        case 'pdf':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_pdf.jpg";
            break;
        case 'zip':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_zip.png";
            break;
        case 'wps':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_wps.png";
            break;
        case 'psd':
            url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_psd.png";
            break;
        default :
            url = url;
            break;
    }
     // 把文件转换成可读URL
    function getObjectURL(file) {
        var url = null;
        if (window.createObjectURL != undefined) { // basic
            url = window.createObjectURL(file);
        } else if (window.URL) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }
    return url;
};*/




/*  弹窗发布作业           作业id     作业所属的课堂id  作业所属的章节id*/
function fnReqIssueLayer(homeworkid, classroomid, sectionid){
    var html = '<div class="layer-issue" style="padding: 20px;"><form class="layui-form" action=""><div class="layui-form-item"><label class="layui-form-label" >提交次数</label><div class="layui-input-block" style="width: 600px;"><input lay-filter="number" type="radio" name="number" value="1" title="1"><input type="radio" lay-filter="number" name="number" value="2" title="2"><input lay-filter="number" type="radio" name="number" value="5" title="5"><input lay-filter="number" type="radio" name="number" value="10" title="10"><input lay-filter="number" type="radio" name="number" value="-1" checked title="不限制提交次数"></div></div><div class="layui-form-item"><label class="layui-form-label">作业满分值</label><div class="layui-input-inline"><select name="score" lay-filter="score"><option value="1" selected>1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select></div></div><div class="layui-form-item"><label class="layui-form-label">提交时间</label><div class="layui-inline"><input style="width: 400px;" name="time" type="text" lay-verify="required" placeholder="请选择提交作业时间段" class="layui-input" id="timerange"></div></div><div class="layui-form-item layui-form-text"><label class="layui-form-label">发布描述</label><div class="layui-input-block" style="width: 600px;"><textarea name="desc" placeholder="请输入内容" class="layui-textarea"></textarea></div></div></form></div>';
    layui.use(['laydate', 'form', 'layer'], function(){
        var This = this;
        var layer = layui.layer;
        layer.open({
            type: 1,
            title: '发布作业',
            content: html,
            area: ['800px', '450px'],
            btn: ['发布', '取消'],
            anim: 2,
            isOutAnim: true,
            success: function(layero, index){
                layui.use(['laydate', 'form', ], function(){
                    var laydate = layui.laydate;
                    var form = layui.form;
                    laydate.render({
                        elem: '#timerange', //指定元素
                        type: 'datetime',
                        range: true,
                        min: 0,
                        theme: 'grid',
                        calendar: true,
                        done: function(value, date, endDate){
                            // console.log(value); //得到日期生成的值，如：2017-08-18
                            // console.log(date); //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
                            // console.log(endDate); //得结束的日期时间对象，开启范围选择（range: true）才会返回。对象成员同上。
                            layero.timerange = function(){
                                return {
                                    starttime: value.split(' - ')[0].replace(/-/g, '/'),
                                    endtime: value.split(' - ')[1].replace(/-/g, '/')
                                };
                            }();
                        }
                    });
                    form.on('radio(number)', function(data){
                        layero.dosubmit = data.value;
                    });
                    form.render();
                });
            },
            yes: function(index, layero){
                if(layero.timerange && layero.timerange.endtime && layero.timerange.starttime){
                    layer.open({
                        type: 3,
                        icon: 0,
                        success: function(){
                            var data = {
                                module: 'service',
                                controller: 'Data_Request',
                                action: 'crRequest',
                                id: homeworkid,
                                classroomid: classroomid,
                                sectionid: sectionid,
                                dosubmit: layero.dosubmit || '-1',
                                timeopen: layero.timerange.starttime,
                                timeclose: layero.timerange.endtime,
                                attention: layero.find('textarea[name="desc"]').val(),
                                score: layero.find('select[name="score"]').val()
                            };
                            // 请求后台发布作业
                            setTimeout(function(){
                                layer.msg('发布成功！', {icon: 6, time:2000}, function(){
                                    layer.closeAll();
                                });
                            }, 4000);
                        }
                    });
                } else {
                    layer.msg('请先选择作业提交时间段', {icon: 5, time:2000});
                }
            },
            btn2: function(){
                layer.closeAll();
            }
        });
    }); 
}