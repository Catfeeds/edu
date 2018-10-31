<template>
  <section id="courselib-index">
    <div class="classify clearfix">
      <ul class="fl">
        <li>
          <i class="before" style="left: 0px;"></i>
          <a href="javascript:void(0);" class="click">课程库</a>
        </li>
      </ul>
    </div>
    <div class="courselin-list">
      <div class="operation-bar" style="border-top: none;">
        <div class="my-cascader">
          <el-cascader change-on-select @change="fnSelOrgChange" v-model="selOrg" :props="props" :options="organizationList"></el-cascader>
        </div>
        <el-input placeholder="请输入课程名称查找" v-model="search.selectCon" class="input-with-select">
          <el-button slot="append" @click="fnSearchByName" icon="el-icon-search">搜索</el-button>
        </el-input>
        <el-tag :key="index" v-for="(item,index) in search.tags" size="small" :closable="true" @close="tagClose(item.key, index)">{{item.label}} : {{item.value}}</el-tag>
      </div>
      <div class="list-wrap clearfix">
        <p style="display:block;padding:10px 0;text-align:center;" v-if='courseList.length == 0'>目前还没有您的课堂数据！</p>
        <div v-else class="item" v-for="(item, index) in courseList" :key="index">
          <router-link :to="'/course/chapters?d='+item.id" class="class-pic"><img :src="item.pic_path" :title="item.shortname" alt="" /></router-link>
          <div class="class-msg">
            <router-link :to="'/course/chapters?d='+item.id"><h2 class="class-title">{{item.fullname}}</h2></router-link>
            <p class="deal-msg">
              <span class="dealing el-icon-star-on" title="课程编号">{{ item.idnumber }}</span>
              <span class="dealed el-icon-news" title="课程类型">{{ item.category }}</span>
              <span class="dealed el-icon-time" title="课程课时">{{ item.numsections }}</span>
            </p>
            <div class="oper">
              <router-link :to="'/course/courseware?cd='+item.id"><span class="dealing" title="课程编号">课件管理</span></router-link>
              <router-link to="#" target="_blank"><span class="dealing" title="课程编号">作业管理</span></router-link>
              <a href="javascript:void(0);" @click="goToQuesBankModule(item.id)"><span class="dealing" title="题库">题库</span></a>
            </div>
          </div>
        </div>
        <div class="item add-course">
          <router-link to="/course/addcourses" class="course-pic" title="添加课堂"><i class="el-icon-plus"></i></router-link>
        </div>
      </div>
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
  </section>
</template>
<script>
export default {
  data () {
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
      selOrg: [],
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
      courseList: [
        /*{
          id: '12312312',
          category: 'FB1CCC54072802F14BCF94C2A1DFE7A4',
          department: '340184FA3EBD3FB2C30CF17453984F58',
          fullname: 'JavaScript高级程序设计',
          name: 'C#语言',
          pic_path: 'https://ss0.bdstatic.com/l4oZeXSm1A5BphGlnYG/skin/459.jpg?2',
          idnumber: 'js10000',
          numsections: '64',
          summary: 'JavaScript高级程序设计课程概要',
          shortname: 'JavaScript高级程序设计课程简介'
        }*/
      ],
      pagination: {
        total: 0,
        pageSize: 10,
        currentPage: 1,
        pageSizes: [10, 15, 30, 50]
      },
      // 课程类型
      categories: []
    }
  },
  mounted () {
    this.requestAllCourse();
  },
  methods: {
    tagClose (key, index) {
      this.search.tags.splice(index, 1);
      this.search.selectCon = '';
    },
    handleSizeChange (val) {
      this.pagination.pageSize = val;
      this.requestAllCourse();
    },
    handleCurrentChange (val) {
      this.pagination.currentPage = val;
      this.requestAllCourse();
    },
    fnSelOrgChange () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'Course_Con',
        action: 'queCourseOrg',
        organization: this.selOrg[this.selOrg.length-1],
        page: this.pagination.currentPage,
        rows: this.pagination.pageSize
      }).then(response => {
        loading.close();
        let resData = response.data;
        if (resData.status == 0) {
          this.courseList = resData.data.data.data;
          this.pagination.total = resData.data.data.page.total;
          this.pagination.currentPage = resData.data.data.page.current_page;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取课程失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: '请求出问题了！'});
      });
    },
    // 搜索课程
    fnSearchByName () { 
      // TODO
    },
    // 获取课程
    requestAllCourse () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiurl, {
        module: 'Service',
        controller: 'Data_Request',
        action: 'courseRequest',
        page: this.pagination.currentPage,
        rows: this.pagination.pageSize
      }).then(response => {
        loading.close();
        let resData = response.data;
        if (resData.status == 0) {
          this.courseList = resData.data.course;
          this.categories = resData.data.categories;
          this.organizationList = (_ => {
            let arr = [];
            arr.push(resData.data.organization);
            return arr;
          })();
          this.pagination.total = resData.data.page.total;
          this.pagination.currentPage = resData.data.page.current_page;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取课程失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: '请求出问题了，检查本地网络！'});
      });
    },
    // 跳转到题库模块
    goToQuesBankModule (bankid) {
      // public/edumodel/questionbank/?d=C9036CA7A41C95D9B919BC990A4E67B4
      window.sessionStorage.setItem('currentCourseInLib', bankid);
      let jumpUrl = this.Const.projectRoot.replace('index.php/', 'public/edumodel/questionbank/?d='+bankid);
      // console.log('jumpUrl', jumpUrl);
      window.open(jumpUrl);
    }
  }
}
</script>
<style lang="scss">
.operation-bar {
  margin-top: 10px;
  .el-input-group{
    width: 40%;
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
.list-wrap {
  position: relative;
  padding: 4px 0;
  .item {
    height: 280px;
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
