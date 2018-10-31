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
      <!-- <div class="operation-bar" style="border-top: none;">
        <el-input placeholder="请输入名称查找" v-model="search.selectCon" class="input-with-select">
          <el-button slot="append" @click="fnSearchByName" icon="el-icon-search">搜索</el-button>
        </el-input>
        <el-tag :key="index" v-for="(item,index) in search.tags" size="small" :closable="true" @close="tagClose(item.key, index)">{{item.label}} : {{item.value}}</el-tag>
      </div> -->
      <div class="wrap-list">
        <div class="title">
          <div class="breadcrumbs clearfix fl">
            <el-breadcrumb separator-class="el-icon-arrow-right">
              <el-breadcrumb-item :key="index" v-for="(item, index) in breadcrumbList">{{ item }}</el-breadcrumb-item>
            </el-breadcrumb>
          </div>
          <!-- <el-radio-group size="small" v-model="isCollapse" style="float: right;margin: 9px 5px 0 0;">
            <el-radio-button :label="true">展开</el-radio-button>
            <el-radio-button :label="false">收起</el-radio-button>
          </el-radio-group> -->
        </div>
        <div class="list-con" v-loading="loading">
          <el-menu class="el-menu-vertical" @open="handleOpen" @close="handleClose">
            <el-submenu v-for="(item, i) in conList" :key="i" :index="item.id">
              <template slot="title">
                <i :class="{'el-icon-menu': item.children && item.children.length, 'el-icon-document': !item.children || !item.children.length}"></i>
                <span slot="title">{{ item.name }}</span>
                <div style="float: right; margin-right: 100px;">
                  <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                    <el-button @click.stop="fnAdd(item.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                  </el-tooltip>
                  <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                    <el-button @click.stop="fnEdit(item.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                  </el-tooltip>
                  <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                    <el-button @click.stop="fnView(item.id)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                  </el-tooltip> -->
                  <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                    <el-button @click.stop="fnDelete(item.id, item.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                  </el-tooltip>
                </div>
              </template>
              <el-submenu v-if="item.children.length" v-for="(item1, n) in item.children" :key="n"  :index="item1.id">
                <template slot="title">
                  <i :class="{'el-icon-menu': item1.children, 'el-icon-document': !item1.children}"></i>
                  <span slot="title">{{ item1.name }}</span>
                  <div style="float: right; margin-right: 100px;">
                    <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                      <el-button @click.stop="fnAdd(item1.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                      <el-button @click.stop="fnEdit(item1.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                    </el-tooltip>
                    <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                      <el-button @click.stop="fnView(item1.id, item1.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                    </el-tooltip> -->
                    <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                      <el-button @click.stop="fnDelete(item1.id, item1.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                    </el-tooltip>
                  </div>
                </template>
                <el-submenu v-if="item1.children.length" v-for="(item2, m) in item1.children" :key="m"  :index="item2.id">
                  <template slot="title">
                    <i :class="{'el-icon-menu': item2.children, 'el-icon-document': !item2.children}"></i>
                    <span slot="title">{{ item2.name }}</span>
                    <div style="float: right; margin-right: 100px;">
                      <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                        <el-button @click.stop="fnAdd(item2.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                      </el-tooltip>
                      <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                        <el-button @click.stop="fnEdit(item2.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                      </el-tooltip>
                      <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                        <el-button @click.stop="fnView(item2.id, item2.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                      </el-tooltip> -->
                      <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                        <el-button @click.stop="fnDelete(item2.id, item2.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                      </el-tooltip>
                    </div>
                  </template>
                  <el-submenu v-if="item2.children.length" v-for="(item3, j) in item2.children" :key="j"  :index="item3.id">
                    <template slot="title">
                      <i :class="{'el-icon-menu': item3.children, 'el-icon-document': !item3.children}"></i>
                      <span slot="title">{{ item3.name }}</span>
                      <div style="float: right; margin-right: 100px;">
                        <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                          <el-button @click.stop="fnAdd(item3.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                        </el-tooltip>
                        <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                          <el-button @click.stop="fnEdit(item3.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                        </el-tooltip>
                        <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                          <el-button @click.stop="fnView(item3.id, item3.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                        </el-tooltip> -->
                        <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                          <el-button @click.stop="fnDelete(item3.id, item3.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                        </el-tooltip>
                      </div>
                    </template>
                    <el-submenu v-if="item3.children.length" v-for="(item4, k) in item3.children" :key="k"  :index="item4.id">
                      <template slot="title">
                        <i :class="{'el-icon-menu': item4.children, 'el-icon-document': !item4.children}"></i>
                        <span slot="title">{{ item4.name }}</span>
                        <div style="float: right; margin-right: 100px;">
                          <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                            <el-button @click.stop="fnAdd(item4.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                          </el-tooltip>
                          <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                            <el-button @click.stop="fnEdit(item4.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                          </el-tooltip>
                          <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                            <el-button @click.stop="fnView(item4.id, item4.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                          </el-tooltip> -->
                          <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                            <el-button @click.stop="fnDelete(item4.id, item4.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                          </el-tooltip>
                        </div>
                      </template>
                      <el-submenu v-if="item4.children.length" v-for="(item5, h) in item4.children" :key="h"  :index="item5.id">
                        <template slot="title">
                          <i :class="{'el-icon-menu': item5.children, 'el-icon-document': !item5.children}"></i>
                          <span slot="title">{{ item5.name }}</span>
                          <div style="float: right; margin-right: 100px;">
                            <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                              <el-button @click.stop="fnAdd(item5.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                            </el-tooltip>
                            <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                              <el-button @click.stop="fnEdit(item5.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                            </el-tooltip>
                            <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                              <el-button @click.stop="fnView(item5.id, item5.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                            </el-tooltip> -->
                            <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                              <el-button @click.stop="fnDelete(item5.id, item5.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                            </el-tooltip>
                          </div>
                        </template>
                        <el-submenu v-if="item5.children.length" v-for="(item6, h) in item5.children" :key="h"  :index="item6.id">
                          <template slot="title">
                            <i :class="{'el-icon-menu': item6.children, 'el-icon-document': !item6.children}"></i>
                            <span slot="title">{{ item6.name }}</span>
                            <div style="float: right; margin-right: 100px;">
                              <el-tooltip class="item" effect="dark" content="添加" placement="top-start">
                                <el-button @click.stop="fnAdd(item6.id)" plain size="mini" type="text" icon="el-icon-plus"></el-button>
                              </el-tooltip>
                              <el-tooltip class="item" effect="dark" content="编辑" placement="top-start">
                                <el-button @click.stop="fnEdit(item6.id)" plain size="mini" type="text" icon="el-icon-edit"></el-button>
                              </el-tooltip>
                              <!-- <el-tooltip class="item" effect="dark" content="查看成员" placement="top-start">
                                <el-button @click.stop="fnView(item6.id, item6.type)" plain size="mini" type="text" icon="el-icon-view"></el-button>
                              </el-tooltip> -->
                              <el-tooltip class="item" effect="dark" content="删除" placement="top-start">
                                <el-button @click.stop="fnDelete(item6.id, item6.name)" plain size="mini" type="text" icon="el-icon-delete"></el-button>
                              </el-tooltip>
                            </div>
                          </template>
                        </el-submenu>
                      </el-submenu>
                    </el-submenu>
                  </el-submenu>
                </el-submenu>
              </el-submenu>
            </el-submenu>
          </el-menu>
          <el-button size="small" type="primary" round v-if="conList.length == 0 && breadcrumbList.length" @click="fnAdd(breadcrumbList[breadcrumbList.length-1])">添加机构</el-button>
        </div>
      </div>
    </section>
    <!-- 添加/修改机构弹窗 -->
    <el-dialog :title="openTitle" :visible.sync="dialogAddOroFormVisible">
      <el-form :model="addOrgForm" ref="addOrgDom" :rules="addOrgFormRoules">
        <el-form-item label="机构名称" :label-width="formLabelWidth" prop="name">
          <el-input v-model="addOrgForm.name" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="机构编号" :label-width="formLabelWidth" prop="org_code">
          <el-input :readonly='isEdit' v-model="addOrgForm.org_code" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="机构行政编号" :label-width="formLabelWidth">
          <el-input :readonly="true" v-model="addOrgForm.area" auto-complete="off" placeholder="机构行政地址的编号"></el-input>
        </el-form-item>
        <el-form-item label="机构详细地址" :label-width="formLabelWidth" prop="address">
          <el-input v-model="addOrgForm.address" auto-complete="off" placeholder="机构详细地址"></el-input>
        </el-form-item>
        <el-form-item label="机构简介" :label-width="formLabelWidth" prop="summary">
          <el-input type="textarea" v-model="addOrgForm.summary" auto-complete="off"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogAddOroFormVisible = false; isEdit = false">取 消</el-button>
        <el-button v-if="!isEdit" type="primary" @click="fnAddSubmit('addOrgDom')">确 定</el-button>
        <el-button v-else type="primary" @click="fnEditSubmit('addOrgDom')">修 改</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  export default {
    data () {
      let This = this;
      return {
        userMsg: null,
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
        loading: false,
        breadcrumbList: (() => {
          if (this.Const.userMsg) {
            return new Array(this.Const.userMsg.org_name);
          } else { return ['--']; }
        })(),
        conList: [
          /* {
            id: '12312',
            name: '测试',
            children: []
          }
          {
            id: (Math.random()*100).toString(),
            name: '名称1',
            ischild: true,
            children: [
              {
                id: (Math.random()*100).toString(),
                name: '名称1-1',
                ischild: true,
                children: [
                  {
                    id: (Math.random()*100).toString(),
                    name: '名称1-1',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-2',
                    ischild: true,
                    children: []
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-3',
                    ischild: false
                  }
                ]
              },{
                id: (Math.random()*100).toString(),
                name: '名称1-2',
                ischild: true,
                children: [
                  {
                    id: (Math.random()*100).toString(),
                    name: '名称1-1',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-2',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-3',
                    ischild: false
                  }
                ]
              },{
                id: (Math.random()*100).toString(),
                name: '名称1-3',
                ischild: true,
                children: [
                  {
                    id: (Math.random()*100).toString(),
                    name: '名称1-1',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-2',
                    ischild: true,
                    children: [
                      {
                        id: (Math.random()*100).toString(),
                        name: '名称1-1',
                        ischild: false
                      },{
                        id: (Math.random()*100).toString(),
                        name: '名称1-2',
                        ischild: false
                      },{
                        id: (Math.random()*100).toString(),
                        name: '名称1-3',
                        ischild: false
                      }
                    ]
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-3',
                    ischild: false
                  }
                ]
              }
            ]
          },{
            id: (Math.random()*100).toString(),
            name: '名称2',
            ischild: true,
            children: [
              {
                id: (Math.random()*100).toString(),
                name: '名称1-1',
                ischild: '2'
              },{
                id: (Math.random()*100).toString(),
                name: '名称1-2',
                ischild: true,
                children: [
                  {
                    id: (Math.random()*100).toString(),
                    name: '名称1-1',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-2',
                    ischild: false
                  },{
                    id: (Math.random()*100).toString(),
                    name: '名称1-3',
                    ischild: false
                  }
                ]
              },{
                id: (Math.random()*100).toString(),
                name: '名称1-3',
                ischild: false
              }
            ]
          } */
        ],
        dialogAddOroFormVisible: false,
        isEdit: false,
        formLabelWidth: '200px',
        addOrgForm: {
          pid: '',
          name: '',
          area: '130423',
          address: '',
          summary: '',
          org_code: ''
        },
        addOrgFormRoules: {
          name: [
            { required: true, message: '请输入机构名称', trigger: 'blur' }
          ],
          org_code: [
            { required: true, message: '请输入机构编号', trigger: 'blur' }
          ],
          address: [
            { required: true, message: '请输入机构详细地址', trigger: 'blur' }
          ],
          summary: [
            { required: true, message: '请输入机构详细地址', trigger: 'blur' }
          ]
        },
        openTitle: (_ => {
          return This.isEdit ? '编辑机构' : '添加机构';
        })()
      }
    },
    mounted () {
      this.fnReqOrgByPrev();
      /* this.breadcrumbList = (function(){
          if (this.Const.userMsg) {
            return new Array(this.Const.userMsg.org_name);
          } else { return ['--']; }
        })(); */
      console.log('元数据', this.Const.userMsg);
    },
    methods: {
      tagClose (key, index) {
        this.search.tags.splice(index, 1);
        this.search.selectCon = '';
      },
      handleOpen (key, keyPath) {
        this.fnBreadcrumb(keyPath);
      },
      handleClose (key, keyPath) {
        this.fnBreadcrumb(keyPath);
      },
      fnEdit (id) {
        console.log('edit', id);
        const This = this;
        let promise = new Promise((resolve, reject) => {
          This.$ajax.post(This.Const.apiurl, {
            module: 'Service',
            controller: 'Organization_Con',
            action: 'getOrgInfo',
            id: id
          }).then((res) => {
            this.addOrgForm.pid = id;
            this.isEdit = true;
            this.dialogAddOroFormVisible = true;
            resolve(res);
          }).catch(error => {
            This.$message.error("请求机构详细信息报错！");
          });
        });
        promise.then((res) => {
          console.log("res", res);
          let resData = res.data;
          if (!resData.status) {
            this.addOrgForm.name = resData.data.name;
            this.addOrgForm.area = resData.data.area;
            this.addOrgForm.address = resData.data.address;
            this.addOrgForm.summary = resData.data.summary;
            this.addOrgForm.org_code = resData.data.org_code;
            this.addOrgForm.id = resData.data.id;
          } else {
            This.$message({type: 'error', message: resData.data || '获取机构详细信息失败！'});
          }
        });
      },
      fnDelete (id, name) {
        console.log('Delete', id);
        this.addOrgForm.pid = id;
        this.$confirm(`删除机构 - ${name}，其下所有机构都将被删除， 是否继续？`, '温馨提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(_ => {
          this.loading = true;
          this.$ajax.post(this.Const.apiurl, {
            module: 'Service',
            controller: 'Organization_Con',
            action: 'delOrganization',
            ids: (_ => { let arr = []; arr.push(id); return arr; })()
          }).then((res) => {
            this.loading = false;
            let resData = res.data;
            if (resData.status == 0) {
              this.$message({type: 'success', message: resData.data || '删除成功'});
              this.fnReqOrgByPrev();
            } else {
              this.$message({type: 'info', message: resData.data || '删除失败'});
            }
          }).catch((error) => {
            this.$message({type: 'error', message: '请求删除机构出错！'});
          });
        }).catch(_ => {
          this.$message({type: 'info', message: '已取消删除'});
        });
      },
      fnAdd (id) {
        this.addOrgForm.pid = id;
        this.dialogAddOroFormVisible = true;
      },
      // 根据keyPath数组，查找生成面包屑导航
      fnBreadcrumb (keyPath) {
        let This = this;
        let crumbArr = [];
        let list = this.conList;
        for (let i = 0; i < keyPath.length; i++) {
          let obj = this.fnPosition(keyPath[i], list);
          crumbArr.push( obj.obj.name );
          list = obj.arr;
        }
        this.userMsg = This.mytools.checkCookie();
        this.breadcrumbList = crumbArr;
      },
      // 按名称搜索机构
      fnSearchByName () {
        this.search.selectCon = this.search.selectCon.replace(/(^\s*)|(\s*$)/g, '');
        if (this.search.selectCon) {
          this.search.tags = new Array({
            label: '搜索',
            value: this.search.selectCon
          });
          this.$ajax.post(this.Const.apiurl, {
            // TODO
          }).then(res => {
            console.log('搜索机构名称', res);
          }).catch(error => {
            this.$message.error('请求搜索机构名称报错！');
          });
        }
      },
      fnPosition (val, list) {
        for (let i = 0, item; item = list[i]; i++) {
          if (item.id === val) {
            return {obj: item, arr: item.children};
          }
        }
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
            this.conList = (_ => {
              let arr = [];
              arr.push(resData.data);
              return arr;
            })();
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '请求机构数据失败！'});
          }
        }).catch((error) => {
          this.$message.error('请求机构数据报错！');
        });
      },
      // 添加机构 提交
      fnAddSubmit (formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            const loading = this.$loading({
              lock: true,
              text: 'Loading',
              spinner: 'el-icon-loading',
              background: 'rgba(0, 0, 0, 0.7)'
            });
            this.$ajax.post(this.Const.apiurl, Object.assign(this.addOrgForm, {
              module: 'Service',
              controller: 'Organization_Con',
              action: 'addOrganization'
            })).then((res) => {
              loading.close();
              let resData = res.data;
              if (!resData.status) {
                this.$message({type: 'success', message: '添加成功！'});
                this.dialogAddOroFormVisible = false;
                this.fnReqOrgByPrev();
              } else {
                this.$message({type: 'error', message: resData.data});
              }
            }).catch((error) => {
              this.$message.error("请求添加机构失败！");
            });
          } else { return false; }
        });
        
      },
      // 修改机构 提交
      fnEditSubmit (formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.$ajax.post(this.Const.apiurl, Object.assign(this.addOrgForm, {
              module: 'Service',
              controller: 'Organization_Con',
              action: 'upOrganization'
            })).then(res => {
              console.log('修改', res);
              let resData = res.data;
              if (!resData.status) {
                this.$message({type: 'success', message: resData.data || '修改成功！'});
              } else {
                this.$message({type: 'error', message: resData.data || '修改失败！'});
              }
              this.isEdit = false;
              this.dialogAddOroFormVisible = false;
            }).catch(error => {
              this.isEdit = false;
              this.dialogAddOroFormVisible = false;
              this.$message.error('提交修改出错了！');
            });
          } else { return false; }
        });
      }
    }
  }
</script>

<style lang="scss">
.breadcrumbs .el-breadcrumb{
  @include middelH(50px);
}
.operation-bar .el-input-group{
  width: 40%;
}
.wrap-list {
  margin-top: 10px;
  .title {
    @include middelH(50px);
    background: rgb(84, 92, 100);
    color: inherit;
    .el-breadcrumb span {
      color: #c0c4cc;
      &:hover {
        color: #32b16c;
      }
    }
    .breadcrumbs {
      padding-left: 5px;
    }
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
  }
}
</style>
