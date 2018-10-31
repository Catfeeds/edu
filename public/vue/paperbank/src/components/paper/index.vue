<template>
  <div class="bankindex questbank">
    <aside class="aside">
      <p class="title">
        <i class="el-icon-d-arrow-right"></i> 考试场次</p>
      <div class="total-bank">
        <span class="key">课堂名称:</span>
         <p>
          <i>{{ classroom }}</i></p>
        </div>
        <div class="total-bank">
        <span>场次总数</span>
        <p>
          <i>{{ page.total }}</i>场</p>
      </div>
      <div class="addbank" @click="fnAddPaper">
        <i class="el-icon-circle-plus"></i>
        <span>新建考试场次</span>
      </div>
      <!-- <div class="addbank" @click="fnCheckPaper">
        <i class="el-icon-circle-plus"></i>
        <span>审核考试场次</span>
      </div> -->
    </aside>
    <section class="wrap">
      <div class="head-condition clearfix">
        <p class="item-condition">
          <el-input placeholder="输入场次名称关键字" v-model="search.name"></el-input>
        </p>
        <p class="item-condition">
          <el-input v-model="search.limittime" min="1" type="number" placeholder="请输入考试时长(分)"></el-input>
        </p>
        <p class="item-condition">
          <el-cascader :options="sectionsList" @change="sectionsChange" :props="sectionsProp"  change-on-select v-model="sectionsOptions" placeholder="请选择章节"></el-cascader></p>
        <p class="item-condition">
          <el-date-picker v-model="time" value-format="yyyy-MM-dd H:mm:ss" type="datetimerange" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
        </p>
        <p class="item-condition">
          <el-button type="primary" @click="requestFn">搜索</el-button>
        </p>
      </div>
      <div class="bank-list">
        <el-table :data="paperList" max-height="400" border style="width: 100%">
        <el-table-column prop="name" fixed label="考试场次" :show-overflow-tooltip="true" width="200"></el-table-column>
        <el-table-column prop="quizname" label="试卷名称" :show-overflow-tooltip="true"></el-table-column>
        <el-table-column prop="starttime" label="开始时间" width="200">
          <template slot-scope="scope">
            <el-popover trigger="hover" placement="top">
              <p>结束时间: {{ scope.row.endtime }}</p>
              <div slot="reference" class="name-wrapper">
                <i class="el-icon-time"></i>
                <span style="margin-left: 10px">{{ scope.row.starttime }}</span>
              </div>
            </el-popover>
          </template>
        </el-table-column>
        <el-table-column prop="status" label="考试状态" width="120">
          <template slot-scope="scope">
            <el-tag type="info" v-if="scope.row.check == 1" >考试未开始</el-tag>
            <el-tag type="success" v-if="scope.row.check == 2" >考试中</el-tag>
            <el-tag type="success" v-if="scope.row.check == 3" >考试结束</el-tag>
            <el-tag type="danger" v-if="scope.row.check == 4" >待审核</el-tag>
            <el-tag type="danger" v-if="scope.row.check == 5" >审核未通过</el-tag>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" width="150">
          <template slot-scope="scope">
            <el-button type="text" @click="fnDelete(scope.row.id)" size="small" >删除</el-button>
            <el-button type="text" size="small" @click="fnEdit(scope.row.id)">编辑</el-button>
            <el-button type="text" size="small" @click="fnReadPaper(scope.row.id)">查看</el-button>
            <el-button v-if="scope.row.check == 4" type="text" size="small" @click="fnCheckPaper(scope.row.id)">审核</el-button>
          </template>
        </el-table-column>
      </el-table>
      </div>
      <div v-show="sideshowhide" class="flow-side-box">
        <div class="go-top" title="回到顶部" @click="fnGoTop">
          <i class="el-icon-arrow-up"></i>
        </div>
        <div class="add-bank-btn" @click="fnAddPaper">
          <i class="el-icon-circle-plus"></i>
          <span>新建考试场次</span>
        </div>
      </div>
      <div class="paging">
        <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="page.currentPage" :page-sizes="[5,10,20,50]" :page-size="page.pageSize" layout="total,sizes,prev,pager,next,jumper" :total="page.total"></el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    let thisVue = this;
    let param = [];
    return {
      courseid: thisVue.$route.query.id,
      classroom: '11111',
      // 分页数据
      page: {
        currentPage: 1,
        pageSize: 5,
        total: 0
      },
      //开始和结束时间
      time: [],
      search: {
        limittime: '',
        name: '',
        // 考试状态
        status: '0',
      },
      sectionsList: [],
      sectionsProp: {
        value: 'id',
        label: 'name',
        children: 'children'
      },
      //章节
      sectionsOptions: [],
      sideshowhide: false,
      paperList: []
    };
  },
  mounted() {
    this.showhideSideBar();
    this.requestFn();
    this.loadSections();
  },
  methods: {
    //章节
    sectionsChange (value) {
      // console.log(value);
    },
    // 侧边楼梯导航显示隐藏
    showhideSideBar () {
      let thisVue = this;
      $(window).scroll(function() {
        let scrollTop = $(document).scrollTop();
        if (scrollTop > 400) {
          thisVue.sideshowhide = true;
        } else {
          thisVue.sideshowhide = false;
        }
      });
    },
    // 回到顶部
    fnGoTop () { $("html, body").animate({ scrollTop: "0" }); },
    // 每页显示条数改变
    handleSizeChange (val) {
      this.page.pageSize = val;
      this.requestFn();
    },
    // 当前页码改变
    handleCurrentChange (val) {
      this.page.currentPage = val;
      this.requestFn();
    },
    // 添加场次
    fnAddPaper () { this.$router.push({ "path": '/paper/addpaper' }); },
    // 查看场次
    fnReadPaper (paperid) { 
      this.$router.push({
        'path': '/paper/checkpaper',
        'query': { d: paperid, type: 'read' }
      }); 
    },
    // 审核考试场次
    fnCheckPaper (paperid) { 
      this.$router.push({
        'path': '/paper/checkpaper',
        'query': {d: paperid, type: 'check'}
      }); 
    },
    //删除场次
    fnDelete (val) {
      this.$confirm('此操作将永久删除该考试场次, 是否继续?', '提示', {
        confirmButtonText: '确定',
        canncelButtonText: '取消',
        type: 'warning'
      }).then(_ => {
        let param = {
          model : 'exam',
          contorller: 'Project',
          action : 'delProject',
          id : val
        };
        this.Const.post(this.Const.apiUrl, param).then(response => {
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: '删除成功！'});
          } else {
            this.$message({type: 'info', message: '删除失败！'});
          }
        }).catch(error => {
          this.$message({type: 'error', message: '删除失败！'});
        });
      }).catch(_ => {
        this.$message({ type: 'info', message: '已取消删除' }); 
      });
    },
    //编辑场次
    fnEdit (id) { this.$router.push({ "path": '/paper/addpaper?id=' + id }); },
    // 章节
    loadSections() {
      let params = {
        module: 'service',
        controller: 'Classroom_Sections_Con',
        action: 'getSections',
        id: this.Const.courseData.query.d
      };
      this.$ajax.post(this.Const.apiUrl, params).then( res => {
        if (res.data.status == 0) {
          this.sectionsList = res.data.data;
        } else {
          this.$message({type: 'info', message: res.data.data || '获取章节信息失败！'});
        }
      }).catch( err => {
        this.$message({type: 'error', message: '获取章节信息失败！'});
      });
    },
    // 搜索请求查询
    requestFn () {
      let params = {
        module : 'exam',
        controller : 'project',
        action : 'index',
        //考试场次名关键字
        name: this.search.name,
        //开始时间
        starttime: this.time[0],
        //结束时间
        endtime: this.time[1],
        //考试时长
        limittime: this.search.limittime,
        //课堂id
        classroom: this.Const.courseData.query.d,
        //课堂章节id 
        sections: '',
        //页码
        page: this.page.currentPage,
        //条数
        rows: this.page.pageSize
      };
      this.$ajax.post(this.Const.apiUrl, params).then(response => {
        let resData = response.data;
        if (resData.status == 0) {
          this.paperList = resData.data.data;
          this.page.currentPage = resData.data.page.current_page;
          this.page.total = resData.data.page.total;
        } else {
          this.$message({type: 'info', message: resData.data || '场次列表获取失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: error.message});
      });
    }
  },
  computed: {
    
  }
};
</script>

<style lang="scss">
.questbank {
  .aside {
    width: 180px;
    background: #fff;
    float: left;
    margin-right: 20px;
    .title {
      @include middelH(50px);
      @include setTitle;
    }
    .total-bank {
      height: 85px;
      background: #ff8a00;
      padding-left: 30px;
      color: #fff;
      span {
        @include middelH(40px);
      }
      i {
        font-size: 3rem;
        margin-right: 10px;
      }
    }
    .addbank {
      position: relative;
      margin-top: 20px;
      @include middelH(60px);
      border: 1px solid #32b16c;
      box-sizing: border-box;
      color: #32b16c;
      cursor: pointer;
      i {
        position: absolute;
        top: 13px;
        left: 40px;
        font-size: 3rem;
      }
      span {
        position: absolute;
        top: 0;
        left: 80px;
      }
      &:hover {
        background: #32b16c;
        color: #fff;
      }
    }
  }
  .wrap {
    width: 1000px;
    box-sizing: border-box;
    float: left;
    background: #fff;
    padding: 10px;
    .head-condition {
      padding: 20px;
      border-bottom: 1px solid gray;
      .item-condition {
        margin-right: 30px;
        float: left;
        margin-bottom: 20px;
        .el-input-group,
        .el-input {
          width: auto;
        }
      }
    }
  }
}
.bank-list {
  margin-top: 20px;
  .optionbar {
    margin-bottom: 20px;
  }
  .bank-item {
    border: 1px solid #dcdfe6;
    margin-bottom: 10px;
    box-sizing: border-box;
    &:hover {
      border-color: #32b16c;
    }
  }
  .title {
    padding: 10px;
    background: #f2f2f2;
    .bank-stem {
      width: 75%;
      .timu {
        width: 95%;
        margin-left: 10px;
      }
    }
  }
  .bank-type {
    color: #32b16c;
    font-weight: bold;
  }
  .answer-box {
    padding: 10px 20px;
    ul {
      margin: 10px 0;
    }
    li {
      padding-left: 30px;
      float: left;
      width: 40%;
      text-indent: 10px;
      margin-bottom: 10px;
      > * {
        float: left;
      }
      .answer-item {
        width: 80%;
      }
    }
    .cankao-answer {
      color: #ff8a00;
    }
    .cankaodaan {
      width: 90%;
      margin: 10px auto;
      .can-title {
        color: #ff8a00;
      }
      > p {
        text-align: center;
      }
    }
    .panduan {
      width: 90%;
      margin: 10px auto;
      p {
        margin-bottom: 10px;
      }
    }
  }
  .comprehensive {
    padding-left: 80px;
    .answer-list {
      margin-bottom: 20px;
    }
  }
  .note {
    @include middelH(30px);
    margin-right: 20px;
    .success {
      color: #67c23a;
    }
    .danger {
      color: #e6a23c;
    }
    .error {
      color: #f56c6c;
      cursor: pointer;
    }
  }
  .pic-box {
    position: relative;
    height: 200px;
    background: #f2f2f2;
    .pic {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      margin: 0 auto;
      width: 350px;
    }
    .annotation {
      position: absolute;
      bottom: 10px;
      left: 0;
      width: 100%;
      text-align: center;
      font-size: 1.4rem;
      color: gray;
    }
  }
  .gf {
    margin: 10px;
    .gf-title {
      color: #32b16c;
    }
    .gf-content {
      display: inline-block;
    }
  }
}

.flow-side-box {
  position: fixed;
  margin-left: 990px;
  top: 60%;
  width: 60px;
  background: #838485;
  color: #fff;
  cursor: pointer;
  .go-top {
    height: 60px;
    i {
      @include middelH(60px);
      font-size: 3rem;
      display: block;
      text-align: center;
    }
    &:hover {
      background: #32b16c;
    }
  }
  .add-bank-btn {
    position: relative;
    height: 140px;
    i {
      position: absolute;
      top: 10px;
      left: 15px;
      display: block;
      font-size: 3rem;
    }
    span {
      position: absolute;
      top: 44px;
      left: 17px;
      width: 26px;
      display: block;
      text-align: center;
      line-height: 20px;
    }
    &:hover {
      background: #32b16c;
      color: #fff;
    }
  }
}
.paging {
  margin: 30px 0;
}
</style>