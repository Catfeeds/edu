<template>
  <div class="add-user">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">用户基本信息</a>
        </li>
      </ul>
      <div class="select fr btnarr clearfix">
        <div class="dt-buttons">
          <el-button-group>
            <el-button @click="fnGetBack" type="primary" size="small" icon="el-icon-arrow-left">返回</el-button>
            <template v-if="!$route.query.l">
              <el-button v-if="isShow" @click="isShow = false" type="primary" size="small" icon="el-icon-edit">启用编辑</el-button>
              <el-button v-else @click="isShow = true" type="primary" size="small" icon="el-icon-circle-close-outline">禁用编辑</el-button>
            </template>
          </el-button-group>
        </div>
      </div>
    </div>
    <div class="add-form">
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>账号</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.username" placeholder="请输入用户名"></el-input>
        </div>
        <div class="form-mid word-aux">登入系统所需的账号</div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>姓名</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.realname" placeholder="请输入您的姓名"></el-input>
        </div>
        <div class="form-mid word-aux">您的真实姓名</div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>用户编号</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.idnumber" placeholder="请输入用户名"></el-input>
        </div>
        <div class="form-mid word-aux">学生学号，教师/管理职工号</div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>性别</label>
        <div class="input-inline">
          <el-radio :disabled="isShow" v-model="userone.sex" label="1">男</el-radio>
          <el-radio :disabled="isShow" v-model="userone.sex" label="2">女</el-radio>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>头像</label>
        <div class="input-inline">
          <!-- <img class="avatar" v-if="userone.picture" :src="userone.picture" alt=""> -->
          <div v-if="isShow" class="showimg avatar">
            <img v-if="userone.picture" class="avatar" :src="userone.pictureUrl" alt="">
            <p v-else>{{ userone.pictureUrl }}</p>
          </div>
          <el-upload action="" class="edit-upload" v-else :on-change="uploadImg" :show-file-list="false" :auto-upload="false">
            <img v-if="userone.picture" :src="userone.pictureUrl" class="avatar">
            <i class="el-icon-plus"></i>
          </el-upload>
        </div>
      </div>
      <div v-if="!$route.query.l && !isShow" class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>用户密码</label>
        <!-- <template v-if="isShow">
          <el-button size="small" type="danger" @click="readonlyPsw">查 看</el-button>
          <div v-if="readPsw.isRead" class="form-mid word-aux">{{ userone.password }}</div>
        </template> -->
        <template>
          <el-button size="small" type="danger" @click="reseatonlyPsw">修 改</el-button>
        </template>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>通讯邮箱</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.email"></el-input>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>电话号码</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.phone"></el-input>
        </div>
        <div class="form-mid word-aux">移动通讯号码</div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>入学/入职日期</label>
        <div class="input-inline">
          <el-input v-if="isShow" readonly v-model="userone.enter_time"></el-input>
          <el-date-picker v-else format="yyyy年MM月dd日" value-format="yyyy年MM月dd日" v-model="userone.enter_time" type="date" placeholder="选择日期"></el-date-picker>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>学历</label>
        <div class="input-inline">
          <el-select :disabled="isShow" v-model="userone.education">
            <el-option v-for="(item, index) in educationList" :label="item.name" :value="item.value" :key="index"></el-option>
          </el-select>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>个人描述</label>
        <div class="input-inline">
          <el-input :readonly="isShow" type="textarea" v-model="userone.description"></el-input>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>地址</label>
        <div class="input-inline">
          <el-input :readonly="isShow" v-model="userone.address"></el-input>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>所属机构</label>
        <div class="input-inline">
          <el-input readonly v-model="userone.org_name[0].name"></el-input>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>所属班级</label>
        <div class="input-inline">
          <el-input v-if="userone.class_name" readonly v-model="userone.class_name"></el-input>
          <el-tag v-else>暂无班级</el-tag>
        </div>
      </div>
      <div class="form-item clearfix">
        <label class="form-label"><i class="sign">*</i>所属专业</label>
        <div class="form-mid word-aux">
          <el-tag v-if="userone.pro && userone.pro.length" v-for="(item, index) in userone.pro" :key="index" type="success">
            <el-badge :value="item.sign">{{ item.name }}</el-badge>
          </el-tag>
          <el-tag v-else>暂无专业</el-tag>
        </div>
      </div>
      <div v-if="!isShow" class="form-item clearfix">
        <label class="form-label"></label>
        <div class="input-inline">
          <el-button @click="fnSubmitEdit" type="success">确认修改</el-button>
        </div>
      </div>
    </div>

    <!-- 重置密码的弹窗 -->
    <el-dialog title="重置密码" :close-on-click-modal="false" :visible.sync="reseatPsw.dialogFormReseatPsw" width="35%">
      <el-form style="width: 60%;margin: 0 auto;" :model="reseatPsw" status-icon :rules="reseatPswRules" ref="ruleFormReseat" label-width="100px">
        <el-form-item label="密码" prop="newPswCon">
          <el-input type="password" v-model="reseatPsw.newPswCon" auto-complete="off"></el-input>
          <span>{{ reseatPsw.newPswCon }}</span>
        </el-form-item>
        <el-form-item label="确认密码" prop="checkNewPswCon">
          <el-input type="password" v-model="reseatPsw.checkNewPswCon" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button @click="reseatPsw.dialogFormReseatPsw = false;reseatPsw.isResetPsw=false;">取 消</el-button>
          <el-button @click="resetFormReseat('ruleFormReseat')">重置</el-button>
          <el-button type="primary" @click="submitFormReseat('ruleFormReseat')">确 定</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    data () {
      var validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入新密码'));
        } else {
          if (this.reseatPsw.checkNewPswCon !== '') {
            this.$refs.ruleFormReseat.validateField('checkNewPswCon');
          }
          callback();
        }
      };
      var validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次确认新密码'));
        } else if (value !== this.reseatPsw.newPswCon) {
          callback(new Error('两次输入的新密码不一致!'));
        } else { callback(); }
      };
      return {
        // 单个添加成员数据
        userone: {
          username: '',
          realname: '',
          idnumber: '',
          sex: '1',// 1:男|2:女
          password: '123123',
          email: '',
          phone: '',
          enter_time: '',
          education: '5',
          description: '',
          address: '',
          org_name: [{name: ''}],// 所属机构
          class_name: '',
          pro: [],// 所属专业
          picture: '', // 头像地址
          pictureUrl: ''
        },
        // 是否是查看，编辑为false
        isShow: (this.$route.params.type === 'show') ? true : false,
        educationList: this.Const.educationList,
        // 查看密码
        readPsw: {
          isRead: false
        },
        // 是否重置密码
        reseatPsw: {
          dialogFormReseatPsw: false,
          isResetPsw: false,
          newPswCon: '',
          checkNewPswCon: ''
        },
        reseatPswRules:{
          newPswCon: [
            { required: true, validator: validatePass, trigger: 'blur' }
          ],
          checkNewPswCon: [
            { required: true, validator: validatePass2, trigger: 'blur' }
          ]
        },
        avator: {
          file: null
        }
      }
    },
    mounted () {
      this.fnReqUserMsg();
    },
    methods: {
      // 返回
      fnGetBack () {
        this.$router.go(-1);
      },
      // 重置密码
      reseatonlyPsw () {
        // TODO 完善重置用户密码的条件
        this.reseatPsw.dialogFormReseatPsw = true;
      },
      // 重置密码提交
      submitFormReseat (formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.reseatPsw.dialogFormReseatPsw = false;
            this.reseatPsw.isResetPsw = true;
          } else { return false; }
        });
      },
      // 头像信息
      uploadImg (file, fileList) {
        let acceptArr = ['image/jpeg', 'image/png'];
        const isJPG =  acceptArr.indexOf(file.raw.type) >= 0;
        const isLt2M = file.size / 1024 / 1024 < 2;
        if (!isJPG) { this.$message.error('上传头像图片只能是 JPG|PNG 格式!'); }
        if (!isLt2M) { this.$message.error('上传头像图片大小不能超过 2MB!'); }
        if (isJPG && isLt2M) {
          this.userone.pictureUrl = URL.createObjectURL(file.raw);
          this.avator.file = file.raw;
        }
        return isJPG && isLt2M;
      },
      resetFormReseat (formName) { this.$refs[formName].resetFields(); },
      // 获取用户基本信息
      fnReqUserMsg () {
        let This = this;
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'User_Con',
          action: 'getUserInfoId',
          id: this.$route.params.userid
        }).then((res) => {
          let resData = res.data;
          if (!resData.status) {
            resData.data.sex = resData.data.sex.toString();
            resData.data.pictureUrl = '';
            this.getPicture(resData.data.picture);
            this.userone = resData.data;
          } else {
            this.$message({type: 'error', message: '请求用户详细信息出错！', onClose () { This.fnGetBack(); }});
          }
        }).catch((error) => {
          this.$message({type: 'error', message: '请求用户详细信息报错！', onClose () { This.fnGetBack(); }});
        });
      },
      // 提交修改用户信息
      fnSubmitEdit () {
        let params = {
          module: 'Service',
          controller: 'User_Con',
          action: 'upUser',
          id: this.userone.id,
          username: this.userone.username,
          realname: this.userone.realname,
          sex: this.userone.sex,
          idnumber: this.userone.idnumber,
          email: this.userone.email,
          phone: this.userone.phone,
          address: this.userone.address,
          description: this.userone.description,
          education: this.userone.education,
          enter_time: this.userone.enter_time,
          files: this.avator.file
        };
        if (this.reseatPsw.isResetPsw) {
          params = Object.assign(params, {
            password: this.reseatPsw.newPswCon,
            checkpassword: this.reseatPsw.checkNewPswCon
          });
        }
        if (this.avator.file) { params.files = this.avator.file; }
        let formData = new FormData();
        for (let key in params) {formData.append(key, params[key]);}
        let config = {headers: {"Content-Type": 'multipart/form-data'}};
        this.$ajax.post(this.Const.apiurl, formData, config).then((res) => {
          console.log('编辑用户', res);
          let resData = res.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: resData.data || '编辑成功!'});
            this.fnReqUserMsg();
          } else {
            this.$message.error(resData.data || '编辑失败！');
          }
        }).catch((error) => {
          this.$message.error('提交用户修改信息报错！');
        });
      },
      // 获取图片
      getPicture (id) {
        if (id) {
          this.$ajax.post(this.Const.apiurl, {
            module: 'service',
            controller: 'Attachments_Con',
            action: 'getFile',
            id: id,
            type: 'getpic'
          }).then(response => {
            let resData = response.data;
            if (resData.status === 0) {
              this.userone.pictureUrl = resData.data;
            } else {
              this.userone.picture = '';
              this.userone.pictureUrl = resData.data;
            }
          }).catch(error => {
            this.$message({type: 'error', message: '获取用户头像失败！'});
          });
        } else {
          this.userone.picture = '';
          this.userone.pictureUrl = '暂无图片！';
        }
      }
    },
    watch: {
      $route: {
        handler (val, oldVal) {
          let currentParams = this.$route.params;
          if (currentParams && currentParams.type === 'show') {
            this.isShow = true;
            this.fnReqUserMsg();
          } else if (currentParams && currentParams.type === 'edit') {
            this.isShow = false;
          }
        },
        deep: true
      },
      userone: {
        handler (val, oldVal) {
          // console.log('watch userone', val);
          this.userone = val;
        },
        deep: true
      }
    }
  }
</script>

<style lang="scss">
.add-form {
  margin: 40px auto;
  .form-item {
    margin-bottom: 10px;
    > * {
      float: left;
    }
    .form-label {
      width: 100px;
      text-align: right;
      margin-right: 20px;
      @include middelH(40px);
      .sign {
        color: #32B16C;
        margin-right: 2px;
      }
    }
    .input-inline{
      width: 300px;
      &.professional {
        width: 50px;
      }
      &.uploadexcel button{
        width: 100px;
      }
    }
    .form-mid{
      margin-left: 20px;
      font-size: 1.2rem;
      color: #b6bdc7;
      @include middelH(40px);
    }
    .avatar, .edit-upload {
      width: 100px;
      height: 100px;
      line-height: 100px;
      font-size: 12px;
      text-align: center;
      display: block;
      border: 1px dashed #c0ccda;
      border-radius: 6px;
      overflow: hidden;
    }
  }
}
</style>
