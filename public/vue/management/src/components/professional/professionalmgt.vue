<template>
  <div id="professionalmgt">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">专业管理中心</a>
        </li>
      </ul>
    </div>
    <section class="pro-mgt">
      <div class="operation-bar" style="border-top: none;">
        <div class="my-cascader">
          <el-cascader change-on-select @change="fnSelOrgChange" v-model="selOrg" :props="props" :options="organizationList"></el-cascader>
        </div>
        <el-input placeholder="请输入专业名称查找" @keyup.enter.native="fnReqProByOrgid" v-model="search.selectCon" class="input-with-select">
          <el-button slot="append" @click="fnReqProByOrgid" icon="el-icon-search">搜索</el-button>
        </el-input>
        <el-tag :key="index" v-for="(item,index) in search.tags" size="small" :closable="true" @close="tagClose(item.key, index)">{{item.label}} : {{item.value}}</el-tag>
      </div>
      <div class="wrap-list">
        <div class="title"></div>
        <div class="list-con">
          <el-menu v-if="conList.length" class="el-menu-vertical" v-loading="loading">
            <el-submenu v-for="(item, i) in conList" :key="i" :index="item.id">
              <template slot="title">
                <i class="el-icon-document"></i>
                <span slot="title">{{ item.name }}</span>
                <div style="float: right; margin-right: 100px;">
                  <el-tooltip v-if="item.ischild" class="item" effect="dark" content="添加" placement="top-start">
                    <el-button @click.stop="fnAdd(item.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                  </el-tooltip>
                  <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                    <el-button @click.stop="fnEdit(item.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                  </el-tooltip>
                  <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                    <el-button @click.stop="fnView(item.id)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                  </el-tooltip>
                  <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                    <el-button @click.stop="fnDelete(item.id)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                  </el-tooltip>
                </div>
              </template>
            </el-submenu>
          </el-menu>
          <p class="not-profess" v-else>该机构下暂无专业！</p>
          <div class="add">
            <el-button @click="addClassFromStart" size="small" type="primary" round>添加专业</el-button>
          </div>
        </div>
      </div>
    </section>

    <!-- 添加/修改专业弹窗 -->
    <el-dialog :title="addProForm.openTitle" :visible.sync="dialogAddProFormVisible">
      <el-breadcrumb separator-class="el-icon-arrow-right">
        <el-breadcrumb-item v-for="(item, index) in addProForm.breadcrumbList" :key="index">{{ item.name }}</el-breadcrumb-item>
      </el-breadcrumb>
      <el-form :status-icon="true" ref="addProform" :model="addProForm.form" :rules="addProForm.rules">
        <el-form-item label="专业名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="addProForm.form.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="专业编码" :label-width="formLabelWidth" prop="code">
          <el-input :readonly="isEdit" v-model="addProForm.form.code" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="专业描述" :label-width="formLabelWidth" prop="describe">
          <el-input type="textarea" v-model="addProForm.form.describe" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogAddProFormVisible = false; isEdit = false">取 消</el-button>
        <el-button v-if="!isEdit" type="primary" @click="fnAddSubmit('addProform')">确 定</el-button>
        <el-button v-else type="primary" @click="fnEditSubmit('addProform')">修 改</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    data () {
      const This = this;
      return {
        search: {
          tags: [
            /* {
              key: '12312',
              label: '搜索标签',
              value: '搜索内容'
            } */
          ],
          selectCon: ''
        },
        // 选中的机构id路劲，默认当前登录用户的机构id
        selOrg: (_ => {
          let arr = [];
          arr.push(This.Const.userMsg.pid);
          return arr;
        })(),
        // 加载等待
        loading: false,
        // 机构下拉
        organizationList: [
          /*{
            id: 'B608AA535E0EC9C5DDBC8548AF39D4CB',
            name: '电子科技大学成都学院',
            children: [
              {
                id: (Math.random()*100).toString(),
                name: '测试1-1',
                children: [
                  {
                    id: (Math.random()*100).toString(),
                    name: '测试1-1-1',
                  }
                ]
              },{
                id: (Math.random()*100).toString(),
                name: '测试1-2',
              }
            ]
          },{
            id: 'B608AA535E0E1215DDBC8548AF39D4CB',
            name: '电子科技大学',
            children: []
          }*/
        ],
        props: {
          value: 'id',
          label: 'name',
          children: 'children'
        },
        conList: [
          {
            id: (Math.random()*100).toString(),
            name: '名称1'
          }
        ],
        // 添加专业
        addProForm: {
          openTitle: "",
          breadcrumbList: [],
          form: {
            name: '',
            code: '',
            describe: '',
          },
          rules: {
            name: [{ required: true, message: '请输入专业名称', trigger: 'blur' }],
            code: [{ required: true, message: '请输入专业编号，添加后不能修改', trigger: 'blur' }],
            describe: [{ required: true, message: '请输入对该专业的描述', trigger: 'blur' }]
          }
        },
        dialogAddProFormVisible: false,
        formLabelWidth: '120px',
        // 弹窗是判断是否为编辑状态
        isEdit: false
      }
    },
    mounted () {
      this.fnReqOrgByPrev();
      this.fnReqProByOrgid();
    },
    methods: {
      // 弹窗中的面包屑导航
      transformBread () {
        const treeToArr = (treeArr) => {
          return treeArr.map(item => {
            if (item.children && item.children.length) {
              let childArr = treeToArr(item.children);
              delete item.children;
              return childArr.concat(item);
            } else {
              return item;
            }
          });
        }
        // 平铺多维数组
        const flattenArr = arr => {
            // 平铺二维数组
            const flattened = [].concat(...arr);
            // 迭代平铺多维
            return flattened.some(item => Array.isArray(item)) ? flattenArr(flattened) : flattened;
        }
        const deepCopy = function(obj) {
          if (typeof obj !== 'object') return;
          var newObj = obj instanceof Array ? [] : {};
          for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
              newObj[key] = typeof obj[key] === 'object' ? deepCopy(obj[key]) : obj[key];
            }
          }
          return newObj;
        }
        let organizationArr = flattenArr(treeToArr(deepCopy(this.organizationList)));
        this.addProForm.breadcrumbList = organizationArr.filter(item => {
          for (let i = 0; i < this.selOrg.length; i++) {
            if (this.selOrg[i] === item.id) {
              return true;
            }
          }
          return false;
        }).reverse();
      },
      addClassFromStart () {
        this.transformBread();
        this.dialogAddProFormVisible = true;
      },
      tagClose (key, index) {
        this.search.tags.splice(index, 1);
        this.search.selectCon = '';
      },
      // 编辑专业
      fnEdit (id) {
        let This = this;
        let promise = new Promise((resolve, reject) => {
          This.$ajax.post(This.Const.apiurl, {
            module: 'Service',
            controller: 'Profession_Con',
            action: 'getProInfo',
            pro_id: id
          }).then((res) => {
            resolve(res);
          }).catch((error) => {
            reject(error);
          });
        });
        promise.then((res) => {
          let resData = res.data;
          console.log(resData);
          if (resData.status == 0) {
            // console.log('resData.data.pro', resData.data.pro);
            // This.addProForm = resData.data.pro;
            This.addProForm.form = resData.data.pro;
            /*This.addProForm.code = resData.data.pro.code;
            This.addProForm.describe = resData.data.pro.describe;
            This.$set(This.addProForm, 'id', resData.data.pro.id);
            This.$set(This.addProForm, 'organization', resData.data.pro.organization);*/
            This.isEdit = true;
            This.transformBread();
            This.dialogAddProFormVisible = true;
          } else {
            This.$message({type: 'error', message: resData.data || '请求专业信息失败！'});
          }
        }, (error) => {
          This.$message({type: 'error', message: '请求专业详细信息错误！'});
        });
      },
      fnView (id) {
        // console.log('View', id);
        this.$router.push({
          path: '/library/userlist/pro',
          query: { d: id }
        });
      },
      fnDelete (id) {
        this.$confirm('确认删除该专业！', '温馨提示', {
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.$ajax.post(this.Const.apiurl, {
            module: 'Service',
            controller: 'Profession_Con',
            action: 'delProfession',
            ids: (_ => {
              let arr = [];
              arr.push(id);
              return arr;
            })()
          }).then((res) => {
            let resData = res.data;
            if (!resData.status) {
              this.$message({type: 'success', message: resData.data});
              this.fnReqProByOrgid();
            } else {
              this.$message({type: 'error', message: resData.data});
            }
          }).catch((error) => {
            this.$message({type: 'error', message: '请求删除专业失败！'});
          });
        }).catch(() => {
          this.$message({ type: 'info', message: '已取消删除' });
        });
      },
      // 请求所有机构信息
      fnReqOrgByPrev () {
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
      fnSelOrgChange () {
        this.search.selectCon = '';
        this.fnReqProByOrgid();
        this.loading = true;
      },
      // 根据机构id 请求机构下的所有专业数据
      fnReqProByOrgid () {
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'Profession_Con',
          // action: 'getProFOrg',
          action: 'index',
          organization: this.selOrg[this.selOrg.length-1],
          name: this.search.selectCon
        }).then((res) => {
          this.loading = false;
          let resData = res.data;
          if (!resData.status) {
            if (typeof resData.data !== 'string') {
              this.conList = resData.data;
            } else {
              this.conList = [];
            }
          } else {
            this.$message.error('请求专业数据失败！');
          }
        }).catch((error) => {
          this.loading = false;
          this.$message.error('请求专业数据报错！');
        });
      },
      // 添加专业
      fnAddSubmit (refform) {
        let This = this;
        this.$refs[refform].validate((valid) => {
          if (valid) {
            const loading = this.$loading({
              lock: true,
              text: 'Loading',
              spinner: 'el-icon-loading',
              background: 'rgba(0, 0, 0, 0.7)'
            });
            let params = Object.assign({
              module: 'Service',
              controller: 'Profession_Con',
              action: 'addProfession',
              code: this.addProForm.form.code,
              describe: this.addProForm.form.describe,
              name: this.addProForm.form,name
            }, {organization: this.selOrg[this.selOrg.length-1]});
            this.$ajax.post(this.Const.apiurl, params).then((res) => {
              loading.close();
              let resData = res.data;
              if (resData.status == 0) {
                this.$message({type: 'success', message: '添加成功！'});
                this.fnReqProByOrgid();
                this.dialogAddProFormVisible = false;
              } else {
                this.$message({type: 'error', message: resData.data || '添加失败！'});
              }
            }).catch((error) => {
              this.$message.error('请求添加专业报错！');
            });
          } else { return false; }
        });
      },
      // 修改
      fnEditSubmit (refform) {
        let This = this;
        this.$refs[refform].validate((valid) => {
          if (valid) {
            const loading = this.$loading({
              lock: true,
              text: 'Loading',
              spinner: 'el-icon-loading',
              background: 'rgba(0, 0, 0, 0.7)'
            });
            let params = Object.assign({}, {
              module: 'Service',
              controller: 'Profession_Con',
              action: 'upProfession',
              name: this.addProForm.form.name,
              code: this.addProForm.form.code,
              describe: this.addProForm.form.describe,
            });
            this.$ajax.post(this.Const.apiurl, params).then((res) => {
              loading.close();
              let resData = res.data;
              if (!resData.status) {
                this.$message({type: 'success', message: resData.data || '修改成功！',onClose () {
                  This.fnReqProByOrgid();
                  This.dialogAddProFormVisible = false;
                }});
              } else {
                this.$message({type: 'error', message: resData.data || '修改失败！'});
              }
            }).catch((error) => {
              this.$message.error('请求修改专业报错！');
            });
          } else { return false; }
        });
      }
    },
    watch: {
      dialogAddProFormVisible: {
        handler (current, old) {
          // console.log('watch dialogAddProFormVisible', old, current);
          // 重置表单，删除验证结果
          if (current && !this.isEdit && this.$refs['addProform']) {
            this.$refs['addProform'].resetFields();
            this.addProForm.form.name = this.addProForm.form.code = this.addProForm.form.describe = "";
          }
        }
      },
      isEdit: {
        handler (newVal, oldVal) {
          this.addProForm.openTitle = newVal ? '编辑专业' : '新增专业';
        },
        immediate: true
      }
    }
  }
</script>

<style lang="scss">
.el-breadcrumb{
  @include middelH(50px);
  margin-bottom: 10px;
  margin-left: 50px;
}
.operation-bar {
  margin-top: 10px;
  .el-input-group{
    width: 40%;
  }
}
.wrap-list {
  margin-top: 10px;
  .title {
    @include middelH(50px);
    background: rgb(84, 92, 100);
    color: inherit;
  }
  .list-con {
    box-sizing: border-box;
    border: 1px solid rgb(84, 92, 100);
    .el-menu-vertical {
      width: 100%;
      border: none;
      .el-submenu__title {
        @include middelH(50px);
      }
    }
    .not-profess {
      @include middelH(50px);
      text-align: center;
    }
    .add {
      text-align: center;
      button {
        margin: 10px auto;
      }
    }
  }
}
.my-cascader {
  width: 300px;
  float: left;
  margin-right: 10px;
  .el-cascader{
    width: 100%;
  }
}
</style>
