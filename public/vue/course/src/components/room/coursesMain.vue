<template>
  <div class="wrap-course content">
    <div class="class-wrap">
      <!--课程顶部信息 start-->
      <div class="classify clearfix">
        <ul class="fl">
          <li>
            <i class="before" style="left: 0;"></i>
            <a class="click" href="javascript:void(0);">我教的课程</a>
          </li>
          <li>
            <a href="javascript:void(0);">我学的课程</a>
          </li>
        </ul>
        <!--学期选择-->
        <form class="layui-form layui-form-pane term fr" action="">
          <div class="layui-form-item">
            <el-select @change="getCourseByTerm" v-model="semester" placeholder="学期">
              <el-option-group v-for="group in semesterList" :key="group.label" :label="group.label">
                <el-option v-for="item in group.options" :key="item.value" :label="item.label" :value="item.value"></el-option>
              </el-option-group>
            </el-select>
          </div>
        </form>
      </div>
      <!--课程顶部信息 end-->
      <!--课程列表 start-->
      <div class="content-list clearfix">
        <p style="display:block;padding: 10px 0;text-align:center;" v-if='courseList.length == 0'>目前还没有您的课堂数据！</p>
        <div v-else class="item" v-for="(item, index) in courseList" :key="index">
          <router-link to="/course/Details" class="class-pic"><img :src="(item.img) ? (item.img) : defaultImg" title="" alt="" /></router-link>
          <div class="class-msg">
            <router-link to="/course/Details"><h2 class="class-title">{{item.fullname}}</h2></router-link>
            <p class="deal-msg">
              <span class="dealing" title="课堂类型"><i class="el-icon-caret-right"></i>{{ item.category }}</span>
              <span class="dealing" title="人数"><i class="el-icon-star-on"></i>{{ (item.people) ? (item.people) : 0 }}</span>
              <span class="dealed" title="老师"><i class="el-icon-news"></i>{{ (item.teacher) ? (item.teacher) : '暂无老师' }}</span>
              <a href="javascript:void(0);" @click="goToPaperBankModule(item.id)"><span class="dealing" title="试卷库">试卷库</span></a>
            </p>
            <p class="deal-msg">
              <span class="dealing" title="删除" @click="handleDelete(item.id)"><i class="el-icon-delete"></i></span>
              <span class="dealing" title="修改" @click="handleEdit(item.id)"><i class="el-icon-edit"></i></span>
            </p>
          </div>
        </div>
        <div class="item add-course">
          <router-link to="/course/addcourses" class="course-pic" title="添加课堂"><i class="el-icon-plus"></i></router-link>
        </div>
      </div>
      <!--课程列表 end-->
      <!--分页 start-->
      <el-pagination @current-change="handleCurrentChange"
        :current-page.sync="page.currentPage"
        :page-size="6" layout="total, prev, pager, next" :total="page.total">
      </el-pagination>
      <!--分页 end-->
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      page: {
        total: 0,
        currentPage: 1
      },
      // 学期
      semester: '',
      semesterList: this.Const.semesterList,
      courseList: [
        /* {
          id: '12312312',
          fullname: 'C#语言',
          img: 'https://ss0.bdstatic.com/l4oZeXSm1A5BphGlnYG/skin/459.jpg?2',
          people: 100,
          teacher: 'Mis Wu',
          category: ''
        } */
      ],
      // 课堂默认封面图片
      defaultImg: 'https://ss1.bdstatic.com/70cFuXSh_Q1YnxGkpoWK1HF6hhy/it/u=743539907,2975959032&fm=26&gp=0.jpg'
    };
  },
  mounted: function() {
    this.getRequestList();
  },
  methods: {
    // 页码改变
    handleCurrentChange (curVal) {
      this.page.currentPage = curVal;
      this.getRequestList();
      console.log('页码改变', curVal);
    },
    getCourseByTerm: function(value) {
      console.log('学期选择', value);
    },
    // 删除课堂
    handleDelete (id) {
      this.$confirm('此操作将永久删除该课堂, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$message({
          type: 'info',
          message: '正在完善中!'+id
        });
      }).catch(() => {
        this.$message({ type: 'info', message: '已取消删除' });          
      });
    },
    handleEdit (id) {
      this.$message({
        type: 'info',
        message: '正在完善中!'+id
      });
    },
    // 请求课堂列表数据
    getRequestList () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Classroom_Con',
        action: 'userClassroom',
        id: '',
        page: this.page.currentPage,
        list_rows: 6
      }).then(response => {
        console.log('课堂列表', response);
        let resData = response.data;
        if (resData.status == 0) {
          this.page.currentPage = resData.data.current_page;
          this.page.total = resData.data.total;
          let promiseList = [];
          for (let i = 0, item; item = resData.data.data[i]; i++) {
            promiseList.push(this.getPicture(item['classroompic']));
          }
          Promise.all(promiseList).then(data => {
            console.log('课堂封面图片', data);
            this.courseList = resData.data.data;
          }).catch(error => {
            this.$message({type: 'info', message: resData.data ? resData.data : '获取课堂列表失败！'});
          });
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取课堂列表失败！'});
        }
      }).catch(err => {
        this.$message({type: 'error', message: resData.data ? resData.data : err.message});
      });
    },
    // 跳转试卷库
    goToPaperBankModule (bankid) {
      window.sessionStorage.setItem('currentCourseInLib', bankid);
      let jumpUrl = this.Const.projectRoot.replace('index.php/', 'public/edumodel/paperbank/?d='+bankid);
      window.open(jumpUrl);
    },
    // 根据图片id获取图片完整路径
    getPicture (id) {
      return new Promise((resolve, reject) => {
        this.$ajax.post(this.Const.apiurl, {
          module: 'service',
          controller: 'Attachments_Con',
          action: 'getFile',
          id: id,
          type: 'getpic'
        }).then(response => {
          resolve(response.data);
        }).catch(error => {
          reject(error);
        });
      });
    }
  }
};
</script>
<style lang="scss">
.content-list {
  position: relative;
  padding: 4px 0;
  min-height: 200px;
  .item {
    height: 265px;
    width: 31.7%;
    overflow: hidden;
    float: left;
    margin: 26px 21px 0 0;
    box-sizing: border-box;
    &:nth-child(3n+3){
      margin: 26px 0 0 0;
    }
    a.class-pic {
      height: 140px;
      width: 100%;
      border-radius: 5px;
      overflow: hidden;
      display: block;
      img {
        height: 140px;
        width: 100%;
      }
    }
    .class-msg {
      width: 100%;
      .class-title {
        font-size: 16px;
        color: #07111B;
        line-height: 24px;
        word-wrap: break-word;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        max-height: 46px;
        transition: all .3s;
        margin: 5px 0;
        &:hover {color: rgb(216, 16, 16);}
      }
      .deal-msg {
        margin: 5px 0;
        span {
          margin-right: 20px;
          i{margin-right: 5px;}
        }
      }
    }
    &.add-course {
      position: absolute;
      bottom: 50px;
      right: 50px;
      width: 100px;
      height: 100px;
      background: gray;
      border-radius: 50%;
      overflow: hidden;
      a {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        display: block;
        line-height: 100px;
        text-align: center;
        font-size: 30px;
        color: #07111B;
      }
    }
  }
}
</style>
