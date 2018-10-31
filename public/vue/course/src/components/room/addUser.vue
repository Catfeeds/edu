<template>
  <section id="add-user-room">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0;"></i>
          <a class="click" href="javascript:void(0);">添加课堂成员</a>
        </li>
      </ul>
      <div class="back fr">
        <el-button type="primary" plain round @click="$router.push('/')">返 回</el-button>
      </div>
    </div>
    <div class="bar">
      <el-input placeholder="输入内容搜索" v-model="search.con" class="input-with-select">
        <el-select @change="roleChange" slot="prepend" v-model="search.role" placeholder="请选择课堂角色">
          <el-option v-for="item in classRoomRole" :key="item.value" :label="item.name" :value="item.value"></el-option>
        </el-select>
        <el-button slot="append" @click="getCRoomUserByAdd" icon="el-icon-search"></el-button>
      </el-input>
      <el-tag type="success">已选择：{{multipleSelection.length}} 人</el-tag>
      <el-button type="primary" plain round @click="submitAddUsers">添 加</el-button>
    </div>
    <el-table ref="multipleTable" :data="allUserList" tooltip-effect="dark" style="width: 100%"
      @selection-change="handleSelectionChange">
      <el-table-column type="selection" width="55"></el-table-column>
      <el-table-column label="学号/编号" width="120">
        <template slot-scope="scope">{{ scope.row.idnumber }}</template>
      </el-table-column>
      <el-table-column prop="username" label="姓名" width="120"></el-table-column>
      <el-table-column prop="address" label="专业" show-overflow-tooltip>
        <template slot-scope="scope">
          <el-tag type="success" disable-transitions>{{scope.row.realname}}</el-tag>
        </template>
      </el-table-column>
    </el-table>
    <!-- 分页 -->
    <el-pagination
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      :current-page="page.currentPage"
      :page-sizes="[20, 30, 50, 100]"
      :page-size="page.pageSize"
      layout="total, sizes, prev, pager, next, jumper"
      :total="page.total">
    </el-pagination>
  </section>
</template>
<script>
export default {
  data () {
    return {
      allUserList: [
        /* {
          id: 'nsaudhaetysdu',
          idnumber: '1231fd3',
          name: '王小虎',
          pro: [
            {
              name: '专业1',
              value: 'pro1vc111'
            },{
              name: '专业2',
              value: 'pro22vve22'
            }
          ]
        } */
      ],
      multipleSelection: [],
      // 课堂角色
      classRoomRole: [
        {
          value: '5',
          name: '老师'
        },{
          value: '8',
          name: '学生'
        }
      ],
      search: {
        role: '5',
        con: ''
      },
      page: {
        currentPage: 1,
        total: 0,
        pageSize: 10
      }
    }
  },
  mounted () {
    this.getCRoomUserByAdd();
  },
  methods: {
    handleSelectionChange(val) {
      console.log(val);
      this.multipleSelection = val;
    },
    handleSizeChange(val) {
      this.page.pageSize = val;
      this.getCRoomUserByAdd();
    },
    handleCurrentChange(val) {
      this.page.currentPage = val;
      this.getCRoomUserByAdd();
    },
    roleChange () {
      this.getCRoomUserByAdd();
    },
    // 获取课堂可添加的用户
    getCRoomUserByAdd () {
      const loading = this.$loading({
        lock: true,
        text: '获取可添加用户...',
        spinner: 'el-icon-loading',
        background: 'rgba(0,0,0,.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'User_Con',
        action: 'getUserList',
        role: (this.search.role == 3 || this.search.role == 5) ? 'teacher' : 'student',
        page: this.page.currentPage,
        rows: this.page.pageSize
      }).then(response => {
        loading.close();
        let resData = response.data;
        if (resData.status == 0) {
          this.page.total = resData.data.total;
          this.page.currentPage = resData.data.current_page;
          this.allUserList = resData.data.data;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取用户列表失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: error.message});
      });
    },
    // 提交添加的用户
    submitAddUsers () {
      if (this.multipleSelection.length) {
        const loading = this.$loading({
          lock: true,
          text: '获取可添加用户...',
          spinner: 'el-icon-loading',
          background: 'rgba(0,0,0,.7)'
        });
        this.$ajax.post(this.Const.apiurl, {
          module: 'service',
          controller: 'Classroom_User_Con',
          action: 'addUser',
          type: '2',
          classroom: this.$route.query.rd,
          id: (_ => {
            let arr = [];
            for (let i = 0, item; item = this.multipleSelection[i]; i++) {
              arr.push(item.id);
            }
            return arr;
          })(),
          role: (this.search.role == 5) ? 2 : 1
        }).then(response => {
          loading.close();
          console.log('添加用户', response);
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: '添加成功！'});
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '添加失败！'});
          }
        }).catch(error => {
          loading.close();
          this.$message({type: 'error', message: error.message});
        });
      } else {
        this.$message({type: 'info', message: '请先选择需要添加的成员！'});
      }
    }
  }
}
</script>
<style lang="scss">
#add-user-room {
  .bar {
    margin: 20px 10px;
  }
  .el-input-group {width: 40%;}
  .el-input-group__prepend {width: 50px;}
  .el-input-group__append {width: 1px;}
}
</style>
