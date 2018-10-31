<template>
  <section id="chapters">
    <!--课程列表 start-->
    <div class="course-con">
      <div class="container">
          <!--课程的详细安排列表内容-->
        <div class="course-list">
          <div class="mod-chapters">
            <!--章节-->
            <el-menu default-active="1-4-1" class="el-menu-vertical-demo">
              <el-submenu :index="item.id" v-for="(item, index) in chaptersList" :key="index">
                <template slot="title">
                  <i class="el-icon-menu"></i>
                  <span slot="title">{{item.name}}</span>
                </template>
                <el-menu-item v-for="(item2, index2) in item.children" :key="index2" :index="item2.id">
                  <i class="el-icon-document"></i>
                  <span>{{item2.name}}</span>
                  <span  class="section_operateion_panel">
                    <a @click="editSession(index, index2)">编辑</a>
                    <a @click="deleteSession(item2.id)">删除</a>
                  </span>
                </el-menu-item>
                <el-menu-item style="color:blue" @click="addSection(index)">添加节</el-menu-item>
              </el-submenu>
            </el-menu>
            <!-- 添加章节 start -->
            <div class="add-chapter">
              <h3>
                <strong v-bind:courseId="curCourse.id" class="chapter-title" id="add-chapterlib" v-on:click="addSections()"><a href="javascript:void(0);">添加章</a></strong>
              </h3>
            </div>
            <!-- 添加章节 end -->
          </div>
        </div>
      </div>
    </div>
    <!-- 添加/修改 章 -->
    <el-dialog :title="secsTitle" :visible.sync="dialogFVASecions" width="40%">
      <el-form :model="addSectionsFrom" label-suffix :status-icon="true" :rules="addSectionsFromRules" ref="addSectionsFrom">
        <el-form-item label="章节名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="addSectionsFrom.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="章节课时" :label-width="formLabelWidth" prop="section">
          <el-input v-model.number="addSectionsFrom.section" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="章节概要" :label-width="formLabelWidth" prop="summary">
          <el-input type="textarea" v-model="addSectionsFrom.summary" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFVASecions = false">取 消</el-button>
        <el-button v-if="isEditSecs" type="primary" @click="submitFormESections('addSectionsFrom')">修 改</el-button>
        <el-button v-else type="primary" @click="submitFormASections('addSectionsFrom')">添 加</el-button>
      </div>
    </el-dialog>
    <!-- 添加/修改 节 -->
    <el-dialog :title="secTitle" :visible.sync="dialogFVASecion" width="40%">
      <el-form :model="addSectionFrom" label-suffix :status-icon="true" :rules="addSectionFromRules" ref="addSectionFrom">
        <el-form-item label="小节名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="addSectionFrom.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="小节概要" :label-width="formLabelWidth" prop="summary">
          <el-input type="textarea" v-model="addSectionFrom.summary" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFVASecion = false">取 消</el-button>
        <el-button v-if="isEditSec" type="primary" @click="submitFormESection('addSectionFrom')">修 改</el-button>
        <el-button v-else type="primary" @click="submitFormASection('addSectionFrom')">添 加</el-button>
      </div>
    </el-dialog>


    <!--课程列表 end-->

    <!-- 分页 start-->
    
    <!-- 分页 end-->
</section>
</template>

