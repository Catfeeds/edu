<template>
  <div class="bankindex paperbank">
    <div class="form-item clearfix">
      <label class="form-label"><i class="sign">*</i>上传文件</label>
      <div class="uploadexcel">
        <input ref="upexcel" type="file" id="upexcel">
        <el-button plain icon="el-icon-upload"></el-button>
      </div>
      <div class="form-mid word-aux">只允许excel文件</div>
    </div>
  </div>
</template>

<script>
import '../../static/js/upload.js';
export default {
  data() {
    let thisVue = this;
    return {
      
    };
  },
  mounted() {
    console.log(window.myFileUpload);
    this.fnUpFile();
  },
  methods: {
    fnUpFile () {
      let This = this;
      const options = {
        fileInput: This.$refs.upexcel,
        acceptFileType: ['ms-excel'],
        onfailMsgDeal (file, msg) {
          console.log('file, msg', file, msg);
        },
        onSelect () {
          console.log('fileFilter', this.fileFilter);
        }
      };
      myFileUpload = $.extend(window.myFileUpload, options);
      myFileUpload.init();
    }
  }
};
</script>

<style lang="scss">
/* 这里可以写css预编译语言 */
.uploadexcel {
  position: relative;
  #upexcel {
    position: absolute;
    top: 0;
    left: 0;
    width: 56px;
    height: 40px;
    display: inline-block;
    z-index: 999;
    opacity: 0.009;
    cursor: pointer;
    &:hover + .el-button{
      background: #fff;
      border-color: #409EFF;
      color: #409EFF;
    }
  }
}
</style>
