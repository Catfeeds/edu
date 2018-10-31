<template>
  <div id="addposition">
    <div class="title">
      <span><i class="el-icon-d-arrow-right"></i>添加试卷</span>
      <div class="fr"><el-button @click="back()" round type="primary" plain>返回</el-button></div>
    </div>
    <section class="add-paper-wrap">
      <el-form :status-icon="true" :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
        <el-form-item label="试卷名称" prop="name">
          <el-input suffix-icon="el-icon-edit" v-model="ruleForm.name" placeholder="请输入试卷名称"></el-input>
        </el-form-item>
        <el-form-item label="试卷说明" prop="intro">
          <el-input suffix-icon="el-icon-time" type="textarea" :rows="4" v-model="ruleForm.intro"></el-input>
        </el-form-item>
        <el-form-item label="章节" prop="section">
          <el-tree 
          ref="quizsection"
          :props="props"
          :data="sections"
          show-checkbox
          @check-change="handleCheckChange('quizsection',null)">
        </el-tree>
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
        <el-form-item label="考试时长" prop="timelimit">
          <el-select v-model="ruleForm.timelimit" placeholder="请选择">
            <el-option label="30分钟" value="30"></el-option>
            <el-option label="60分钟" value="60"></el-option>
            <el-option label="90分钟" value="90"></el-option>
            <el-option label="120分钟" value="120"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="试卷用途" prop="examtype">
           <el-select @change="examtypeChange"  v-model="ruleForm.examtype" placeholder="请选择">
            <el-option  label="测试" value="0"></el-option>
            <el-option  label="考试" value="1"></el-option>
           </el-select>
        </el-form-item>
        <el-form-item label="查看答卷时间" prop="preferredbehaviour">
          <el-select  v-model="ruleForm.preferredbehaviour" placeholder="请选择">
            <el-option  label="考试结束后" value="0"></el-option>
            <el-option  label="始终不显示" value="1"></el-option>
           </el-select>
        </el-form-item>
        <el-form-item label="评分方法" prop="grademethod">
          <el-select  v-model="ruleForm.grademethod" placeholder="请选择">
            <el-option  label="最高分" value="1"></el-option>
            <el-option  label="平均分" value="2"></el-option>
            <el-option  label="第一次答题" value="3"></el-option>
            <el-option  label="最后一次答题" value="4"></el-option>
           </el-select>
        </el-form-item>
        <el-form-item label="答题次数" prop="attempts">
          <el-input :readonly="attemptsbool" suffix-icon="el-icon-edit-outline" v-model="ruleForm.attempts" type="number"></el-input>
        </el-form-item>
        <el-form-item label="总分" prop="grade">
          <el-input suffix-icon="el-icon-edit-outline" v-model="ruleForm.grade" type="number"></el-input>
        </el-form-item>
        <el-form-item label="选题方式" prop="shufflequestions">
          <el-select @change="changeShuffle"  v-model="ruleForm.shufflequestions" placeholder="请选择">
            <el-option  label="自动选题" value="0"></el-option>
            <el-option  label="手动选题" value="1"></el-option>
           </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </section>

    <!-- 弹窗添加模板试卷 -->
    <section>
        <el-dialog :close-on-click-modal="false" :show-close="false" title="选择试卷模板" width="80%" :visible.sync="dialogTableVisible">
          <el-tabs type="border-card">
            <el-tab-pane v-for='item in questions' :key="item.qtype" :label="item.name">
                章节：<el-tree
                :ref="item.qtype + 'Section'"
                :props="props"
                :data="sections"
                show-checkbox
                @check-change="handleCheckChange(item.qtype + 'Section',item.qtype)">
              </el-tree>
                每题分数：<el-input v-model="item.score" placeholder="请输入内容"></el-input>
                难度配置：
                <div style="margin-left:60px">
                  <div v-for="val in item.conf" :key="val.index">
                    {{val.diff}}<el-input v-model="val.num" placeholder="请输入内容"></el-input>
                  </div>
                </div>
            </el-tab-pane>
          </el-tabs>
            <div slot="footer" class="dialog-footer">
                <el-button @click="cancelQuestConf">取 消</el-button>
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
        props: {
          label: 'name',
          children : "children",
          isLeaf :"leaf"
        },
        sections:[],
        ruleForm: {
          name: '',
          timerange: [new Date(), new Date(new Date().getTime() + 24*60*60*1000)],
          attempts: '3',
          preferredbehaviour:'1',
          section:[],
          sections:[],
          intro:'',
          examtype: '1',
          timelimit:'',
          grademethod:'1',
          shufflequestions:'1',
          grade:'',
        },
        questions: [
          {
            name:'单选题',
            qtype: 'singlechoice',
            section: [],
            sections: [],
            score: '10',
            conf : [
                {diff: '难度1', num : '10'},
                {diff: '难度2', num : '20'}
            ]
          },
          {
            name:'多选题',
            qtype: 'multiplechoice',
            section: [],
            sections: [],
            score: '20',
            conf : [
                {diff: '难度1', num : '1'},
                {diff: '难度1', num : '2'}
            ]
          },
          {
            name:'填空题',
            qtype: 'shortanswer',
            section: [],
            sections: [],
            score: '每题分值',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          },
          {
            name:'判断题',
            qtype: 'truefalse',
            section: [],
            sections: [],
            score: '每题分值',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          },
          {
            name:'匹配题',
            qtype: 'match',
            section: [],
            sections: [],
            score: '每题分值',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          },
          {
            name:'简答题',
            qtype: 'essay',
            section: [],
            sections: [],
            score: '每题分值',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          },
          {
            name:'综合题',
            qtype: 'comprehensive',
            section: [],
            sections: [],
            score: '每题分值',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          },
          {
            name:'阅读理解',
            qtype: 'readingcomprehension',
            section: [],
            sections: [],
            score: '',
            conf : [
                {diff: '难度1', num : ''},
                {diff: '难度1', num : ''}
            ]
          }
          ],
        
        rules: {
          name: [
            { required: true, message: '请输入场次名称', trigger: 'blur' }
          ],
          attempts: [
            { required: true, message: '允许答题次数', trigger: 'blur' }
          ],
          intro: [
            { required: true, message: '请输入试卷说明', trigger: 'blur' }
          ],
          section: [
            { required: true, message: '请输入章ID', trigger: 'blur' }
          ],
          sections: [
            { required: true, message: '请输入节ID', trigger: 'blur' }
          ],
          preferredbehaviou: [
            { required: true, message: '请选择查看答卷时间', trigger: 'change' }
          ],
          examtype: [
            { required: true, message: '请选择考试用途', trigger: 'change' }
          ],
          grademethod: [
            { required: true, message: '请选择评分方法', trigger: 'change' }
          ],
          timelimit: [
            { required: true, message: '请选择考试时间', trigger: 'change' }
          ],
          shufflequestions: [
            { required: true, message: '请选择添加试题方式', trigger: 'change' }
          ],
          grade: [
            { required: true, message: '请输入总分', trigger: 'blur' }
          ]
        },
        attemptsbool: false,
        dialogTableVisible: false, // 拉取课程的弹窗
      }
    },
    activated: function () {
      this.resetForm('ruleForm');
      this.loadSections();
      if (this.$route.query.id)
      {
        let params = {
          id:this.$route.query.id,
          module: 'exam',
          controller: 'quiz',
          action: 'quizDetail',
        };
        this.$ajax.post(this.Const.apiUrl, params).then( res => {
          if (res.data.status == 0) {
            let timeopen = new Date(res.data.data.timeopen);
            let timeclose = new Date(res.data.data.timeclose);
            let timerange = [timeopen, timeclose];
            let data = Object.assign({
              timerange:timerange,
            }, res.data.data);
            data.examtype = String(data.examtype);
            data.timelimit = String(data.timelimit);
            data.grademethod = String(data.grademethod);
            data.shufflequestions = String(data.shufflequestions);
            data.preferredbehaviour = String(data.preferredbehaviour);
            data.shufflequestions = String(data.shufflequestions);
            this.ruleForm = data;
          }
        }).catch( error => {
          this.$message({type: 'error', message: error.message});
        });
      }
  },
    methods: {
      loadSections() {
        let params = {
          'module': 'service',
          'controller': 'Classroom_Sections_Con',
          'action': 'getSections',
          'id': this.Const.courseData.query.d
        };
        console.log(params);
        this.$ajax.post(this.Const.apiUrl,params).then( res => {
          console.log(res);
          let resData = res.data;
          if (resData.status == 0) {
            this.sections = resData.data;
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '获取章节失败！'});
          };
        }).catch( error =>{
          this.$message({type: 'error', message: error.message});
        })
        /* for (let key in this.sections) {
          for (let k in this.sections[key].children) {
            this.sections[key].child[k].leaf = true;
          }
        }; */
      },
      inArray(array , object){
        for ( let item of array )
        {
          if (item == object)
          return true;
        }
        return false;
      },
      splitsection(base){
        let section = [];
        let sections = [];
        for (let item of base) {
          if (item.pid)
          {
            let exist = this.inArray(section,item.pid);
            if (! exist)
            section.push(item.pid);
            exist = this.inArray(sections, item.id)
            if (! exist)
            sections.push(item.id);
          }else{
            let exist = this.inArray(section,item.id);
            if (! exist)
            section.push(item.id);
          }
        }
        return [section,sections];
      },
      handleCheckChange(ref,qtype) {
        let base = [];
        let section = [];
        let sections = [];
        if (qtype == null)
        {
          let data = this.$refs[ref].getCheckedNodes();
          base = this.splitsection(data);
          section = base[0];
          sections = base[1];
          this.ruleForm.section = section;
          this.ruleForm.sections = sections;
        }else{
          let data = this.$refs[ref][0].getCheckedNodes();
          base = this.splitsection(data);
          section = base[0];
          sections = base[1];
        };
        for (let key in this.questions)
        {
          if (this.questions[key].qtype == qtype)
          {
            this.questions[key].section = section;
            this.questions[key].sections = sections;
            break;
          }
        }
      },
      back(){
        this.$router.push({ path: '/position' });
      },
      examtypeChange(){
        if (this.ruleForm.examtype == 0){
          this.ruleForm.attempts = 1;
          this.attemptsbool = true;
        }else{
          this.attemptsbool = false;
        }
      },
      submitForm(formName) {
        let params = Object.assign({
          timeopen: this.ruleForm.timerange[0],
          timeclose: this.ruleForm.timerange[1],
          classroom: this.Const.courseData.query.d
        }, this.ruleForm);
        params.module = 'exam';
        params.controller ='quiz';
        if (this.$route.query.id)
        {
          params.id = this.$route.query.id;
          params.action = 'editQuiz';
        } else {
          params.questions = this.questions;
          params.action = 'createQuiz';
        }
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.$ajax.post(this.Const.apiUrl,params).then( res => {
              if (res.data.status == 0) {
                if (this.ruleForm.shufflequestions == 1) {
                  this.$router.push({ path: `/posi/additems?id=${res.data.data['quiz']}` });
                }
              }
            }).catch( error => {
              this.$message({type: 'error', message: error.message});
            })
          } else { return false; }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
      },
      // 弹窗页面确定
      getTablePaperData () {
        this.dialogTableVisible = false;
      },
      changeShuffle () {
        this.ruleForm.questions = '';
        if (this.ruleForm.shufflequestions == 0)
        {
          this.dialogTableVisible = true;
        }
      },
      cancelQuestConf () {
        this.dialogTableVisible = false;
        this.ruleForm.shufflequestions = '1';
        this.ruleForm.questions = '';
      }
    }
  }
</script>

<style lang="scss">
#addpaper {
  .title {
    @include middelH(50px);
    border-bottom: 1px solid gray;
    @include setTitle;
    i {
      padding-right: 10px;
    }
  }
  .add-paper-wrap {
    width: 70%;
    margin: 30px auto;
  }
}
.add-paper-wrap {
  text-align: center;
}
.el-input__inner {
  width:400px
}
.el-textarea {
    display: inline-block;
    width: 400px;
    vertical-align: bottom;
    font-size: 14px;
}
.el-form{
  display: inline-block
}
</style>
