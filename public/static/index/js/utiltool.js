//模态框消息提醒
//json对象：obj:模态框对象(jquery对象，必填参数)，msg:模态框内容(必填参数)，fontcolor:字体颜色(可选参数)，feedTime：提示时长
// bg:模态框的背景颜色(选填参数，缺省rgba(33,175,150,.7))，callback回调函数
/*eg:feedback({
            fontcolor: '#fff',
            msg: '模态提示框!',
            bg: '#0099cc'
        }, function(){
            console.log('1231231');
        });*/
//#c7a952警告   #C72828严重错误   rgba(33,175,150,.7)成功

//checkLoginStatus(userStatus)  检查用户是否登录。9999 未登录状态     返回true/false

// translateObj(jsonData)  将后台的用户的权限转化  将用户权限数组存到全局API_ENV.API_PrivilegeArr中
window.utiltool = {
    // 模态弹窗
    feedback: function (json, callback) {
        var obj = $("<span id='feedbox'></span>");
        var msg = json.msg,
            fontcolor = json.fontcolor || '#000',
            bg = json.bg || 'rgba(33,175,150,.7)',
            feedTime = json.feedTime || '3000';
        obj.text(msg);
        obj.css({
            'position': 'fixed',
            'z-index': 9999,
            'display': 'inline-block',
            'width': '400px',
            'height': '35px',
            'text-align': 'center',
            'background-color': bg,
            'color': fontcolor,
            'box-shadow': '0 8px 8px rgba(0,0,0,0.24), 0 0 8px rgba(0,0,0,0.12)',
            'border-radius': '2px',
            'left': '50%',
            'transform': 'translateX(-200px)'
        });
        var height = obj.height();
        obj.css({
            'top': -height + 'px',
            'line-height': height + 'px'
        });
        obj.appendTo('body');
        var fn = function () {
            return new Promise(function (resolve, reject) {
                obj.stop().animate({
                    top: '5px',
                    opacity: 1
                }, 500, 'swing');
                resolve();
            });
        };
        fn().then(function () {
            return new Promise(function (resolve, reject) {
                setTimeout(function () {
                    obj.stop().animate({
                        top: '-35px',
                        opacity: 0
                    }, 700);
                    resolve();
                }, Number(feedTime));
            });
        }).then(function () {
            setTimeout(function () {
                callback && callback();
            }, 500);
        });
    },
    // 检查状态
    checkLoginStatus: function (userStatus) {
        if (userStatus === '9999') {
            window.location.href = `${API_ENV.API_ProjectRoot}index/index/login`;
        }
    },
    // 检查cookie值是否过期，没有过期则返回用户信息
    checkCookie: function(){
        if(!$.cookie('userMsg')){
            window.sessionStorage.clear();
            window.localStorage.clear();
            window.location.href = `${API_ENV.API_ProjectRoot}index/index/login`;
            $.cookie('userMsg', null, {path: '/'});
        }
    },
    // 权限数据处理
    translateObj: function (jsonData) {
        var PrivilegeArr = [];
        for (var model in jsonData) {
            if (typeof jsonData[model] === 'object') {
                for (var con in jsonData[model]) {
                    if (typeof jsonData[model][con] === 'object') {
                        for (var me in jsonData[model][con]) {
                            if (typeof jsonData[model][con][me] === 'object') {
                                return PrivilegeArr = [];
                            } else {
                                var Privile = `${model}/${con}/${me}`;
                                PrivilegeArr.push(Privile);
                            }
                        }
                    } else {
                        return PrivilegeArr = [];
                    }
                }
            } else {
                return PrivilegeArr = [];
            }
        }
        return PrivilegeArr;
    },
    isArray: function(object){
        return Object.prototype.toString.apply(object) === '[object Array]';
    },
    transformToArr: function(data){// 把object中的typeof object字段转化为数组
        for(var i in data){
            if(typeof data[i] === 'object'){
                var arr = [];
                for(var j in data[i]){
                    window.utiltool.transformToArr(data[i][j]);
                    arr.push(data[i][j]);
                }
                data[i] = arr;
            }
        }
        return data;
    },
    //克隆对象
    Colne: function(obj){
        var newObj = new Object();
        for (var key in obj)
        {
            if(typeof obj[key] == 'object'){
                newObj[key] = window.utiltool.Colne( obj[key] );
            }else{
                newObj[key] = obj[key];//等号是赋值关系
            }
        }
        return newObj;
    },
    // 错误处理
    dealErr: function(json, fn){
        var data = {
            level: json.level,// 错误级别 1:本地请求报错|2:端口请求错误
            msg: json.msg || '貌似出了点问题！',
        };
        switch(data.level){
            case 1:
                layer.msg(data.msg, {icon: 5, time: 5000}, function(){
                    // TODO
                    fn && fn();
                });
                break;
            case 2:
                layer.msg(data.msg, {icon: 5, time: 2000}, function(){
                    // TODO
                    fn && fn();
                });
                break;
        }
    },
    // 上传文件的过滤，计算文件hash值，转换文件大小，判断文件类型
    dealFileFilter: function(json){
        var options = {
            filedata: json.filtes, // 初始文件列表
            maxFileSizeImg: json.maxFileSizeImg || 1024 * 1024 * 5,// 图片最大5M
            minFileSizeImg: json.minFileSizeImg || 0,// 图片最小0
            maxFileSizeMedia: json.maxFileSizeMedia || 1024 * 1024 * 50,// 媒体文件(audio,video,ppt,pdf,psd,zip)最大50M
            minFileSizeMedia: json.minFileSizeMedia || 0, // 媒体文件最小0
            errorMsg: {                     // 文件出错提示信息
                fileSizeErr: '文件大小超出约束范围',
                fileTypeErr: '文件类型不被允许'
            },
            msglist: json.msglist || new Array(),
            sign: json.sign,// 标记区分作业和答案
        };
        function filter(files, options){
            var self = options;
            var arr = [];
            for(var i = 0, file; file = files[i]; i++){
                var type = getFileType(file);
                if($.inArray(type, ['image', 'text', 'word', 'excel', 'wps']) > -1){// 图片,文本文件
                    if(file.size > this.maxFileSizeImg || file.size < this.minFileSizeImg){
                        // this.onfailMsgDeal(file, self.errorMsg.fileSizeErr); // 文件被过滤调的回调
                        json.onfailMsgDeal(file, self.errorMsg.fileSizeErr);
                    } else {
                        arr.push(file);
                    }
                } else if($.inArray(type, ['audio', 'video', 'pdf', 'ppt', 'zip', 'psd']) > -1) {
                    if(file.size > this.maxFileSizeMedia || file.size < this.minFileSizeMedia){
                        json.onfailMsgDeal(file, self.errorMsg.fileSizeErr);
                    } else {
                        arr.push(file);
                    }
                } else {
                    json.onfailMsgDeal(file, self.errorMsg.fileTypeErr);// 此文件类型不允许上传
                }
            }
            for(var i = 0, item; item = arr[i]; i++){
                getFileMd5Hash(item, options);
            }
            return arr;
        };
        // 判断文件类型
        function getFileType(file){
            var type = "";
            if(/image/.test(file.type)){
                type = 'image';
            } else if(/video/.test(file.type)){
                type = 'video';
            } else if(/audio/.test(file.type)){
                type = 'audio';
            } else if(/zip/.test(file.name)){// 压缩文件的type属性为空
                type = 'zip';
            } else if(/msword/.test(file.type) || /wordprocessingml.document/.test(file.type)){
                type = 'word';
            } else if(/ms-powerpoint/.test(file.type) || /presentationml.presentation/.test(file.type)){
                type = 'ppt';
            } else if(/ms-excel/.test(file.type) || /spreadsheetml.sheet/.test(file.type)){
                type = 'excel';
            } else if(/plain/.test(file.type)){
                type = 'text';
            } else if(/pdf/.test(file.type)){
                type = 'pdf';
            } else if(/ms-works/.test(file.type)){
                type = 'wps';
            } else if(/psd/.test(file.name)){
                type = "psd"
            }else {
                type = 'other';
            }
            return type;
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
        // 计算文件大小
        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }
            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }
            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }
            return (bytes / 1000).toFixed(2) + ' KB';
        }
        // 计算文件md5的hash值    在此之前必须先引入spark-md5.js文件
        function getFileMd5Hash(file, options){
            var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice,
                // file = this.files[0],
                chunkSize = 2097152,                             // Read in chunks of 2MB
                chunks = Math.ceil(file.size / chunkSize),
                currentChunk = 0,
                spark = new SparkMD5.ArrayBuffer(),
                fileReader = new FileReader();

            fileReader.onload = function (e) {
                // console.log('read chunk nr', currentChunk + 1, 'of', chunks);
                spark.append(e.target.result);                   // Append array buffer
                currentChunk++;

                if (currentChunk < chunks) {
                    loadNext();
                } else {
                    // console.log('finished loading');
                    // console.info('computed hash', spark.end());  // Compute hash
                    file.hash = spark.end();
                    console.log('file.hash', file.hash);
                    options.msglist.push({
                        file_hash: file.hash,
                        summary: '',
                        sign: function(){
                            if(options.sign === 'homeworkupfile'){
                                return 'homework';
                            } else {
                                return 'answer';
                            }
                        }()
                    });
                }
            };

            fileReader.onerror = function () {
                // console.warn('oops, something went wrong.');
                return false;
            };

            function loadNext() {
                var start = currentChunk * chunkSize,
                    end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;

                fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
            }

            loadNext();
        }

        options.filedata = filter(options.filedata, options);
        var filtered = [];
        for(var i = 0, item; item = options.filedata[i]; i++){
            filtered.push({
                file: item,
                formatFileSize: formatFileSize(item.size),
                type: getFileType(item),
                msglist: options.msglist
            });
        }
        return filtered;
    },
    // 根据文件的类型，获取缩略图url
    funGetUrlByType: function(file, type){
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
            case 'audio':
                url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_audio.png";
                break;
            case 'video':
                url = API_ENV.API_STATIC +"public/static/index/images/upfile/show_video.png";
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
    },
    fnGragProfessional: function(myOption){// 给用户添加专业
        /*
        myOption = {
            pardom: $('.allpro'), // 拖拽元素的父级元素
            dragend: $('.selpro')// 拖拽的目标框
        }
        */

        // 添加和排序
        var item = myOption.pardom.children();
        for(var i=0;i<item.length;i++){
            item.eq(i).get(0).ondragstart = function(ev){
                ev = ev || window.event;
                ev.dataTransfer.setData("index", $(this).index());
                if($(this).parent().hasClass("allpro")){
                    ev.dataTransfer.setData("isSel", 1);// 判断是否是拖拽添加
                } else {
                    ev.dataTransfer.setData("isSel", 0);
                }
                ev.dataTransfer.effectAllowed = "move";//设置拖拽中鼠标类型
                // this.style.backgroundColor = "#0ff";
            }
            item.eq(i).get(0).ondrag = function(ev){
                ev = ev || window.event;
                // this.style.backgroundColor = "#f63";
                // this.innerText = "拖拽中";
            }
            item.eq(i).get(0).ondragend = function(ev){
                ev = ev || window.event;
                // this.style.backgroundColor = "#f90";
                // this.innerText = "拖拽结束";
            }
        }
        //目标元素事件方法
        myOption.dragend.get(0).ondragenter = function(){//进入目标元素
            // this.innerText = "可以将文件拖拽到这里";
            // this.style.backgroundColor = "#990";
        }
        myOption.dragend.get(0).ondragover = function(ev){//进入目标元素到离开目标元素之间，连续触发
            ev = ev || window.event;//兼容
            ev.preventDefault();//阻止默认事件，ondrop事件需要
            // this.innerText += "元素已拖拽到目标位置";
            // this.style.backgroundColor = "#f33";
        }
        myOption.dragend.get(0).ondragleave = function(){//离开目标元素
            // this.innerText += "元素已离开目标位置";
        }
        myOption.dragend.get(0).ondrop = function(ev){//释放元素
            ev = ev || window.event;
            var index = ev.dataTransfer.getData('index');
            if (ev.dataTransfer.getData('isSel') === '1'){// 添加
                myOption.pardom.children().eq(index).remove().appendTo(myOption.dragend);
            } else {// 排序
                myOption.dragend.children().eq(index).remove().appendTo(myOption.dragend);
            }
        }


        // 移除操作
        var itemend = myOption.dragend.children();
        for(var i=0;i<itemend.length;i++){
            itemend.eq(i).get(0).ondragstart = function(ev){
                ev = ev || window.event;
                ev.dataTransfer.setData("index", $(this).index());
                ev.dataTransfer.effectAllowed = "move";//设置拖拽中鼠标类型
            }
            itemend.eq(i).get(0).ondrag = function(ev){
                ev = ev || window.event;
                // this.style.backgroundColor = "#f63";
                // this.innerText = "拖拽中";
            }
            itemend.eq(i).get(0).ondragend = function(ev){
                ev = ev || window.event;
                // this.style.backgroundColor = "#f90";
                // this.innerText = "拖拽结束";
            }
        }
        //目标元素事件方法
        myOption.pardom.get(0).ondragenter = function(){//进入目标元素
            // this.innerText = "可以将文件拖拽到这里";
            // this.style.backgroundColor = "#990";
        }
        myOption.pardom.get(0).ondragover = function(ev){//进入目标元素到离开目标元素之间，连续触发
            ev = ev || window.event;//兼容
            ev.preventDefault();//阻止默认事件，ondrop事件需要
            // this.innerText += "元素已拖拽到目标位置";
            // this.style.backgroundColor = "#f33";
        }
        myOption.pardom.get(0).ondragleave = function(){//离开目标元素
            // this.innerText += "元素已离开目标位置";
        }
        myOption.pardom.get(0).ondrop = function(ev){//释放元素
            ev = ev || window.event;
            var index = ev.dataTransfer.getData('index');
            myOption.dragend.children().eq(index).remove().appendTo(myOption.pardom);
        }
    }



};