<script>
export default {
  data: function() {
        var checkSection = (rule, value, callback) => {
      if (!value) {
        return callback(new Error('课时不能为空'));
      }
      setTimeout(() => {
        if (!Number.isInteger(value)) {
          callback(new Error('课时只能是数字值'));
        } else {
          if (value < 1) {
            callback(new Error('课时要大于0'));
          } else {
            callback();
          }
        }
      }, 500);
    };
    var thisVue = this;
    return {
      formLabelWidth: '120px',
      isEditSec: false,
      //章
      dialogFVASecions: false,
      secsTitle: '添加章',
      addSectionsFrom: {
        name: '',
        section: 1,
        summary: ''
      },
      addSectionsFromRules: {
        name: [
          { required: true, message: '请输入章节名称', trigger: 'blur' }
        ],
        section: [
          { validator: checkSection, trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请输入章节概要', trigger: 'blur' }
        ]
      },
      //节
      dialogFVASecion: false,
      secTitle: '添加节',
      addSectionFrom: {
        name: '',
        summary: '',
        pid: '',
        sort: 0
      },
      addSectionFromRules: {
        name: [
          { required: true, message: '请输入小节名称', trigger: 'blur' }
        ],
        summary: [
          { required: true, message: '请输入小节概要', trigger: 'blur' }
        ]
      },


      curCourse: "", // ,
      chaptersList: [
        {
          id: '1231231',
          name: '第一章',
          children: [
            {
              id: 'sjfusdfs',
              name: '第1-1节'
            },{
              id: 'sjfus32dfs',
              name: '第1-2节'
            }
          ]
        }
      ]
    };
  },
  mounted: function() {
    let params = {
      'module': 'service',
      'controller': 'Classroom_Sections_Con',
      'action': 'getSections',
      'id': '73BD19F3DD9B33A8EC12691857A11C71'
      //'id': this.Const.courseData.query.d
      };
    this.$ajax.post(this.Const.apiurl, params).then( res => {
      this.chaptersList = res.data.data
    }).catch();
  },
  methods: {
        // 编辑章
    submitFormESections (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let params = {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'upSections',
            courseid: this.$route.query.d,
            pid: '',
            type: 0,
            id: this.addSectionsFrom.id,
            name: this.addSectionsFrom.name, 
            section: this.addSectionsFrom.section,
            summary: this.addSectionsFrom.summary
          };
          this.$ajax.post(this.Const.apiurl, params).then(response => {
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data ? resData.data : '修改成功！'});
              this.dialogFVASecions = false;
              this.requestAllChapters();
            } else {
              this.$message({type: 'info', message: resData.data ? resData.data : '修改失败！'});
            }
          }).catch(error => {
            this.$message({type: 'error', message: error.message});
          });
        } else { return false; }
      });
    },
    // 提交节
    submitFormASection (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let params = {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'addSections',
            courseid: this.$route.query.d,
            pid: this.addSectionFrom.pid,
            type: 1,
            dataList: (_ => {
              let arr = [];
              arr.push({
                name: this.addSectionFrom.name, 
                summary: this.addSectionFrom.summary,
                sort: this.addSectionFrom.sort
              });
              return arr;
            })()
          };
          this.$ajax.post(this.Const.apiurl, params).then(response => {
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: '添加成功！'});
              this.dialogFVASecion = false;
              this.requestAllChapters();
            } else {
              this.$message({type: 'info', message: resData.data ? resData.data : '添加失败！'});
            }
          }).catch(error => {
            this.$message({type: 'error', message: error.message});
          });
        } else { return false; }
      });
    },
    addSections () {
      this.dialogFVASecions = true;
      this.secsTitle = '添加章';
    },
    addSection (index) {
      this.dialogFVASecion = true;
      this.secsTitle = '添加节';
    },
    editSession (sectionsindex, sectionindex) {
      this.dialogFVASecion = true;
      this.addSectionFrom = this.chaptersList[sectionsindex][sectionindex];
      this.secsTitle = '修改节';
    },
    deleteSession (id) {
      let params = {
        module: 'service',
        controller: 'Course_Sections_Con',
        action: 'addSections',
        id: id
      };
      this.$ajax.post(this.Const.apiurl, params).then( res => {

      }).catch( res => {

      });
    }
  }
};
</script>

<style lang="scss">
#chapters {
  .el-submenu {
    background: #ecf5ff;
    margin: 0 0 5px 0;
    .el-submenu__title:hover {
      background-color: rgb(175, 171, 171);
    }
    .el-menu-item:focus, .el-menu-item:hover{
      background-color: rgb(175, 171, 171);
    }
    .el-menu-item.is-active{
      color: #fff;
    }
    .section_operateion_panel{
      right: 0px;
      position: absolute;
      a{
        color: blue;
      }
    }
  }
}
</style>
