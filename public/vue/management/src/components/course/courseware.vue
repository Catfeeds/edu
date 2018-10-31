<template>
  <section id="courseware">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">课件管理</a>
        </li>
      </ul>
    </div>
    <div class="courseware-wrap">
      <yk-upload :fileOldList="fileOldList" :onSelect="selectSuccess" :onDeleteOld="delectOldFile"></yk-upload>
      <div class="upload-submit">
        <el-button :disabled="newFileList.length > 0" type="primary" plain round @click="submitFile">上 传</el-button>
      </div>
    </div>
  </section>
</template>
<script>
import upload from '@/components/common/upload';
export default {
  data () {
    return {
      fileOldList: [
        /* {
          id: '12312313',
          name: 'ceshi.jpg',
          summary: '描述',
          type: 'image/jpg',
          size: '100kb',
          url: 'https://ss1.bdstatic.com/kvoZeXSm1A5BphGlnYG/skin_zoom/459.jpg?2'
        } */
      ],
      newFileList: [],
      oldFileList: [],
      allFiles: []
    }
  },
  mounted () {
    this.requestOldFile();
  },
  components: {
    "yk-upload": upload
  },
  methods: {
    // 获取历史文件 TODO
    requestOldFile () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Courseware_Con',
        action: 'queCourseware',
        id: this.$route.query.id,
        section: this.$route.query.sec ? this.$route.query.sec : ''
      }).then(response => {
        console.log(response);
        let resData = response.data;
        if (resData.status == 0) {
          this.fileOldList = resData.data;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取图片列表失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: error.message});
      });
    },
    selectSuccess (fileList) {
      this.allFiles = fileList;
      console.log('selectSuccess', fileList);
    },
    delectOldFile (fileid) {

    },
    // 文件上传
    submitFile () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      for (let i = 0, item; item = this.allFiles[i]; i++) {
        if (item.id) {
          this.oldFileList.push(item);
        } else {
          this.newFileList.push(item);
        }
      }
      let params = {
        module: 'service',
        controller: 'Courseware_Con',
        action: 'addCourseware',
        flag: 1,
        course_sections: this.$route.query.sec,
        course: this.$route.query.id,
        msglist: (_ => {
          let arr = [];
          for (let i = 0, item; item = this.newFileList[i]; i++) {
            arr.push({file_hash: item.file.hash, summary: item.summary});
          }
          return JSON.stringify(arr);
        })(),
        files: (_ => {
          let arr = [];
          for (let i = 0, item; item = this.newFileList[i]; i++) {
            arr.push(item.file);
          }
          return arr;
        })()
      };
      let formdata = new FormData();
      for (let key in params) {
        if (key == 'files') {
          for (let j = 0, jtem; jtem = params['files'][j]; j++) {
            formdata.append('files[]', jtem);
          }
        } else {
          formdata.append(key, params[key]);
        }
      }
      let config = {headers: {"Content-Type": 'multipart/form-data'}};
      this.$ajax.post(this.Const.apiurl, formdata, config).then(response => {
        loading.close();
        let resData = response.data;
        if (resData.status == 0) {
          this.$message({type: 'success', message: resData.data ? resData.data : '上传成功！'});
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '上传失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: error.message});
      });
    }
  }
}
</script>
<style lang="scss" scoped>
.courseware-wrap {
  margin: 20px auto;
}
.upload-submit {
  text-align: center;
  margin-top: 20px;
}
</style>
