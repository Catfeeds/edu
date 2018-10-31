<template>
  <div class="add-user">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">添加用户</a>
        </li>
      </ul>
      <div class="select fr btnarr clearfix">
        <div class="dt-buttons">
          <el-button-group>
            <el-button @click="fnGetBack" type="primary" size="small" icon="el-icon-arrow-left">返回</el-button>
            <el-button @click="fnCutAddTag($route.params.tag)" size="small" v-if="($route.params.tag === 'one')" type="primary">批量导入<i class="el-icon-upload2"></i></el-button>
            <el-button @click="fnCutAddTag($route.params.tag)" size="small" v-if="($route.params.tag === 'more')" type="primary">单个录入<i class="el-icon-plus"></i></el-button>
          </el-button-group>
        </div>
      </div>
    </div>
    <div class="add-form">
      <el-form :model="userone" :rules="rules" ref="addform" label-width="130px">
        <template v-if="$route.params.tag === 'more'">
          <el-form-item label="上传文件">
            <el-upload class="avatar-uploader" :show-file-list="false" action=""
              :on-change="beforeExcelUpload" :auto-upload="false">
              <el-button size="small" type="primary">点击上传</el-button>
              <span slot="tip" v-if="file.excelName">{{ file.excelName}}</span>
            </el-upload>
            <div class="form-mid word-aux" style="display: inline-block;">只允许excel文件 <a :href="file.downloadexcel" download="批量成员导入模板.xlsx">下载模板</a></div>
          </el-form-item>
        </template>
        <template v-if="$route.params.tag === 'one'">
          <el-form-item label="账号" prop="username">
            <el-input status-icon v-model="userone.username" placeholder="请输入用户账号"></el-input>
          </el-form-item>
          <el-form-item label="姓名" prop="realname">
            <el-input status-icon v-model="userone.realname" placeholder="请输入您的姓名"></el-input>
          </el-form-item>
          <el-form-item label="用户编号" prop="idnumber">
            <el-input status-icon v-model="userone.idnumber" placeholder="请输入用户编号"></el-input>
            <div class="form-mid word-aux" style="display: inline-block;">学生学号，教师/管理职工号</div>
          </el-form-item>
          <el-form-item label="性别">
            <el-radio v-model="userone.sex" label="1">男</el-radio>
            <el-radio v-model="userone.sex" label="2">女</el-radio>
          </el-form-item>
          <el-form-item label="上传头像">
            <el-upload action="" class="avatar-uploader" list-type="picture-card" :show-file-list="false"
              :on-change="beforeAvatarUpload" :auto-upload="false">
              <img v-if="userone.imageUrl" :src="userone.imageUrl" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
          </el-form-item>
          <el-form-item label="用户密码" prop="password">
            <el-input @change="handlePswChange" type="password" status-icon v-model="userone.password" placeholder="请输入用户密码"></el-input>
            <span>默认:{{ userone.password }}</span>
          </el-form-item>
          <el-form-item label="确认密码" prop="checkpassword">
            <el-input type="password" status-icon v-model="userone.checkpassword" placeholder="请确认您的密码"></el-input>
          </el-form-item>
          <el-form-item label="通讯邮箱" prop="email">
            <el-input status-icon v-model="userone.email" placeholder="请输入您的通讯邮箱"></el-input>
          </el-form-item>
          <el-form-item label="电话号码" prop="phone">
            <el-input status-icon v-model="userone.phone" placeholder="请输入您的电话号码"></el-input>
          </el-form-item>
          <el-form-item label="入学/入职日期" prop="enter_time">
            <el-date-picker v-model="userone.enter_time" type="date" aria-placeholder="请选择日期"></el-date-picker>
          </el-form-item>
          <el-form-item label="学历" prop="education">
            <el-select v-model="userone.education">
              <el-option v-for="(item, index) in educationList" :label="item.name" :value="item.value" :key="index"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="个人描述">
            <el-input type="textarea" v-model="userone.description" placeholder="请尽量描述详细"></el-input>
          </el-form-item>
          <el-form-item label="地址">
            <el-input v-model="userone.address" placeholder="请输入您的所在地址"></el-input>
          </el-form-item>
        </template>
        <el-form-item label="用户角色" prop="role">
          <el-select v-model="userone.role">
            <el-option v-for="(item, index) in roleList" :label="item.remark" :value="item.id" :key="index"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="所属机构">
          <el-cascader v-loading="orgLoading" show-all-levels @change="fnSelOrgChange" v-model="userone.org_id" :props="props" :options="organizationList"></el-cascader>
        </el-form-item>
        <el-form-item label="所属班级">
          <el-select v-model="userone.class">
            <el-option v-for="(item, index) in classList" :label="item.name" :value="item.id" :key="index"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="所属专业">
          <el-button @click="dialogFormVisible = true" icon="el-icon-edit-outline"></el-button>
          <div class="form-mid word-aux">
            <el-tag v-for="(item, index) in professionalTags" :key="index" type="success">{{ item }}</el-tag>
          </div>
        </el-form-item>
        <el-form-item>
          <el-button v-if="$route.params.tag === 'one'" type="primary" @click="fnSubmitAdd('addform')">立即添加</el-button>
          <el-button :disabled='!file.excel' v-if="$route.params.tag === 'more'" type="primary" @click="fnSubmitExcel('addform')">立即添加</el-button>
          <el-button @click="resetForm('addform')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>

    <!-- 选择专业的对话框 -->
    <el-dialog title="添加专业" :visible.sync="dialogFormVisible">
      <el-transfer filterable :titles="['所有专业', '已选专业']" :button-texts="['移除', '添加']" v-model="userone.professional" :data="professionalList"></el-transfer>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogPro">取 消</el-button>
        <el-button type="primary" @click="dialogPro">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    data () {
      let validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        } else {
          if (this.userone.password !== '') {
            this.$refs.addform.validateField('checkpassword');
          }
          callback();
        }
      };
      let validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请确认您的密码'));
        } else if (value !== this.userone.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      let checkPhone = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入您的电话号码'));
        } else {
          callback();
        }
      };
      let checkName = (rule, value, callback) => {
        let reg = /^[\u4e00-\u9fa5]+$/;
        if (!reg.test(value)) {
          callback(new Error('请输入正确的中文姓名'));
        } else {
          callback();
        }
      };
      return {
        // 单个添加成员数据
        userone: {
          username: '',
          realname: '',
          idnumber: '',
          sex: '1', // 1:男|2:女
          password: '123123',
          checkpassword: '123123',
          email: '',
          phone: '',
          role: '',
          enter_time: new Date(),
          education: '5',
          description: '',
          address: '',
          org_id: [],// 所属机构
          class: '',
          professional: [],// 所属专业
          imageUrl: '', // 头像地址
          excelFile: null, // 批量导入excel
        },
        // 验证规则
        rules: {
          username: [
            {required: true, message: '请输入用户名', trigger: 'blur'}
          ],
          realname: [
            {required: true, validator: checkName, trigger: 'blur'}
          ],
          idnumber: [
            {required: true, message: '请输入用户编号', trigger: 'blur'}
          ],
          password: [
            {required: true, validator: validatePass, trigger: 'blur'}
          ],
          checkpassword: [
            {required: true, validator: validatePass2, trigger: 'blur'}
          ],
          email: [
            {type: 'email', required: true, message: '请输入您的通讯邮箱', trigger: 'blur'}
          ],
          phone: [
            {required: true, validator: checkPhone, trigger: 'blur'}
          ],
          enter_time: [
            { type: 'date', required: true, message: '请选择日期', trigger: 'change' }
          ],
          education: [
            { required: true, message: '请选择学历', trigger: 'change' }
          ],
          role: [
            {required: true, message: '请选择用户角色', trigger: 'change'}
          ]
        },
        educationList: this.Const.educationList,
        // 角色列表
        roleList: [
          /* {
            id: '123123',
            remark: 'admin'
          },{
            id: '1231233',
            remark: 'admin1'
          } */
        ],
        // 机构下拉
        organizationList: [
          /* {
            id: 'B608AA535E0EC9C5DDBC8548AF39D4CB',
            name: '电子科技大学成都学院',
            children: []
          } */
        ],
        props: {
          value: 'id',
          label: 'name',
          children: 'children'
        },
        orgLoading: false,
        // 班级数据
        classList: [
          /* {
            id: '12312',
            name: '一班'
          },{
            id: '1231232',
            name: '二班'
          } */
        ],
        // 专业数据
        dialogFormVisible: false,
        professionalList: [
          /*{
            key: '1231',
            label: '网络工程'
          },{
            key: '12232331',
            label: '软件工程'
          },{
            key: '12454531',
            label: '媒体艺术'
          }*/
        ],
        professionalTags: [/*'媒体艺术'*/],
        userMsg: null,
        file: {
          downloadexcel: this.Const.projectRoot.replace('index.php/', 'public/static/muban/userupload.xlsx'),
          excel: null,
          excelName: '',
          avataorimg: null
        }
      }
    },
    mounted () {
      /*  判断用户状态和填充初始机构信息 */
      let userData = this.Const.userMsg || '';
      if (!userData) {
        this.$message({
          type: 'error',
          message: '登录用户的信息丢失，请重新登录!',
          onClose () {
            window.location.href = This.Const.login;
          }
        });
      } else {
        let parseUserData = userData;
        this.userMsg = parseUserData;
        console.log('userMsg', userData);
        this.userone.org_id = (() => {
          let arr = [];
          arr.push(parseUserData.pid);
          return arr;
        })();
        this.organizationList = (() => {
          return [{
            id: parseUserData.pid,
            name: parseUserData.org_name,
            children: []
          }];
        })();
      }
      

      // 请求所有机构信息
      this.fnReqAllOrg();
      this.fnGetRole();

      if (this.$route.params.tag === 'one') {
        console.log('watch router one');
      } else if (this.$route.params.tag === 'more') {
        console.log('watch router more');
      }
    },
    methods: {
      // 返回
      fnGetBack () {
        this.$router.go(-1);
      },
      // 切换批量/单个添加
      fnCutAddTag (tag) {
        if (tag === 'one') {
          this.$router.push({path: '/library/user/add/more'});
        } else if (tag === 'more') {
          this.$router.push({path: '/library/user/add/one'});
        }
      },
      // 密码输入框改变
      handlePswChange (value) {
        this.userone.checkpassword = '';
      },
      // 头像上传
      beforeAvatarUpload(file, fileList) {
        let acceptArr = ['image/jpeg', 'image/png'];
        const isJPG =  acceptArr.indexOf(file.raw.type) >= 0;
        const isLt2M = file.size / 1024 / 1024 < 2;
        if (!isJPG) {
          this.$message.error('上传头像图片只能是 JPG|PNG 格式!');
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        if (isJPG && isLt2M) {
          this.userone.imageUrl = URL.createObjectURL(file.raw);
          this.file.avataorimg = file.raw;
        }
        return isJPG && isLt2M;
      },
      // 专业数据-关闭弹窗-生成tag标记
      dialogPro () {
        this.professionalTags = (() => {
          const arr = [];
          for (let i = 0, item; item = this.userone.professional[i]; i++) {
            for (let j = 0, jtem; jtem = this.professionalList[j]; j++) {
              if (item === jtem.key) {
                arr.push(jtem.label);
              }
            }
          }
          return arr;
        })();
        this.dialogFormVisible = false;
      },
      // 获取所有机构信息
      fnReqAllOrg () {
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'Organization_Con',
          action: 'getUserOrgAll'
        }).then((res) => {
          var resData = res.data;
          if (!resData.status) {
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
      // 机构绑定值发生改变时 触发请求班级数据
      fnSelOrgChange () {
        this.fnReqClassByOrgid(this.userone.org_id[this.userone.org_id.length - 1]);
        this.fnReqProByOrgid(this.userone.org_id[this.userone.org_id.length - 1]);
      },
      // 根据机构id 请求机构下的班级数据
      fnReqClassByOrgid (orgid) {
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'Class_Con',
          action: 'getClassForOrg',
          id: orgid
        }).then((res) => {
          let resData = res.data;
          if (resData.status == 0) {
            this.classList = resData.data;
          } else {
            this.classList = [];
            this.$message({type: 'info', message: resData.data ? resData.data : '目前没有添加班级！'});
          }
        }).catch((error) => {
          this.$message({type: 'error', message: resData.data ? resData.data : '请求班级数据报错，请检查本地网络！'});
        });
      },
      // 根据机构id 请求机构下的所有专业数据
      fnReqProByOrgid (orgid) {
        // 清空'专业'数据列表
        this.professionalList = [];
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'Profession_Con',
          action: 'getProFOrg',
          id: orgid
        }).then((res) => {
          let resData = res.data;
          if (!resData.status) {
            if (typeof resData.data === 'string') {
              this.$message({type: 'info', message: resData.data});
            } else {
              let arr = [];
              for (let i = 0, item; item = resData.data[i]; i++) {
                arr.push({
                  "key": item['id'],
                  "label": item['name']
                });
              }
              this.professionalList = arr;
            }
          } else {
            this.$message.error('请求专业数据有点小问题！');
          }
        }).catch((error) => {
          this.$message.error('请求专业数据报错！');
        });
      },
      // 提交数据
      fnSubmitAdd (formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            let loadingContent = this.$loading({text: '正在添加，请稍后......'});
            let mca = {
              module: 'Service',
              controller: 'User_Con',
              action: 'addUser'
            };
            let userone = JSON.parse(JSON.stringify(this.userone));
            userone.org_id = userone.org_id[userone.org_id.length-1];
            userone.enter_time = Date.parse(new Date(userone.enter_time)).toString().slice(0, 10);
            userone.files = this.file.avataorimg;
            let query = Object.assign(mca, userone);
            let formData = new FormData();
            for (let key in query) {formData.append(key, query[key]);}
            let config = {headers: {"Content-Type": 'multipart/form-data'}};
            this.$ajax.post(this.Const.apiurl, formData, config).then((res) => {
              loadingContent.close();
              let resData = res.data;
              if (resData.status == 0) {
                this.$message({type: 'success', message: '添加成功！'});
              } else {
                this.$message({type: 'error', message: resData.data || '添加失败！'});
              }
            }).catch((error) => {
              loadingContent.close();
              this.$message.error('添加用户请求失败！');
            });
          } else { return false; }
        });
      },
      // 重置表单数据
      resetForm (formName) {
        this.$refs[formName].resetFields();
      },
      // 获取角色信息
      fnGetRole () {
        this.$ajax.post(this.Const.apiurl, {
          module: 'Admin',
          controller: 'Rbac',
          action: 'getSonRole'
        }).then(res => {
          // console.log('角色信息', res);
          let resData = res.data;
          if (resData.status == 0) {
            this.roleList = resData.data;
          } else {
            this.$message({type: 'error', message: resData.data || '获取角色失败！'});
          }
        }).catch(error => {
          this.$message.error('请求角色信息失败！');
        });
      },
      // 批量导入
      beforeExcelUpload (file, fileList) {
        let acceptArr = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        const isEXCEL = acceptArr.indexOf(file.raw.type) >= 0;
        if (!isEXCEL) {
          this.$message.error('只能上传 excel 文件！');
        }
        if (isEXCEL) {
          this.file.excelName = file.name;
          this.file.excel = file.raw;
        }
        return isEXCEL;
      },
      // 批量上传提交
      fnSubmitExcel (formName) {
        console.log('提交excel文件', this.file.excel);
        this.$refs[formName].validate(valid => {
          if (valid) {
            let query = {
              module: 'service',
              controller: 'User_Con',
              action: 'InsertExcel',
              organization: this.userone.org_id[this.userone.org_id.length-1],
              class: this.userone.class,
              profession: this.userone.professional,
              role: this.userone.role,
              files: this.file.excel
            };
            let loadingContent = this.$loading({text: '正在添加，请稍后......'});
            let formData = new FormData();
            for (let key in query) {formData.append(key, query[key]);}
            let config = {headers: {"Content-Type": 'multipart/form-data'}};
            this.$ajax.post(this.Const.apiurl, formData, config).then((res) => {
              console.log('批量导入', res);
              loadingContent.close();
              let resData = res.data;
              if (!resData.status && typeof resData.data === 'boolean') {
                this.$message({type: 'success', message: '添加成功！'});
              } else {
                this.$message({type: 'info', message: resData.data || '添加失败！'});
              }
            }).catch((error) => {
              loadingContent.close();
              this.$message.error('添加用户请求失败！');
            });
          } else { return false; }
        });
      }
    },
    watch: {
      $route: {
        handler (val, oldVal) {
          let currentParams = this.$route.params;
          if (this.$route.params.tag === 'one') {
            console.log('watch router one');
          } else if (this.$route.params.tag === 'more') {
            console.log('watch router more');
          }
        },
        deep: true
      }
    }
  }
</script>

<style lang="scss">

.add-user {
  .el-dialog {
    width: 970px;
  }
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
      .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        &:hover {
          border-color: #409EFF;
        }
        .avatar-uploader-icon {
          font-size: 20px;
          color: #8c939d;
          width: 100px;
          height: 100px;
          line-height: 100px;
          text-align: center;
        }
        .avatar {
          width: 100px;
          height: 100px;
          display: block;
        }
      }
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
    }
    .el-form-item__label {
      margin-right: 20px;
    }
    .el-form-item__content {
      .word-aux{display: inline-block;}
      > .el-input, > .el-textarea {
        width: 50%;
      }
      .el-form-item__error {
        left: 20px;
      }
      .el-upload--picture-card {
        width: 100px;
        height: 100px;
        line-height: 100px;
        img {
          display: block;
          width: 100%;
          height: 100%;
        }
      }
      .avatar-uploader {
        display: inline-block;
        margin-right: 20px;
      }
    }
  }
}
</style>
