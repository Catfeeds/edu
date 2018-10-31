<template>
  <div class="up-file-wrap">
    <div class="up-btn">
      <input type="file" multiple @change="fnDealFile" class="btn-file">
      <el-button size="small" type="primary">点击上传</el-button>
    </div>
    <div class="preview">
      <ul class="preview-list">
        <li v-for="(item, index) in imglist" :key="index"><a :href="item.url" target="_blank"><img :src="item.url" alt=""></a><span class="name">{{ item.name }}</span></li>
      </ul>
    </div>
    <hr>
    <el-upload
      class="upload-demo"
      ref="upload"
      :multiple="true"
      :data="upLoadData"
      action="https://jsonplaceholder.typicode.com/posts/"
      :on-preview="handlePreview"
      :on-remove="handleRemove"
      :before-upload="handleBeforeUpload"
      :http-request="upLoadRequest"
      :file-list="imglist"
      :auto-upload="false"
      list-type="picture">
      <el-button slot="trigger" size="small" type="primary">选取文件</el-button>
      <el-button style="margin-left: 10px;" size="small" type="success" @click="submitUpload">上传到服务器</el-button>
      <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
    </el-upload>
    <hr>
    <yk-upload
      :object="fileObj"
      :limit="limit"
      :file-old-list="fileOldList"
      :max-file-size="maxFileSize"
      :on-select="handleSelected"
      :on-delete-old="handleDeleteOld"></yk-upload>
  </div>
</template>

<script>
  import ykUpload from '@/components/upload';
  export default {
    data () {
      return {
        imglist: [
          {
            name: 'food.jpeg',
            url: 'https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png'
          }
        ],
        upLoadData: {
          module: 'Service',
          controller: 'asdasd',
          action: '123124',
          page: 1,
          rows: 10
        },
        hashlist: [],
        limit: 5,
        maxFileSize: 1024 * 1024 * 5,
        fileObj: {},
        fileOldList: [
          {
            id: '123123123',
            url: 'https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png',
            name: 'girl.jpg',
            file: null,
            hash: '',
            size: '',
            summary: '描述'
          }
        ]

      }
    },
    props: {},
    components: {
      "yk-upload": ykUpload
    },
    methods: {
      fnDealFile (e) {
        console.log(e.target.files);
      },
      submitUpload() {
        this.$refs.upload.submit();
      },
      handleRemove(file, fileList) {
        console.log('handleRemove', file, fileList);
      },
      handlePreview(file) {
        console.log('handlePreview', file);
        window.open(file.url, file.name);
      },
      handleBeforeUpload (file) {
        this.getFileMd5Hash(file);
        console.log('handleBeforeUpload');
      },
      upLoadRequest (request) {
        console.log('自定义上传', request);
        console.log(request.file, request.file.name, request.file.uid);
      },
      getFileMd5Hash (file) {
        const This = this;
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
            This.hashlist.push(file.hash);
          }
        };
        fileReader.onerror = function () {
          return false;
        };
        function loadNext() {
          var start = currentChunk * chunkSize, end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;
          fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
        }
        loadNext();
      },
      handleSelected (fileList) {
        console.log('handleSelected-fileList', fileList, this.fileObj);
      },
      handleDeleteOld (fileId) {
        console.log('删除历史文件', fileId);
      }
    }
  }
</script>

<style lang="scss" scoped>
.up-file-wrap {
  .up-btn {
    position: relative;
    width: 80px;
    height: 40px;
    margin-bottom: 10px;
    .btn-file, button {
      position: absolute;
      top: 0;
      left: 0;
      width: 80px;
      height: 40px;
    }
    .btn-file {
      z-index: 101;
      opacity: 0.0009;
    }
    button {
      z-index: 100;
    }
  }
  .preview {
    li {
      height: 50px;
      img {
        height: 50px;
        width: 50px;
      }
    }
  }
}
</style>
