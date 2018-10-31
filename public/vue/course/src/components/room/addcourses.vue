<template>
  <div id="addcourse" class="wrap-course content">
    <div class="class-wrap">
      <div class="classify clearfix">
        <ul class="fl">
          <li>
            <i class="before" style="left: 0;"></i>
            <a class="click" href="javascript:void(0);">添加新课堂</a>
          </li>
        </ul>
        <div class="back fr">
          <el-button type="primary" plain round @click="$router.go(-1)">返 回</el-button>
        </div>
      </div>
      <div class="add-course-form">
        <div id="add-form">
          <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">
            <el-form-item label="选择课程">
              <el-button @click="selectCourseByList" type="primary" plain size='small'><i class="el-icon-plus"></i></el-button>
            </el-form-item>
            <el-form-item label="所属机构" prop="org_name">
              <el-input :readonly="true" v-model="ruleForm.org_name" placeholder="请选择所属机构"></el-input>
            </el-form-item>
            <el-form-item label="课堂全名" prop="fullname">
              <el-input v-model="ruleForm.fullname" placeholder="请输入课堂全名"></el-input>
            </el-form-item>
            <el-form-item label="课堂简称" prop="shortname">
              <el-input v-model="ruleForm.shortname" placeholder="请输入课堂简称"></el-input>
            </el-form-item>
            <el-form-item label="课程编号" prop="idnumber">
              <el-input :readonly="true" v-model="ruleForm.idnumber" placeholder="请输入课程编号"></el-input>
            </el-form-item>
            <el-form-item label="课堂编号" prop="roomcode">
              <el-input v-model="ruleForm.roomcode" placeholder="请输入课堂编号"></el-input>
            </el-form-item>
            <el-form-item label="课堂类型" prop="cate_name">
              <!-- <el-select v-model="ruleForm.roomtype" placeholder="请选择课堂类型">
                <el-option v-for="(item, index) in roomtypeList" :key="index" :label="item.name" :value="item.value"></el-option>
              </el-select> -->
              <el-input :readonly="true" v-model="ruleForm.cate_name" placeholder="请输入课堂类型"></el-input>
            </el-form-item>
            <el-form-item label="课堂课时" prop="numsections">
              <el-input type="number" min="1" placeholder="请输入课堂课时" v-model="ruleForm.numsections"></el-input>
            </el-form-item>
            <el-form-item label="课堂学期" prop="semester">
              <el-select v-model="ruleForm.semester" placeholder="请选择课堂学习学期">
                <el-option v-for="(item, index) in semesterList" :key="index" :label="item.name" :value="item.value"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="课堂概要" prop="summary">
              <el-input type="textarea" placeholder="请输入课堂概要" v-model="ruleForm.summary"></el-input>
            </el-form-item>
            <el-form-item label="课程封面">
              <el-upload :auto-upload="false" action="" list-type="picture-card" :show-file-list="false"
                :on-change="handlePictureChange">
                <img v-if="file.imgurl" :src="file.imgurl" class="avatar">
                <i v-else class="el-icon-plus"></i>
              </el-upload>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
              <el-button @click="resetForm('ruleForm')">重置</el-button>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </div>
    <!-- 弹窗选择课程 -->
    <el-dialog title="选择课程模板" :close-on-click-modal="false" :visible.sync="dialogTableVisibleSelCourse" :fullscreen="true">
      <!-- 条件搜索 -->
      <el-table :data="gridDataCourseList" highlight-current-row @current-change="currentRowChange">
        <el-table-column type="index" width="50"></el-table-column>
        <el-table-column property="fullname" label="课程全称"></el-table-column>
        <el-table-column property="shortname" label="课程简称"></el-table-column>
        <el-table-column property="cate_name" label="课程类型" width="150"></el-table-column>
        <el-table-column property="org_name" label="所属机构" width="240"></el-table-column>
        <el-table-column property="idnumber" label="课程编号" width="120"></el-table-column>
        <el-table-column property="numsections" label="课程课时" width="100"></el-table-column>
      </el-table>
      <el-pagination :current-page.sync="dialogPage.currentPage" :current-change="handleCurrentPage" background layout="prev, pager, next" :total="dialogPage.total"></el-pagination>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogTableVisibleSelCourse = false">取 消</el-button>
        <el-button type="primary" @click="submitDialog">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      // userMsg: JSON.parse($.cookie("userMsg")),
      dialogTableVisibleSelCourse: false,
      ruleForm: {
        org_name: '',
        fullname: '',
        shortname: '',
        idnumber: '',
        roomcode: '',
        cate_name: '',
        numsections: '',
        semester: '',// 学期
        summary: ''
      },
      rules: {
        department: [
          { required: true, message: '请选择所属机构', trigger: 'blur' }
        ],
        fullname: [
          { required: true, message: '请输入课堂全名', trigger: 'blur' }
        ],
        shortname: [
          { required: true, message: '请输入课堂简称', trigger: 'blur' }
        ],
        idnumber: [
          { required: true, message: '请输入课程编号', trigger: 'blur' }
        ],
        roomcode: [
          { required: true, message: '请输入课堂编号', trigger: 'blur' }
        ],
        cate_name: [
          { required: true, message: '请输入课堂类型', trigger: 'change' }
        ],
        numsections: [
          { required: true, message: '请输入课堂课时', trigger: 'change' }
        ],
        summary: [
          { required: true, message: '请输入课堂概要', trigger: 'blur' }
        ]
      },
      semesterList: this.Const.semesterList,
      // 选择课程模板
      gridDataCourseList: [
        {
          id: '',
          fullname: '',
          shortname: '',
          cate_name: '',
          org_name: '',
          idnumber: '',
          numsections: ''
        }
      ],
      // 弹窗翻页
      dialogPage: {
        total: 100,
        currentPage: 1
      },
      // 弹窗选中的数据
      selDialogData: null,
      file: {
        avataorimg: null,
        imgurl: ''
      }
    };
  },
  created: function() {},
  activated: function() {},
  mounted: function() {
    
  },
  methods: {
    // 封面图片
    handlePictureChange (file, fileList) {
      console.log('封面图片', file, fileList);
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
        this.file.imgurl = URL.createObjectURL(file.raw);
        this.file.avataorimg = file.raw;
      }
      return isJPG && isLt2M;
    },
    // 提交
    submitForm (formName) {
      let This = this;
      this.$refs[formName].validate((valid) => {
        const loading = this.$loading({
          lock: true,
          text: 'Loading',
          spinner: 'el-icon-loading',
          background: 'rgba(0, 0, 0, 0.7)'
        });
        if (valid) {
          this.$ajax.post(this.Const.apiurl, {
            module: 'service',
            controller: 'Classroom_Con',
            action: 'addClassroom',
            fullname: this.ruleForm.fullname,
            shortname: this.ruleForm.shortname,
            idnumber: this.ruleForm.roomcode,
            summary: this.ruleForm.summary,
            course_id: this.ruleForm.id,
            category: this.ruleForm.cate_id,
            numsections: this.ruleForm.numsections,
            department: this.ruleForm.org_id,
            semester: this.ruleForm.semester,
            files: this.file.avataorimg
          }).then(res => {
            loading.close();
            console.log('ansidausd', res);
            let resData = res.data;
            if (resData.status == 0) {
              this.$message({
                type: 'success', 
                message: resData.data ? resData.data : '课堂添加成功！', 
                onClose () {
                  This.$router.push({
                    path: '/course/adduser',
                    query: {
                      rd: "964903DA489CB4E92A00FCD9C71819BD"
                    }
                  });
                }
              });

            } else {
              this.$message({type: 'error', message: resData.data ? resData.data : '添加课堂失败！'});
            }
          }).catch(err => {
            loading.close();
            this.$message({type: 'error', message: resData.data ? resData.data : '添加课堂失败！请检查本地网络'});
          });
        } else { return false; }
      });
    },
    // 重置
    resetForm (formName) {this.$refs[formName].resetFields();},
    // 弹窗选择课程
    selectCourseByList () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Course_Con',
        action: 'queCourseFc',
        page: this.dialogPage.currentPage,
        rows: 10
      }).then(res => {
        loading.close();
        let resData = res.data;
        console.info('2324', resData);
        if (resData.status == 0) {
          this.dialogTableVisibleSelCourse = true;
          this.dialogPage.total = resData.data.total;
          this.dialogPage.currentPage = resData.data.current_page;
          this.gridDataCourseList = resData.data.data;
        } else { this.$message({type: 'info', message: resData.data ? resData.data : '获取课程模板失败！'}); }
      }).catch(err => {
        loading.close();
        this.$message({type: 'error', message: resData.data ? resData.data : err.message});
      });
    },
    // 弹窗页码发生变化
    handleCurrentPage (currentVal) {
      this.dialogPage.currentPage = currentVal;
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Course_Con',
        action: 'queCourseFc',
        page: this.dialogPage.currentPage,
        rows: 10
      }).then(res => {
        loading.close();
        let resData = res.data;
        if (resData.status == 0) {
          this.dialogTableVisibleSelCourse = true;
          this.dialogPage.total = resData.data.total;
          this.dialogPage.currentPage = resData.data.current_page;
          this.gridDataCourseList = resData.data.data;
        } else { this.$message({type: 'info', message: resData.data ? resData.data : '获取课程模板失败！'}); }
      }).catch(err => {
        loading.close();
        this.$message({type: 'error', message: resData.data ? resData.data : '获取课程模板失败！请检查本地网络'});
      });
    },
    // 选中行发生变化
    currentRowChange (currentRow, oldCurrentRow) {
      console.log('行发生', currentRow, oldCurrentRow);
      this.selDialogData = currentRow;
    },
    // 提交弹窗
    submitDialog () {
      if (this.selDialogData) {
        this.ruleForm = this.selDialogData;
        this.dialogTableVisibleSelCourse = false;
      }
    }
  }
};
</script>

<style lang="scss">
  #add-form {
    width: 80%;
    margin: 20px auto;
    .el-upload--picture-card {
      width: 100px;
      height: 100px;
      line-height: 100px;
      img {width: 100%;height:100%;display: block;}
    }
    .el-form {
      width: 60%;
    }
  }
</style>
