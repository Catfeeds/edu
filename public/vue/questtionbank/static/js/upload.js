/*
    多文件多类型，可预览文件上传
*/

function myFileUpload () {
    return {
        fileInput: null,                     // html5文件上传控件
        submitBtn: null,                   // 提交按钮
        dragDrop: null,                    // 拖拽敏感区域
        infoPreview: null,             // 文件预览的大容器  目前只能是table容器
        isSummary: true,                 // 是否 为每个文件进行描述
        fileNum: 1,                     // 文件允许上传个数
        summary: [],                   // 存放文件描述内容
        clearBtn: null,                   // 清除文件列表
        url: null,                     // 文件上传地址
        fileFilter: [],                    //过滤后的文件数组
        module: null,                       // 服务器上传的私有字段
        controller: null,
        action: null,
        otherData: null,                       // 数据提交的字段数据
        //                                          压缩          word文档             ppt              excel文档                          txt文档   pdf文档
        acceptFileType: ['image', 'text', 'word', 'excel', 'wps', 'audio', 'video', 'pdf', 'ppt', 'zip', 'psd'],            // 允许上传的文件类型
        maxFileSizeImg: 1024 * 1024 * 5,// 图片最大5M
        minFileSizeImg: 0,              // 图片最小0
        maxFileSizeMedia: 1024 * 1024 * 50, // 媒体文件(audio,video,ppt,pdf,psd,zip)最大50M
        maxFileSizeMedia: 0,            // 媒体文件最小0
        errorMsg: {                     // 文件出错提示信息
            fileSizeErr: '文件大小超出约束范围',
            fileTypeErr: '文件类型不被允许',
            fileNumErr: '文件个数超出允许范围'
        },
        onfailMsgDeal: function(file, msg){
            /*
               file :  错误文件
               msg  :  提示信息
            */
        },   // 文件被过滤后的回调
        onSelect: function() {},        //文件选择后 // 生成预览html结构 预览文件地址，注册删除按钮的删除事件
        onDelete: function() {},        //文件删除后
        onDragOver: function() {},        //文件拖拽到敏感区域时
        onDragLeave: function() {},    //文件离开到敏感区域时
        onUploadFile: function(){},     // 启动上传
        onProgress: function() {},        //文件单个文件上传进度
        onAllProgress: function(){},       // 所有文件总上传精度
        onSuccess: function() {},        //文件上传成功时
        onFailure: function() {},        //文件上传失败时,
        onComplete: function() {},        //文件全部上传完毕时
    
    
        /* 内置方法 */
        //文件拖放
        funDragHover: function(e) {
            e.stopPropagation();
            e.preventDefault();
            this[e.type === "dragover" ? "onDragOver" : "onDragLeave"].call(e.target);
            return this;
        },
        funGetFiles: function(e){
            // 取消鼠标经过样式
            this.funDragHover(e);
            // 获取文件列表
            var files = e.target.files || e.dataTransfer.files;
            // 继续添加文件
            this.fileFilter = this.fileFilter.concat(this.filter(files));
            this.funDealFiles();
            return this;
        },
        // 文件处理函数，触发预览
        funDealFiles: function(){
            var self = this;
            for (var i = 0, file; file = this.fileFilter[i]; i++) {
                //增加唯一索引值
                file.index = i;
                self.getFileMd5Hash(file);
                this.onSelect(file);
            }
            this.fnPreviewHtml();
            return this;
        },
        fnPreviewHtml () {
            var self = this;
            var el = $("<ul class='yk-preview-ul'></ul>");
            var html = '';
            for (var i = 0, file; file = this.fileFilter[i]; i++) {
                var fileUrl = this.getObjectURL(file);
                var fileType = this.getFileType(file);
                // TODO 不同的文件类型显示不同的封面
                switch (fileType) {
                    case 'image': html += "<li class='yk-preview-item'><img src="+ fileUrl +" width='100px' height='100px'><p class='yk-file-name'>"+ file.name +"</p><span id='yk-preview-delete' title='删除'>×</span></li>"; break;
                    default : html += "<li class='yk-preview-item'><p class='yk-file-name'>"+ file.name +"</p><span id='yk-preview-delete' title='删除'>×</span></li>"; break;
                }
            }
            el.append(html);
            if (this.isSummary) {
                var textarea = $('<textarea class="yk-preview-summary"></textarea>');
                var li = el.find('li');
                li.append(textarea);
            }
            $(this.infoPreview).html(el);
            el.on('click', '#yk-preview-delete', function () {
                var index = $(this).parents('.yk-preview-item').index();
                self.funDeleteFile(self.fileFilter[index]);
                // self.fnPreviewHtml();
            });
        },
        //删除对应的文件
        funDeleteFile: function(fileDelete) {
            var arrFile = [];
            for (var i = 0, file; file = this.fileFilter[i]; i++) {
                if (file != fileDelete) {
                    arrFile.push(file);
                } else {
                    this.onDelete(fileDelete);// 触发文件删除的回调
                }
            }
            for(var i=0, afile; afile = arrFile[i]; i++){// 更行文件列表的序号
                arrFile[i].index = i;
            }
            this.fileFilter = arrFile;
            if(this.fileFilter.length === 0){// 所有上传文件数组为空
                this.funRestart();
            }
            // 重置预览
            this.fnPreviewHtml();
            return this;
        },
        // 为每个文件添加文字描述
        fnAddSummary: function () {
            for (var i = 0, file; file = this.fileFilter[i]; i++) {
                this.summary.push({file_hash: file.hash, summary: $(".yk-preview-summary").eq(i).val().trim()});
            }
        },
        // 重置
        funRestart: function(){
            this.fileFilter = [];
        },
        // 文件上传
        funUploadFile: function(){
            var self = this;
            //非站点服务器上运行
            if (location.host.indexOf("sitepointstatic") >= 0) { return; }
            // 添加描述
            if (this.isSummary) {
                this.fnAddSummary();
                formData.append('summarylist', this.summary);
            }
            console.log('文件列表', this.fileFilter, this.otherData);
            var formData = new FormData();
            // 添加额外字段
            for (let item in this.otherData) {
                formData.append(item, this.otherData[item]);
            }
            // 添加文件
            for (var i = 0, file; file = this.fileFilter[i]; i++) {
                formData.append('files', file);
            }
            var xhr = new XMLHttpRequest();
            if (xhr.upload) {
                // 上传中
                xhr.upload.addEventListener("progress", function(e) {
                    self.onAllProgress(e.loaded, e.total);
                }, false);
                // 文件上传成功或是失败
                xhr.onreadystatechange = function(e) {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200) {
                            self.onSuccess(xhr.responseText);
                            self.funRestart(); // 上传成功后 初始化内容
                            self.funDeleteFile(file);
                            if (!self.fileFilter.length) {
                                //全部完毕
                                self.onComplete();
                            }
                        } else {
                            self.onFailure(file, xhr.responseText);
                        }
                    }
                };
                // 开始上传
                xhr.open('POST', self.url, true);
                // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send(formData);
            }
        },
         // 文件过滤
        filter: function(files){
            var self = this;
            var arr = []
            if (files.length > this.fileNum) {
                // this.onfailMsgDeal(files, self.errorMsg.fileNumErr);
                alert(self.errorMsg.fileNumErr);
            } else {
                for(var i = 0, file; file = files[i]; i++){
                    var type = this.getFileType(file);
                    if ($.inArray(type, this.acceptFileType) > -1) {
                        if($.inArray(type, ['image', 'text', 'word', 'excel', 'wps']) > -1){// 图片,文本文件
                            if(file.size > this.maxFileSizeImg || file.size < this.minFileSizeImg){
                                this.onfailMsgDeal(file, self.errorMsg.fileSizeErr); // 文件被过滤调的回调
                            } else {
                                arr.push(file);
                            }
                        } else if($.inArray(type, ['audio', 'video', 'pdf', 'ppt', 'zip', 'psd']) > -1) {
                            if(file.size > this.maxFileSizeMedia || file.size < this.minFileSizeMedia){
                                this.onfailMsgDeal(file, self.errorMsg.fileSizeErr);
                            } else {
                                arr.push(file);
                            }
                        }
                    } else {
                        this.onfailMsgDeal(file, self.errorMsg.fileTypeErr);// 此文件类型不允许上传
                    }
                }
            }
            return arr;
        },
        // 判断文件类型
        getFileType: function(file){
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
        },
        // 把文件转换成可读URL
        getObjectURL: function (file) {
            var url = null;
            if (window.createObjectURL != undefined) { // basic
                url = window.createObjectURL(file);
            } else if (window.URL) { // mozilla(firefox)
                url = window.URL.createObjectURL(file);
            } else if (window.webkitURL) { // webkit or chrome
                url = window.webkitURL.createObjectURL(file);
            }
            return url;
        },
        // 计算文件大小
        formatFileSize: function (bytes) {
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
        },
        // 计算文件md5的hash值    在此之前必须先引入spark-md5.js文件
        getFileMd5Hash: function(file){
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
        },
        // 入口函数
        init: function(event){
            var self = this;
    
            if (this.dragDrop) {
                this.dragDrop.addEventListener("dragover", function(e) { self.funDragHover(e); }, false);
                this.dragDrop.addEventListener("dragleave", function(e) { self.funDragHover(e); }, false);
                this.dragDrop.addEventListener("drop", function(e) { self.funGetFiles(e); }, false);
            }
    
            //文件选择控件选择
            if (this.fileInput) {
                // this.fileInput.addEventListener("change", function(e) { self.funGetFiles(e); }, false);
                self.funGetFiles(event);
            }
    
            //上传按钮提交
            if (this.submitBtn) {
                this.submitBtn.addEventListener("click", function(e) { 
                    // self.funUploadFile(e); 
                    self.onUploadFile(); 
                }, false);
            }
        }
    };
}
export {
    myFileUpload
}


