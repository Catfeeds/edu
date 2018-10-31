<template>
  <div class="user-center">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">{{ title }}</a>
        </li>
      </ul>
      <div class="fr"><el-button @click="$router.back(-1)" type="primary">返回</el-button></div>
    </div>
    <div class="user-table">
      <section class="show-table">
        <div class="show-track clearfix">
          <div class="user-list clearfix">
            <!-- 条件筛选 -->
            <div class="operation-bar" style="border-top: none;">
              <el-button-group>
                <!-- <el-button size="small" type="primary" icon="el-icon-edit"></el-button> -->
                <el-button @click="addUserDialogVisible = true" size="small" type="primary" icon="el-icon-plus"></el-button>
                <el-button v-if="selUsers.length" @click="fnDeleteUsers" size="small" type="primary" icon="el-icon-delete"></el-button>
                <el-button @click="filterSearch" size="small" type="primary" icon="el-icon-search">搜索</el-button>
              </el-button-group>
              <el-tag :key="index" v-for="(item,index) in search.tags" size="mini" :closable="true" @close="tagClose(item.key, index)">{{item.label}} : {{item.value}}</el-tag>
            </div>
            <!-- 成员列表 -->
            <div class="table-list search-students">
              <el-table height="560" size="small" :data="datauser" border style="width: 100%" @selection-change="handleSelectionChange">
                <el-table-column v-if="!filterModel" type="selection" width="55"></el-table-column>
                <el-table-column fixed show-overflow-tooltip prop="idnumber" label="学号/工号" width="150">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.idnumber" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.idnumber }}</a>
                  </template>
                </el-table-column>
                <el-table-column prop="username" label="账号" width="150">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.username" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.username }}</a>
                  </template>
                </el-table-column>
                <el-table-column prop="realname" label="姓名" width="150">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.realname" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.realname }}</a>
                  </template>
                </el-table-column>
                <el-table-column show-overflow-tooltip prop="email" label="Email" width="100"></el-table-column>
                <el-table-column show-overflow-tooltip prop="starttime" label="入学/入职时间" width="150"></el-table-column>
                <el-table-column  prop="prevtime" label="最近访问" width="120"></el-table-column>
                <el-table-column fixed="right" label="操作" width="100">
                  <template slot-scope="scope">
                    <el-button @click="handleReadEditClick(scope.row, 'show')" type="text" size="small">查看</el-button>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
          <!-- 表单页面 -->
        </div>
      </section>
    </div>
    <!--课程分页 start-->
    <div class="paging course-paging clearfix">
      <el-pagination
        background
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page="pagination.currentPage"
        :page-sizes="pagination.pageSizes"
        :page-size="pagination.pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pagination.total">
      </el-pagination>
    </div>
    <!--课程分页 end-->
    <el-dialog
      title="请选择添加方式"
      :visible.sync="addUserDialogVisible"
      width="20%"
      center>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addUserByMore">批量添加</el-button>
        <el-button type="primary" @click="addUserByOne">单个添加</el-button>
      </span>
    </el-dialog>

    <!-- 添加成员 -->
    <el-dialog :close-on-click-modal="false" title="添加成员" :visible.sync="addUserByTable.addDialogTableVisible" width="1000px">
      <el-transfer
        v-loading="addUserByTable.loading"
        v-model="addUserByTable.addUserList"
        filterable
        :render-content="renderFunc"
        :titles="['所有可添加成员', '欲新添加成员']"
        :button-texts="['移除', '添加']"
        :format="{
          noChecked: '${total}',
          hasChecked: '${checked}/${total}'
        }"
        @change="handleChange"
        :data="addUserByTable.addGridData"
        :props="addUserByTable.props">
      </el-transfer>
      <div slot="footer" class="dialog-footer">
        <el-pagination @current-change="addUserCurrentChange" background layout="prev, pager, next" :current-page="currentPage" :total="addUserByTable.pages.total">
        </el-pagination>
        <el-button @click="addUserByTable.addDialogTableVisible = false">取 消</el-button>
        <el-button type="primary" @click="addUserByTableSubmit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      title: '专业成员管理',
      // 搜索数据
      search: {
        tags: [
          /* { key: '12312', label: '标签一', value: 'label' } */
        ],
        keyFrom: {
          username: null,
          realname: null,
          idnumber: null
        }
      },
      // 用户数据
      datauser: [
        {
          id: '123123121',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云1',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        },{
          id: '1231232321',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云2',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        },{
          id: '123123321',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云3',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        }
      ],
      // 搜索开启隐藏开关
      filterModel: false,
      // 添加成员的弹框
      addUserDialogVisible: false,
      selData: new Array(), // 记录选中的数据
      pagination: {
        total: 400,
        pageSize: 10,
        currentPage: 1,
        pageSizes: [10, 30, 50, 100]
      },
      // 选中的用户列表
      selUsers: [],
      // 弹窗选择添加用户
      addUserByTable: {
        addDialogTableVisible: false,
        addGridData: [
          /* {
            id: '1231231231231',
            idnumber: '1400005300',
            realname: '马云2',
          },{
            id: '12312312311312231',
            idnumber: '14000014340000',
            realname: '马云2',
          },{
            id: '123123123435451231',
            idnumber: '1400100',
            realname: 'yk2',
          } */
        ],
        props: {
          key: 'idnumber',
          label: 'realname'
        },
        addUserList: [],
        pages: {
          total: 100,
          currentPage: 1
        },
        loading: false
      }
      
    };
  },
  mounted: function() {
    this.fnReqAllUser();
  },
  activated: function() {
    
  },
  methods: {
    // 查看 成员信息
    handleReadEditClick (row, type) {
      let This = this;
      this.$router.push({
        path: `/library/user/${type}/${row.id}`,
        query: {l: This.$route.params.c}
      });
    },
    // 每页条数改变
    handleSizeChange (size) {
      this.pagination.pageSize = size;
      this.fnReqAllUser();
    },
    // 页码改变
    handleCurrentChange (currentPage) {
      this.pagination.currentPage = currentPage;
      this.fnReqAllUser();
    },
    // 选择项改变
    handleSelectionChange (section) {
      this.selUsers = section;
    },
    // 移除用户列表
    fnDeleteUsers () {
      let This = this;
      let promise = new Promise((resolve, reject) => {
        This.$confirm('移除删除？').then(_ => {
          let idArr = [];
          for (let i = 0, item; item = This.selUsers[i]; i++) {
            idArr.push(item.id);
          }
          resolve(idArr);
        }).catch(_ => {reject();});
      });
      promise.then((idArr) => {
        This.$ajax.post(This.Const.apiurl, {
          module: 'Service',
          controller: 'Profession_Con',
          action: 'delProUser',
          id: this.$route.query.d,
          user_id: idArr
        }).then((res) => {
          let resData = res.data;
          console.log('移除', res);
          if (!resData.status) {
            This.fnReqAllUser();
            This.$message({ type: 'success', message: resData.data || '移除成功！' });
          } else {
            This.$message({type: 'error', message: resData.data || '移除失败！'});
          }
        }).catch((error) => {
          This.$message.error('请求移除报错');
        });
      }, () => {
        This.$message({type: 'info', message: '已取消删除'});
      });
    },
    // 点击开启/影藏搜索
    filterSearch () {
      if (this.filterModel) {
        this.datauser.splice(0, 1);
      } else {
        this.datauser.unshift({ "id": "#" });
      }
      this.filterModel = !this.filterModel;
    },
    // 搜索
    fnSearch () {
      let label, This = this;
      let params = Object.assign(this.mytools.filterParams(this.search.keyFrom));
      let paramsKey = Object.keys(params);
      this.search.tags = [];
      paramsKey.forEach((val) => {
        // 将搜索字段名汉化，用于标签的显示
        switch(val){
          case 'username': label = '用户名'; break;
          case 'realname': label = '用户姓名'; break;
          case 'idnumber': label = '用户编号'; break;
        }
        This.search.tags.push({
          key: val,
          label: label,
          value: params[val]
        });
      });
    },
    // 删除搜索标签
    tagClose (key, index) {
      this.search.tags.splice(index, 1);
      this.search.keyFrom[key] = null;
      this.fnReqAllUser();
    },
    // 批量添加成员
    addUserByMore () {
      this.$router.push({
        path: '/library/user/add/more'
      });
      this.addUserDialogVisible = false;
    },
    // 单一录入成员信息
    addUserByOne () {
      this.addUserDialogVisible = false;
      this.addUserByTable.addDialogTableVisible = true;
      this.fnReqUserInAdd();
    },
    // 获取已存在的用户数据
    fnReqAllUser () {
      let currentParams = this.$route.params;
      if ((currentParams && currentParams.c === 'pro') || (currentParams && currentParams.c === 'cla')) {
        let params = Object.assign(this.mytools.filterParams(this.search.keyFrom), {
          module: 'Service',
          controller: (currentParams.c === 'pro') ? 'Profession_Con' : 'Class_Con',
          action: (currentParams.c === 'pro') ? 'getProUser' : 'queClassmate',
          id: this.$route.query.d,// 班级id
          page: this.pagination.currentPage,
          rows: this.pagination.pageSize
        });
        this.$ajax.post(this.Const.apiurl, params).then((res) => {
          console.log('用户数据列表', res);
          let data = res.data;
          if (!data.status) {
            this.pagination.currentPage = data.data.page.current_page;
            this.pagination.total = data.data.page.total;
            this.datauser = data.data.data;
            this.filterModel = false;
          } else {
            this.$message({ message: data.data || '用户数据获取失败', type: 'error' });
          }
        }).catch((error) => {
          this.$message.error('请求报错');
        });
      }
    },
    // 获取可添加的用户
    fnReqUserInAdd () {
      this.addUserByTable.loading = true;
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'Profession_Con',
        action: 'getProOtherUser',
        id: this.$route.query.d,
        page: this.addUserByTable.pages.currentPage,
        rows: 10
      }).then((res) => {
        this.addUserByTable.loading = false;
        console.log('获取可添加的用户', res);
        let resData = res.data;
        if (!resData.status) {
          this.addUserByTable.addGridData = resData.data.data;
          this.addUserByTable.pages.total = resData.data.page.total;
          this.addUserByTable.pages.currentPage = resData.data.page.current_page;
        } else {
          this.$message({type: 'error', message: '获取用户列表失败！'});
        }
      }).catch(error => {
        this.addUserByTable.loading = false;
        this.$message.error('请求可添加用户的数据出错！');
      });
    },
    // 穿梭狂添加用户
    handleChange(value, direction, movedKeys) {
      console.log('穿梭狂添加用户', value, direction, movedKeys);
    },
    // 穿梭框翻页
    addUserCurrentChange () {
      this.fnReqUserInAdd();
    },
    renderFunc(h, option) {
      console.log(h, option);
      return <span>{ option.idnumber } - { option.realname }</span>;
    },
    // 提交穿梭框添加的用户
    addUserByTableSubmit () {
      let This = this;
      if (this.addUserByTable.addUserList.length) {
        const loading = this.$loading({
          lock: true,
          text: 'Loading',
          spinner: 'el-icon-loading',
          background: 'rgba(0, 0, 0, 0.7)'
        });
        this.$ajax.post(this.Const.apiurl, {
          module: 'Service',
          controller: 'Profession_Con',
          action: 'addBatchProUser',
          id: this.$route.query.d,
          user: (_ => {
            let arr = [];
            for (let i = 0, item; item = This.addUserByTable.addGridData[i]; i++) {
              for (let j = 0, jtem; jtem = This.addUserByTable.addUserList[j]; j++) {
                if (jtem === item.idnumber) {
                  arr.push(item.id);
                }
              }
            }
            return arr;
          })()
        }).then(res => {
          loading.close();
          let resData = res.data;
          if (!resData.status) {
            this.fnReqAllUser();
            this.$message({type: 'success', message: resData.data || '添加成功！'});
          } else {
            this.$message({type: 'error', message: resData.data || '添加成员失败！'});
          }
          console.log('添加成员', res);
          this.addUserByTable.addDialogTableVisible = false;
        }).catch(error => {
          loading.close();
          this.$message.error('请求添加成员失败！');
          this.addUserByTable.addDialogTableVisible = false;
        });
        
      }
    }
  },
  watch: {
    $route: {
      handler (val, oldVal) {
        this.fnReqAllUser();
        let currentParams = this.$route.params;
        if (currentParams && currentParams.c === 'pro') {
          // 专业
          this.title = '专业成员管理';
        } else if (currentParams && currentParams.c === 'org') {
          // 机构
          this.title = '机构成员管理';
        } else if (currentParams && currentParams.c === 'cla') {
          // 机构
          this.title = '班级成员管理';
        }
      },
      deep: true
    }
  }
};
</script>

<style lang="scss">
.operation-bar{
  padding: 10px 0;
  width: 100%;
  // border: 1px solid red;
}
.user-center .el-dialog__body{
  padding: 0;
  width: 950px;
  margin: 0 auto;
}
.el-transfer {
  .el-transfer-panel {
    width: 400px;
  }
  .el-transfer-panel__body, .el-transfer-panel__list.is-filterable{
    height: 500px;
  }
  
}
.el-dialog__wrapper {
  .el-pagination {
    float: left;
  }
}

</style>