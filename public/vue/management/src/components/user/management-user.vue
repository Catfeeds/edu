<template>
  <div class="user-center">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">用户管理中心</a>
        </li>
      </ul>
    </div>
    <div class="user-table">
      <section class="show-table">
        <div class="show-track clearfix">
          <div class="user-list clearfix">
            <!-- 条件筛选 -->
            <div class="operation-bar" style="border-top: none;">
              <el-select @change="roleChange" v-model="search.role" placeholder="请选择用户角色">
                <el-option v-for="item in roleList" :key="item.value" :label="item.name" :value="item.value"></el-option>
              </el-select>
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
                <el-table-column fixed show-overflow-tooltip prop="idnumber" label="学号/工号" width="120">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.idnumber" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.idnumber }}</a>
                  </template>
                </el-table-column>
                <el-table-column prop="username" label="账号" width="120">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.username" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.username }}</a>
                  </template>
                </el-table-column>
                <el-table-column prop="realname" label="姓名" width="120">
                  <template slot-scope="scope">
                    <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.realname" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input>
                    <a v-else href="javascript:void(0);">{{ scope.row.realname }}</a>
                  </template>
                </el-table-column>
                <el-table-column :show-overflow-tooltip="true" prop="org" label="机构" width="150">
                  <template slot-scope="scope">
                    <el-cascader v-if="scope.row.id === '#'" size="mini" @change="fnSearch" placeholder="请选择机构" show-all-levels v-model="search.orgid" :props="search.props" :options="search.organizationList"></el-cascader>
                    <!-- <el-input v-if="scope.row.id === '#'" size="mini" v-model="search.keyFrom.orgid" placeholder="请输入内容">
                      <el-button slot="append" icon="el-icon-search" @click="fnSearch"></el-button>
                    </el-input> -->
                    <a v-else href="javascript:void(0);">{{ scope.row.org }}</a>
                  </template>
                </el-table-column>
                <el-table-column prop="email" label="Email" width="100" :show-overflow-tooltip="true"></el-table-column>
                <el-table-column prop="starttime" label="入学/入职时间" width="120" :show-overflow-tooltip="true"></el-table-column>
                <el-table-column prop="prevtime" label="最近访问" width="120" :show-overflow-tooltip="true"></el-table-column>
                <el-table-column fixed="right" label="操作" width="100">
                  <template slot-scope="scope">
                    <el-button @click="handleReadEditClick(scope.row, 'show')" type="text" size="small">查看</el-button>
                    <el-button @click="handleReadEditClick(scope.row, 'edit')" type="text" size="small">编辑</el-button>
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
      <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange"
        :current-page="pagination.currentPage"
        :page-sizes="pagination.pageSizes"
        :page-size="pagination.pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="pagination.total">
      </el-pagination>
    </div>
    <!--课程分页 end-->
    <el-dialog title="请选择添加方式" :visible.sync="addUserDialogVisible" width="20%" center>
      <span slot="footer" class="dialog-footer">
        <el-button @click="addUserByMore">批量添加</el-button>
        <el-button type="primary" @click="addUserByOne">单个添加</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      // 角色数据
      roleList: this.Const.role,
      // 搜索数据
      search: {
        role: '',
        tags: [
          /* { key: '12312', label: '标签一', value: 'label' } */
        ],
        keyFrom: {
          username: '',
          realname: '',
          idnumber: '',
          orgid: ''
        },
        // 机构下拉
        organizationList: [
          /*{
            id: 'B608AA535E0EC9C5DDBC8548AF39D4CB',
            name: '电子科技大学成都学院',
            children: []
          }*/
        ],
        props: {
          value: 'id',
          label: 'name',
          children: 'children'
        },
        orgid: []
      },
      // 用户数据
      datauser: [
        /* {
          id: '123123121',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云1',
          org: '机构',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        },{
          id: '1231232321',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云2',
          org: '机构',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        },{
          id: '123123321',
          username: 'teacher',
          idnumber: '1400000000',
          realname: '马云3',
          org: '机构',
          email: '邮箱',
          education: '学历',
          starttime: '2017/11/21',
          prevtime: '2017/10/21'
        } */
      ],
      // 搜索开启隐藏开关
      filterModel: false,
      // 添加成员的弹框
      addUserDialogVisible: false,
      selData: new Array(), // 记录选中的数据
      rolestudent: true, // 学生角色
      pagination: {
        total: 400,
        pageSize: 10,
        currentPage: 1,
        pageSizes: [10, 30, 50, 100]
      },
      // 选中的用户列表
      selUsers: []
    };
  },
  mounted: function() {
    this.fnReqAllUser();
  },
  activated: function() {
    
  },
  methods: {
    // 查看/编辑 成员信息
    handleReadEditClick (row, type) { this.$router.push({ path: `/library/user/${type}/${row.id}` }); },
    // 用户角色改变
    roleChange (val) {
      this.fnReqAllUser();
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
    // 删除用户列表
    fnDeleteUsers () {
      let This = this;
      let promise = new Promise((resolve, reject) => {
        This.$confirm('确认删除？').then(_ => {
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
          controller: 'User_Con',
          action: 'delUser',
          id: idArr
        }).then((res) => {
          let resData = res.data;
          if (!resData.status) {
            This.fnReqAllUser();
            This.$message({ type: 'success', message: '删除成功！' });
          } else {
            This.$message({type: 'error', message: resData.data || '删除失败！'});
          }
        }).catch((error) => {
          This.$message.error('请求报错');
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
        this.fnReqAllOrg();
      }
      this.filterModel = !this.filterModel;
    },
    // 搜索
    fnSearch () {
      this.search.keyFrom.orgid = this.search.orgid.length ? this.search.orgid[this.search.orgid.length-1] : null;
      this.fnReqAllUser();
      let label, This = this;
      let params = Object.assign(this.filterParams(this.search.keyFrom));
      let paramsKey = Object.keys(params);
      this.search.tags = [];
      paramsKey.forEach((val) => {
        // 将搜索字段名汉化，用于标签的显示
        switch(val){
          case 'username': label = '用户名'; break;
          case 'realname': label = '用户姓名'; break;
          case 'org': label = '用户机构'; break;
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
      this.$router.push({ path: '/library/user/add/more' });
      this.addUserDialogVisible = false;
    },
    // 单一录入成员信息
    addUserByOne () {
      this.$router.push({ path: '/library/user/add/one' });
      this.addUserDialogVisible = false;
    },
    // 获取用户数据
    fnReqAllUser () {
      let params = Object.assign(this.filterParams(this.search.keyFrom), {
        module: 'Service',
        controller: 'User_Con',
        action: 'getUserList',
        role: (_ => {
          if (this.search.role && this.search.role == '3' || this.search.role == '5') {
            return 'teacher';
          } else if (this.search.role && this.search.role == '8') {
            return 'student';
          } else { return ''; }
        })(),
        page: this.pagination.currentPage,
        rows: this.pagination.pageSize
      });
      this.$ajax.post(this.Const.apiurl, params).then((res) => {
        console.log('用户数据列表', res);
        let data = res.data;
        if (!data.status) {
          this.pagination.currentPage = data.data.current_page;
          this.pagination.total = data.data.total;
          this.datauser = data.data.data;
          this.filterModel = false;
        } else {
          this.$message({ message: data.data || '用户数据获取失败', type: 'error' });
        }
      }).catch((error) => {
        this.$message.error('请求报错');
      });
    },
    //  清除搜索对象中值为空的属性
    filterParams(obj){
      let form = obj, newPar = {};
      for (let key in form) {
        if (form[key] !== null && form[key] !== "") {
          newPar[key] = form[key].toString();
        }
      }
      return newPar;
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
          this.search.organizationList = (_ => {
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
    }
  },
  watch: {
    
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
}
</style>