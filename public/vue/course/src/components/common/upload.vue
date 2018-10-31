<template>
  <div class="yk-upload">
    <div class="yk-upload-btn">
      <input type="file" :multiple="multiple" @change="handleChange" class="btn-file">
      <button class="yk-btn"><i class="el-icon-circle-plus"></i> 添加文件</button>
    </div>
    <div class="yk-preview">
      <ul>
        <transition-group name="fade">
          <li v-for="(item, index) in fileList" :key="index">
            <img v-if="item.type === 'img'" :src="item.url" class="yk-picture-img" alt="">
            <i v-else class="el-icon-document yk-picture-img"></i>
            <span class="yk-file-name">{{ item.name }}</span>
            <textarea v-model="item.summary" class="yk-summary"></textarea>
            <label @click="handleDeleteNew(item.file)" v-if="item.status == 'ready'" class="yk-upload-list-status label-ready">
              <i class="el-icon- yk-icon-success"></i>
            </label>
            <label @click="handleDeleteOld(item.id)" v-else class="yk-upload-list-status label-success">
              <i class="el-icon- yk-icon-success"></i>
            </label>
          </li>
        </transition-group>
        
      </ul>
    </div>
  </div>
</template>

<script>
/*
  fileList: 文件对象数组，返回的结果对象
  fileFilter：上传文件原始数组

  // 可传参数
    fileOldList: 历史文件数组
    multiple: 是否多文件上传， 默认true
    accept: 允许文件格式，文件后缀的数组
    limit: 允许文件个数，数字类型 默认-1(不限个数)个
    maxFileSize：允许文件大小，单位字节，默认1024*1024
    errorMsg: 错误提示内容，

    onExceed：文件个数超出钩子
    onSizeError：文件大小不符钩子
    onDeleteNew：新添加的文件删除钩子
    object：文件列表所属对象
   
  // 必传参数
    onSelect：文件选择成功钩子，参数fileList
    onDeleteOld：历史文件删除钩子，参数文件id


*/
  export default {
    data () {
      return {
        fileList: [].concat(this.fileOldList),
        fileFilter: []
      }
    },
    props: {
      fileOldList: {
        type: Array,
        default: function () {
          return [];
        }
      },
      multiple: {
        type: Boolean,
        default: true
      },
      accept: {
        type: Array,
        default: function () {
          return ['jpg', 'png', 'jpeg', 'gif'];
        }
      },
      limit: {
        type: Number,
        default: -1
      },
      maxFileSize: {
        type: Number,
        default: 1024*1024
      },
      errorMsg: {
        type: Object,
        default: function () {
          return {
            fileSizeErr: '文件大小超出约束范围',
            fileNumErr: '文件个数超出允许范围',
            fileTypeErr: '文件格式不被允许'
          }
        }
      },
      onExceed: {
        type: Function,
        default: function (num) {
          this.$notify({
            title: '文件数量超出',
            message: `添加文件个数${num} - 允许文件个数${this.limit}`,
            duration: 3000
          });
        }
      },
      onSizeError: {
        type: Function,
        default: function (file, msg) {
          this.$notify({
            title: '文件大小不符',
            message: `文件大小不超过${this.formatFileSize(this.maxFileSize)} - ${file.name}(${this.formatFileSize(file.size)})`,
            duration: 3000
          });
        }
      },
      onTypeError: {
        type: Function,
        default: function (file, msg) {
          this.$notify({
            title: '文件格式不符',
            message: `不符合文件：${file.name} - (${this.accept.join('|')})`,
            duration: 3000
          });
        }
      },
      onSelect: {
        type: Function
      },
      onDeleteOld: {
        type: Function
      },
      onDeleteNew: {
        type: Function,
        default: function (file) {
          this.$notify({
            title: '文件删除提示',
            message: `删除文件 - ${file.name}`,
            duration: 3000
          });
        }
      },
      object: {
        type: Object,
        default: function () {
          return {};
        }
      }
    },
    methods: {
      // 文件控件change事件处理函数
      handleChange (e) {
        let files = e.target.files || e.dataTransfer.files;
        this.fileFilter = this.fileFilter.concat(this.filter(files));
        this.funDealFiles();
      },
      funDealFiles () {
        const self = this;
        this.fileList = this.fileList.concat(this.fileFilter.map(file => {
          self.getFileMd5Hash(file);
          return {
            status: 'ready',
            url: self.getObjectURL(file),
            name: file.name,
            file: file,
            type: (function(){
              let typename = file.name.substring(file.name.lastIndexOf(".")+1).toLowerCase();
              let arr = ['jpg', 'png', 'jpeg', 'gif'];
              return (new RegExp(`,${typename},`)).test(`,${arr.join()},`) ? 'img' : 'doc';
            })(),
            // hash: file.hash,
            size: self.formatFileSize(file.size),
            summary: ''
          };
        }));
        this.onSelect(this.fileList);
        this.object.files = this.fileList;
        this.fileFilter = [];
      },
      // 文件规则过滤
      filter (files) {
        var self = this;
        var arr = [];
        if (this.limit != -1 && (files.length > this.limit || (files.length + this.fileList.length) > this.limit)) {
            // 文件允许上传个数受限
            this.onExceed(files.length + this.fileList.length, this.errorMsg.fileNumErr);
        } else {
          for(var i = 0, file; file = files[i]; i++){
            console.log(123123);
            if (file.size > self.maxFileSize) {
              self.onSizeError(file, self.errorMsg.fileSizeErr);
            } else {
              if (self.checkFileType(file.name)) {
                self.onTypeError(file, self.errorMsg.fileTypeErr);
              } else {
                arr.push(file);
              }
            }
          }
        }
        return arr;
      },
      // 检查文件格式
      checkFileType (fileName) {
        let type = fileName.substring(fileName.lastIndexOf(".")+1).toLowerCase();
        return (new RegExp(`,${type},`)).test(`,${this.accept.join()},`) ? false : true;
      },
      // 获取文件本地预览地址
      getObjectURL (file) {
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
      // 文件大小格式化
      formatFileSize (bytes) {
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
      // 计算问阿金md5hash值
      getFileMd5Hash (file) {
        var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice,
          chunkSize = 2097152,
          chunks = Math.ceil(file.size / chunkSize),
          currentChunk = 0,
          spark = new SparkMD5.ArrayBuffer(),
          fileReader = new FileReader();

        fileReader.onload = function (e) {
          spark.append(e.target.result);
          currentChunk++;
          if (currentChunk < chunks) {
            loadNext();
          } else {
            file.hash = spark.end();
          }
        };

        fileReader.onerror = function () {
          return false;
        };

        function loadNext() {
          var start = currentChunk * chunkSize,
            end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;

          fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
        }
        loadNext();
      },
      // 删除新添加的文件
      handleDeleteNew (file) {
        this.fileList = this.fileList.filter(item => {
          return (item.file && file === item.file) ? false : true;
        });
        this.fileFilter = this.fileFilter.filter(item => {
          return (item === file) ? false : true;
        });
        this.object.files = this.fileList;
        this.onDeleteNew(file);
      },
      // 删除已上传的文件，通过文件id
      handleDeleteOld (fileId) {
        this.fileList = this.fileList.filter(file => {
          return (file.id && file.id == fileId) ? false : true;
        });
        this.onDeleteOld(fileId);
      }
    }
  }
</script>

<style lang="scss" scoped>
.yk-upload {
  .yk-btn {
    display: inline-block;
    line-height: 1;
    white-space: nowrap;
    cursor: pointer;
    border: 1px solid #dcdfe6;
    -webkit-appearance: none;
    text-align: center;
    box-sizing: border-box;
    outline: none;
    margin: 0;
    transition: .1s;
    font-weight: 500;
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    padding: 10px 15px;
    font-size: 14px;
    border-radius: 4px;
    color: #fff;
    background-color: #409eff;
    border-color: #409eff;
    font-size: 12px;
  }
  .yk-upload-btn {
    position: relative;
    height: 40px;
    .btn-file {
      position: absolute;
      z-index: 1;
      opacity: 0;
      width: 80px;
      display: block;
      height: 34px;
      cursor: pointer;
      &:hover + .yk-btn {
        background: #66b1ff;
        border-color: #66b1ff;
        color: #fff;
      }
    }
    .yk-btn {
      cursor: pointer;
      position: absolute;
      top: 0;
      left: 0;
    }
  }
  .yk-preview {
    li {
      overflow: hidden;
      z-index: 0;
      background-color: #fff;
      border: 1px solid #c0ccda;
      border-radius: 6px;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      margin-top: 10px;
      padding: 10px 10px 10px 90px;
      height: 92px;
      position: relative;
      transition: all 0.3s ease-out;
      &:hover{
        color: #13ce66;
      }
      .yk-picture-img {
        vertical-align: middle;
        display: inline-block;
        width: 70px;
        height: 70px;
        float: left;
        position: relative;
        z-index: 1;
        margin-left: -80px;
        text-align: center;
        line-height: 70px;
        font-size: 50px;
      }
      .yk-file-name {
        line-height: 70px;
        float: left;
        width: 200px;
      }
      .yk-upload-list-status {
        position: absolute;
        right: -17px;
        top: -7px;
        width: 46px;
        height: 26px;
        cursor: pointer;
        &.label-success{
          background: #13ce66;
          text-align: center;
          -webkit-transform: rotate(45deg);
          transform: rotate(45deg);
          -webkit-box-shadow: 0 1px 1px #ccc;
          box-shadow: 0 1px 1px #ccc;
          i{
            color: #fff;
            font-size: 12px;
            margin-top: 12px;
            -webkit-transform: rotate(-45deg);
            transform: rotate(-45deg);
            &::before{
              content: "\E611"
            }
          }
        }
        &.label-ready {
          text-align: center;
          i{
            color: #606266;
            font-size: 12px;
            margin-top: 12px;
            margin-right: 12px;
            &::before{
              content: "\E60F"
            }
          }
        }
      }
      .yk-summary {
        margin-top: 10px;
        margin-left: 20px;
        resize: vertical;
        padding: 5px 15px;
        line-height: 1.5;
        box-sizing: border-box;
        width: 40%;
        font-size: inherit;
        color: #606266;
        background-color: #fff;
        background-image: none;
        border: 1px solid #dcdfe6;
        border-radius: 4px;
        transition: border-color .2s cubic-bezier(.645,.045,.355,1);
        &:focus {
          outline: none;
          border-color: #409eff;
        }
      }
    }
  }
  .fade-enter-active {
    transition: all .3s ease;
  }
  .fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
  }
  .fade-enter, .fade-leave-active {
    padding-top: 20px;
    opacity: 0;
  }
}
</style>
