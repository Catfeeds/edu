<template>
  <section id="chapters-index">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">章节管理</a>
        </li>
      </ul>
    </div>
    <div class="chapters-list">
      <el-tree :data="chaptersList" node-key="id" :default-expand-all="false" :highlight-current="true"
        @node-drag-start="handleDragStart"
        @node-drag-enter="handleDragEnter"
        @node-drag-leave="handleDragLeave"
        @node-drag-over="handleDragOver"
        @node-drag-end="handleDragEnd"
        @node-drop="handleDrop"
        :props="defaultProps"
        draggable
        :allow-drop="allowDrop"
        :allow-drag="allowDrag">
          <div class="custom-tree-node" slot-scope="{ node, data }">
            <span>{{ node.label }}</span>
            <span class="oper">
              <el-button title="添加" type="text" size="mini" @click.stop="() => append(data)"><i class="el-icon-plus"></i></el-button>
              <el-button title="删除" type="text" size="mini" @click.stop="() => remove(node, data)"><i class="el-icon-delete"></i></el-button>
              <el-button title="编辑" type="text" size="mini" @click.stop="() => edit(node, data)"><i class="el-icon-edit"></i></el-button>
              <el-button title="课件" type="text" size="mini" @click.stop="() => addCourseware(node, data)"><i class="el-icon-goods"></i></el-button>
            </span>
          </div>
      </el-tree>
      <div class="add-chap">
        <el-button type="primary" plain round @click="addChaperts">添加章</el-button>
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
  </section>
