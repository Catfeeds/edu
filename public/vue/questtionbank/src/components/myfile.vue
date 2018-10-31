<template>
  <div class="up-file-wrap">
    <div class="start-up">
      <el-button @click="startUpload" type="primary" round><i class="el-icon-upload"></i> 开始上传</el-button>
    </div>
    <yk-upload
      :object="fileObj"
      :limit="limit"
      :accept="accept"
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
        msglist: [],
        limit: 5,
        maxFileSize: 1024 * 1024 * 5,
        accept: ['jpg', 'png', 'jpeg', 'gif', 'zip', 'ppt', 'pptx'],
        fileObj: {},
        fileOldList: [
          {
            id: '123123123',
            url: 'https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png',
            name: 'girl.jpg',
            type: 'doc',
            file: null,
            hash: '',
            size: '',
            summary: '描述'
          }
        ],
        newFiles: []
      }
    },
    props: {},
    components: {
      "yk-upload": ykUpload
    },
    methods: {
      handleSelected (fileList) {
        console.log('handleSelected-fileList', fileList, this.fileObj);
        this.newFiles = fileList.filter(item => item.file ? true : false);
        console.log('this.newFiles', this.newFiles);
      },
      handleDeleteOld (fileId) {
        console.log('删除历史文件', fileId);
      },
      // 开始上传
      startUpload () {
        this.msglist = this.newFiles.map(item => {
          return {
            file_hash: item.file.hash,
            summary: item.summary
          };
        });
        console.log('this.newFiles hash', this.msglist);
        // {file_hash: file.hash, summary: $(self.summary).eq(Number(file.index)).val().trim()}
      }
    }
  }
</script>

<style lang="scss" scoped>
.start-up {
  text-align: center;
  height: 50px;
  line-height: 50px;
  margin-bottom: 20px;
}
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
