<template>
  <div id="addpaper">
    <div class="title">
      <span><i class="el-icon-d-arrow-right"></i>添加考试场次</span>
      <div class="fr"><el-button @click="$router.back(-1)" round type="primary" plain>返回</el-button></div>
    </div>
    <section class="add-paper-wrap">
      <el-form :status-icon="true" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item label="使用试卷" prop="quiz">
          <template v-if="!$route.query.id">
            <div v-if="ruleForm.quiz">{{currentRow.name}}
              <el-button @click="dialogTableVisible = true" type="primary" plain><i class="el-icon-plus"></i> 更换试卷</el-button>
            </div>
            <el-button v-else @click="dialogTableVisible = true" type="primary" plain><i class="el-icon-plus"></i> 添加试卷</el-button>
          </template>
          <el-tag type="success" v-else>{{ ruleForm.quiz_name }}</el-tag>
        </el-form-item>
        <el-form-item label="场次名称" prop="name">
          <el-input suffix-icon="el-icon-edit" v-model="ruleForm.name" placeholder="请输入场次名称"></el-input>
        </el-form-item>
        <el-form-item label="开放时间" prop="timerange">
          <el-date-picker
            v-model="ruleForm.timerange"
            type="datetimerange"
            suffix-icon="el-icon-date"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="场次描述" prop="message">
          <el-input v-model="ruleForm.message" type="textarea" placeholder="关于本场考试的简单描述和考试的相关事项"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button v-if="!$route.query.id"  type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
          <el-button v-else  type="primary" @click="editForm('ruleForm')">立即修改</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </section>

    <!-- 弹窗添加模板试卷 -->
    <section>
        <el-dialog :close-on-click-modal="false" :show-close="false" title="选择试卷模板" width="80%" :visible.sync="dialogTableVisible">
            <el-table highlight-current-row @current-change="handleCurrentTableChange" height="300" :stripe="true" :data="paperGridData">
                <el-table-column type="index" width="50"></el-table-column>
                <el-table-column prop="name" label="试卷名称"></el-table-column>
                <el-table-column prop="classroom" label="课堂名称" width="230"></el-table-column>
                <el-table-column label="试卷时长" width="100">
                  <template slot-scope="scope">
                    <i class="el-icon-time"></i>
                    <span style="margin-left: 10px">{{ scope.row.timelimit }}</span>
                  </template>
                </el-table-column>
                <el-table-column label="试卷总分" width="100">
                  <template slot-scope="scope">
                    <el-tag type="success" close-transition>{{scope.row.grade}}</el-tag>
                  </template>
                </el-table-column>
                <el-table-column label="创建时间" width="170">
                  <template slot-scope="scope">
                    <i class="el-icon-date"></i>
                    <span style="margin-left: 10px">{{ scope.row.timecreated }}</span>
                  </template>
                </el-table-column>
                <el-table-column prop="auth" label="试卷作者" width="150"></el-table-column>
            </el-table>
            <el-pagination background
                @current-change="handleCurrentChangePage"
                :current-page.sync="paperTablePage.current_page"
                :page-size="paperTablePage.last_page"
                layout="total, prev, pager, next"
                :total="paperTablePage.total">
            </el-pagination>
            <div slot="footer" class="dialog-footer">
                <el-button @click="cancel">取 消</el-button>
                <el-button type="primary" @click="getTablePaperData">确 定</el-button>
            </div>
        </el-dialog>
    </section>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        ruleForm: {
          name: '',
          message: '',
          timerange: [new Date(), new Date(new Date().getTime() + 24*60*60*1000)],
          quiz:'',
          attempts: 1,
          quiz_name: ''
        },
        rules: {
          name: [
            { required: true, message: '请输入场次名称', trigger: 'blur' }
          ],
          message: [
            { required: true, message: '关于本场考试的简单描述和考试的相关事项', trigger: 'blur' }
          ]
        },
        currentRow: null,
        attemptsbool: false,
        dialogTableVisible: false, // 拉取课程的弹窗
        paperGridData:  [],
        paperTablePage: {
          currentPage: 1,
          pageSize: 10,
          total: 0
        },
      };
    },
    activated: function () {
      this.resetForm('ruleForm');
      this.requestAblePosi();
      if (this.$route.query.id) {
        this.requestEditPaper();
      }
  },
    methods: {
      datachechk(){
        if(this.$route.query.id === null) {
          this.ruleForm.name = this.paperList.name;
        }
      },
      // 获取可用试卷
      requestAblePosi () {
        let params = {
          module: 'exam',
          controller: 'Quiz',
          action: 'getQuizList',
          examtype: '1',
          classroom: this.Const.courseData.query.d,
          check: '6',
          page: this.paperTablePage.currentPage,
          rows: this.paperTablePage.pageSize
        };
        this.$ajax.post(this.Const.apiUrl, params).then( res => {
          let resData = res.data;
          if (resData.status == 0) {
            this.paperGridData = resData.data.data;
            this.paperTablePage = resData.data.page;
          } else {
            this.$message({type: 'info', message: resData.data || '获取可用试卷列表失败！'});
          }
        }).catch( error => {
          this.$message({type: 'error', message: '获取可用试卷列表失败！'+error.message});
        });
      },
      // 修改场次
      requestEditPaper () {
        let params = {
          module: 'exam',
          controller: 'project',
          action: 'details',
          id: this.$route.query.id
        };
        this.$ajax.post(this.Const.apiUrl, params).then( response => {
          let resData = response.data;
          if (resData.status == 0) {
            this.ruleForm.name = resData.data.name;
            this.ruleForm.message = resData.data.message;
            this.ruleForm.timerange = [resData.data.starttime, resData.data.endtime];
            this.ruleForm.attempts = resData.data.attempts;
            this.ruleForm.quiz_name = resData.data.quiz_name;
            this.ruleForm.id = resData.data.id;
          } else {
            this.$message({type: 'info', message: '获取可用试卷列表失败！'+resData.data});
          }
        }).catch( err => {
          this.$message({type: 'error', message: '获取可用试卷列表失败！'+err.message});
        });
      },
      submitForm(formName) {
        let params = {
          module: 'exam',
          controller: 'project',
          action: 'createProject',
          name: this.ruleForm.name,
          message: this.ruleForm.message,
          starttime: this.ruleForm.timerange[0],
          endtime: this.ruleForm.timerange[1],
          quiz: this.ruleForm.quiz,
        };
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.$ajax.post(this.Const.apiUrl, params).then( response => {
              let resData = response.data;
              if (resData.status == 0) {
                this.$message({type: 'success', message: '场次创建成功'});
                this.resetForm();
              } else {
                this.$message({type: 'info', message: '添加场次失败'+resData.data});
              }
            }).catch( err => {
              this.$message({type: 'error', message: '添加场次失败'+err.message});
            });
          } else { return false; }
        });
      },
      // 修改提交
      editForm (formName) {
        let params = {
          module: 'exam',
          controller: 'project',
          action: 'editProject',
          name: this.ruleForm.name,
          message: this.ruleForm.message,
          starttime: this.ruleForm.timerange[0],
          endtime: this.ruleForm.timerange[1],
          id: this.ruleForm.id,
          attempts: this.ruleForm.attempts
        };
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.$ajax.post(this.Const.apiUrl, params).then( response => {
              let resData = response.data;
              if (resData.status == 0) {
                this.$message({type: 'success', message: '场次修改成功'});
              } else {
                this.$message({type: 'info', message: '添加修改失败'+resData.data});
              }
            }).catch( err => {
              this.$message({type: 'error', message: '添加修改失败'+err.message});
            });
          } else { return false; }
        });
      },
      // 弹窗选择试卷
      handleCurrentTableChange (currentRow) {
        // console.log(currentRow);
        this.currentRow = currentRow;
        // console.log(this.currentRow,44444);
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      },
      // 页面改变
      handleCurrentChangePage (currentpage) {
        let params = {
          module: 'exam',
          controller: 'quiz',
          action: 'getQuizList',
          examtype: '1',
          classroom: '1',
          check: '6',
          page: currentpage,
          rows: '15'
        };
        this.$ajax.post(this.Const.apiUrl, params).then( res => {
          console.log( res);
          let base = res.data.data;
          if (base.status == 1)
          console.log('错误');
          if(base.data != null)
          this.paperGridData = base.data;
          this.paperTablePage = base.page;
        }).catch( error => {
          console.log('aaaaaaa');
        });
      },
      // 弹窗页面确定
      getTablePaperData () {
        this.ruleForm.quiz  = this.currentRow.id;
        this.dialogTableVisible = false;
      },
      cancel () {
        this.currentRow = null;
        this.dialogTableVisible = false;
      }
    },
    watch: {
      $route: function(newval, oldval){
        // console.log('oldval, newval', newval);
        if ( newval.query === '') {
          this.resetForm('ruleForm');
        }
      }
    }
  }
</script>

<style lang="scss">
#paper-content {
  .add-paper-wrap {
    width: 70%;
    margin: 30px auto;
  }
}
</style>