</template>
<script>
export default {
  data () {
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
    return {
      // 加载等待
      loading: false,
      chaptersList: [
        /* {
          id: 1,
          name: '一级 1',
          child: [{
            id: 4,
            name: '二级 1-1'
          }]
        } */
      ],
      defaultProps: {
        children: 'child',
        label: 'name'
      },
      // 添加章
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
      formLabelWidth: '120px',
      // 修改章
      isEditSecs: false,
      // 添加节
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
      // 修改节
      isEditSec: false
    }
  },
  created() {
    this.requestAllChapters();
  },
  mounted () {
    
  },
  methods: {
    // 获取章节数据
    requestAllChapters () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'Course_Sections_Con',
        action: 'queSections',
        courseid: this.$route.query.d
      }).then(response => {
        loading.close();
        let resData = response.data;
        if (resData.status == 0) {
          this.chaptersList = resData.data;
        } else {
          this.chaptersList = [];
          this.$message({type: 'info', message: resData.data ? resData.data : '获取章节失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: '请求出问题了，检查本地网络！'});
      });
    },
    handleDragStart(node, ev) {
      //console.log('drag start', node);
    },
    handleDragEnter(draggingNode, dropNode, ev) {
      //console.log('tree drag enter: ', dropNode.label);
    },
    handleDragLeave(draggingNode, dropNode, ev) {
      //console.log('tree drag leave: ', dropNode.label);
    },
    handleDragOver(draggingNode, dropNode, ev) {
      //console.log('tree drag over: ', dropNode.label);
    },
    handleDragEnd(draggingNode, dropNode, dropType, ev) {
      //console.log('tree drag end: ', dropNode && dropNode.label, dropType);
    },
    handleDrop(draggingNode, dropNode, dropType, ev) {
      console.log('tree drop: ', draggingNode, dropNode, dropType, ev);
      let sortArr = [];
      if (dropType == 'after') {
        let childs = (_ => {
          for (let j = 0, sections; sections = this.chaptersList[j]; j++) {
            if (sections['id'] == draggingNode.data.pid) {
              return sections.child ? sections.child : [];
            }
          }
        })();
        for (let i = draggingNode.id-2; i <= dropNode.id-2; i++ ) {
          sortArr.push({id: childs[i]['id'], sort: i + 1});
        }
      } else if (dropType == 'defore') {

      }
      console.log(sortArr);
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Course_Sections_Con',
        action: 'rankSections',
        sort: sortArr
      }).then(response => {
        let resData = response.data;
        if (resData.status == 0) {
          this.$message({type: 'success', message: '排序成功！'});
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '排序失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: error.message});
      });
    },
    // 拖拽时判定目标节点能否被放置
    allowDrop(draggingNode, dropNode, type) {
      // console.log('drop', draggingNode, dropNode, type);
      // 二级以下不能拖拽到一级
      return (dropNode.level > 1) ? true : false;
    },
    // 判断节点能否被拖拽
    allowDrag(draggingNode) {
      // console.log('drag', draggingNode);
      // 只允许二级拖拽
      return (draggingNode.level > 1) ? true : false;
    },
    addChaperts () {
      this.dialogFVASecions = true;
      this.secsTitle = '添加章';
    },
    append(data) {
      console.log(data);
      if (data.pid === '' || data.pid == '0') {
        // 添加节
        this.addSectionFrom.pid = data.id;
        this.addSectionFrom.sort = data.child ? data.child.length + 1 : 1;
        this.secTitle = '添加节';
        this.dialogFVASecion = true;
      } else {
        // 添加节下的资源
        // TODO
      }
    },
    remove(node, data) {
      if (data.pid == '' || data.pid == '0') {
        // 删除改章
        this.$confirm('删除章，其下的节也会被移除, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$ajax.post(this.Const.apiurl, {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'delSections',
            id: data.id,
            type: '2'
          }).then(response => {
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data || '删除成功！'});
              this.requestAllChapters();
            } else {
              this.$message({type: 'info', message: resData.data || '删除失败！'});
            }
          }).catch(error => {
            this.$message({type: 'error', message: '删除失败！'});
          });
        }).catch(() => {});
      } else {
        // 删除改节
        this.$confirm('此操作将永久删除该小节, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$ajax.post(this.Const.apiurl, {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'delSections',
            id: data.id,
            type: '1'
          }).then(response => {
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data || '删除成功！'});
              this.requestAllChapters();
            } else {
              this.$message({type: 'info', message: resData.data || '删除失败！'});
            }
          }).catch(error => {
            this.$message({type: 'error', message: '删除失败！'});
          });
        }).catch(() => {});
      }
    },
    // 添加课件 
    addCourseware (node, data) {
      this.$router.push({
        path: '/course/courseware',
        query: {
          id: data.courseid,
          type: 1,
          sec: data.id
        }
      });
    },
    // 添加章
    addSections () {

    },
    // 提交章
    submitFormASections (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let params = {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'addSections',
            courseid: this.$route.query.d,
            pid: '',
            type: 0,
            dataList: (_ => {
              let arr = [];
              arr.push({
                name: this.addSectionsFrom.name, 
                section: this.addSectionsFrom.section, 
                summary: this.addSectionsFrom.summary, 
                sort: this.chaptersList.length + 1
              });
              return arr;
            })()
          };
          this.$ajax.post(this.Const.apiurl, params).then(response => {
            let resData = response.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: '添加成功！'});
              this.dialogFVASecions = false;
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
    // 修改章
    edit (node, data) {
      if (data.pid == '' || data.pid == '0') {
        // 修改章
        this.addSectionsFrom = data;
        this.secsTitle = '修改章';
        this.isEditSecs = true;
        this.dialogFVASecions = true;
      } else {
        // 修改节
        this.addSectionFrom = data;
        this.secTitle = '修改节';
        this.isEditSec = true;
        this.dialogFVASecion = true;
      }
    },
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
    // 编辑节
    submitFormESection (formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let params = {
            module: 'service',
            controller: 'Course_Sections_Con',
            action: 'upSections',
            courseid: this.$route.query.d,
            id: this.addSectionFrom.id,
            type: 1,
            name: this.addSectionFrom.name, 
            summary: this.addSectionFrom.summary
          };
          this.$ajax.post(this.Const.apiurl, params).then(response => {
            let resData = response.data;
            console.log(response);
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data ? resData.data : '修改成功！'});
              this.dialogFVASecion = false;
              this.requestAllChapters();
            } else {
              this.$message({type: 'info', message: resData.data ? resData.data : '修改失败！'});
            }
          }).catch(error => {
            this.$message({type: 'error', message: error.message});
          });
        } else { return false; }
      });
    }
  },
  watch: {
　　'$route': {
      handler (to, from) {
        if (to.query.d) {
          this.requestAllChapters();
        }
      },
      deep: true
    }
  }
}
</script>
<style lang="scss">
.el-tree {
  .el-tree-node__content {
    height: 50px;
    line-height: 50px;
  }
  .el-tree-node__children .el-tree-node__content{
    height: 40px;
    line-height: 40px;
    margin-bottom: 5px;
    background-color: rgb(215, 232, 238);
    &:hover {
      background-color: rgb(68, 68, 109);
      color: #fff;
    }
  }
  .custom-tree-node {
    width: 100%;
    .oper{
      float: right;
      margin-right: 20px;
    }
  }
}
.add-chap {
  text-align: center;
  margin-top: 20px;
}
</style>
