<template>
  <section id="addcourses">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">添加课程</a>
        </li>
      </ul>
    </div>
    <div class="add-form">
      <el-form status-icon :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item label="所属机构">
          <el-cascader change-on-select @change="fnSelOrgChange" v-model="selOrg" :props="props" :options="organizationList"></el-cascader>
        </el-form-item>
        <el-form-item label="课程全名" prop="fullname">
          <el-input v-model="ruleForm.fullname" placeholder="请输入课程全名"></el-input>
        </el-form-item>
        <el-form-item label="课程简称" prop="shortname">
          <el-input v-model="ruleForm.shortname" placeholder="请输入课程简称"></el-input>
        </el-form-item>
        <el-form-item label="课程编号" prop="idnumber">
          <el-input v-model="ruleForm.idnumber" placeholder="请输入课程编号"></el-input>
        </el-form-item>
        <el-form-item label="课程类型" prop="category">
          <el-select v-model="ruleForm.category" placeholder="请选择课程类型">
            <el-option v-for="item in categoryList" :key="item.id" :label="item.name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="课程课时" prop="numsections">
          <el-input v-model.number="ruleForm.numsections" placeholder="请输入课程课时"></el-input>
        </el-form-item>
        <el-form-item label="课程概要" prop="summary">
          <el-input type="textarea" v-model="ruleForm.summary" placeholder="请输入课程概要"></el-input>
        </el-form-item>
        <el-form-item label="课程封面">
          <el-upload action="" class="avatar-uploader" list-type="picture-card" :show-file-list="false"
            :on-change="beforeAvatarUpload" :auto-upload="false">
            <img v-if="coverfile.imgurl" :src="coverfile.imgurl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </section>
</template>
<script>
export default {
  data () {
    var checkNumsections = (rule, value, callback) => {
      console.log(rule, value);
      if (!value) {
        return callback(new Error('课程预定课时数不能为空'));
      }
      setTimeout(() => {
        if (!Number.isInteger(value)) {
          callback(new Error('请输入数字值'));
        } else {
          if (value < 1) {
            callback(new Error('课程预定课时数必须大于0'));
          } else {
            callback();
          }
        }
      }, 1000);
    };
    return {
      ruleForm: {
        fullname: '',
        shortname: '',
        idnumber: '',
        category: '',
        numsections: 12,
        summary: ''
      },
      rules: {
        fullname: [
          { required: true, message: '请输入课程全名', trigger: 'blur' }
        ],
        shortname: [
          { required: true, message: '请输入课程简称', trigger: 'blur' }
        ],
        idnumber: [
          { required: true, message: '请输入课程编号', trigger: 'blur' }
        ],
        category: [
          { required: true, message: '请选择课程类型', trigger: 'change' }
        ],
        numsections: [
          { validator: checkNumsections, trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请对该课程进行概要描述', trigger: 'blur' }
        ]
      },
      // 选中的机构id路劲，默认当前登录用户的机构id
      selOrg: (_ => {
        let arr = [];
        arr.push(this.Const.userMsg.pid);
        return arr;
      })(),
      // 机构下拉
      organizationList: [],
      props: {
        value: 'id',
        label: 'name',
        children: 'children'
      },
      // 课程类型
      categoryList: [],
      coverfile: {
        imgurl: '',
        file: null
      }
    }
  },
  mounted () {
    this.fnReqOrgByPrev();
    this.requestCategory();
  },
  methods: {
    submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          const loading = this.$loading({
            lock: true,
            text: 'Loading',
            spinner: 'el-icon-loading',
            background: 'rgba(0, 0, 0, 0.7)'
          });
          let requestData = Object.assign({
            module: 'service',
            controller: 'Course_Con',
            action: 'addCourse',
            department: this.selOrg[this.selOrg.length-1],
            files: this.coverfile.file
          }, this.ruleForm);
          let formData = new FormData();
          for (let key in requestData) { formData.append(key, requestData[key]); }
          let config = {headers: {"Content-Type": 'multipart/form-data'}};
          this.$ajax.post(this.Const.apiurl, formData, config).then(response => {
            loading.close();
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data ? resData.data : '添加课程成功！'});
            } else {
              this.$message({type: 'info', message: resData.data ? resData.data : '添加课程失败！'});
            }
          }).catch(error => {
            loading.close();
            this.$message({type: 'error', message: '添加课程失败，请检查本地网络！'});
          });
        } else { return false; }
      });
    },
    resetForm (formName) {
      this.$refs[formName].resetFields();
    },
    // 请求所有机构信息
    fnReqOrgByPrev () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'Organization_Con',
        action: 'getUserOrgAll'
      }).then((res) => {
        var resData = res.data;
        if (resData.status == 0) {
          this.organizationList = (_ => {
            let arr = [];
            arr.push(resData.data);
            return arr;
          })();
        } else {
          this.$message.error('请求机构数据失败！');
        }
      }).catch((error) => {
        this.$message.error('请求机构数据报错！');
      });
    },
    fnSelOrgChange () {
      
    },
    // 请求所有课程类型信息
    requestCategory () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Course_Categories_Con',
        action: 'getSonCate',
        organization: this.selOrg[this.selOrg.length-1]
      }).then(response => {
        let resData = response.data;
        if (resData.status == 0) {
          this.categoryList = resData.data;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取课程类型失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: '获取课程类型失败，请检查本地网络！'});
      });
    },
    // 上传封面图片处理
    beforeAvatarUpload (file, fileList) {
      let acceptArr = ['image/jpeg', 'image/png'];
      const isJPG =  acceptArr.indexOf(file.raw.type) >= 0;
      const isLt2M = file.size / 1024 / 1024 < 2;
      if (!isJPG) {
        this.$message.error('封面图片只能是 JPG|PNG 格式!');
      }
      if (!isLt2M) {
        this.$message.error('封面图片大小不能超过 2MB!');
      }
      if (isJPG && isLt2M) {
        this.coverfile.imgurl = URL.createObjectURL(file.raw);
        this.coverfile.file = file.raw;
      }
      return isJPG && isLt2M;
    }
  }
}
</script>
<style lang="scss">
.add-form {
  margin-top: 20px;
  width: 60%;
  margin-left: 100px;
  .el-upload--picture-card{
    width: 100px;
    height: 100px;
    line-height: 100px;
    img {
      width: 100%;
      height: 100%;
      display: block;
    }
  }
}
</style>
