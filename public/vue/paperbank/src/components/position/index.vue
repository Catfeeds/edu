<template>
  <div class="bankindex questbank">
    <aside class="aside">
      <p class="title">
        <i class="el-icon-d-arrow-right"></i> 课程试卷</p>
      <div class="total-bank">
        <span>试卷总数</span>
        <p>
          <i>{{ page.total }}</i>套</p>
      </div>
      <div class="addbank"  @click="fncreate()">
        <i class="el-icon-circle-plus"></i>
        <span>新建考试</span>
      </div>
    </aside>
    <section class="wrap">
      <div class="head-condition clearfix">
        <p class="item-condition">
          <el-input placeholder="输入试卷名称关键字搜索" v-model="search.keywords" >
          <el-button slot="append" icon="el-icon-search" @click="requestFn()"></el-button></el-input>
        </p>
        <el-radio-group  v-model="search.examtype" @change="requestFn()">
          <el-radio-button :key="index" v-for="(item, index) in paperexamtypeList" :label="item.value" >{{ item.name }}</el-radio-button>
        </el-radio-group>
        <p class="item-condition">
         <el-select v-model="search.check" placeholder="请选择审核状态" @change="requestFn()">
           <el-option v-for="item in papercheckList " :key="item.value" :label="item.name" :value="item.value">
           </el-option>
         </el-select>
        </p>
      </div>
      <div class="bank-list">
        <el-table :data="paperList" stripe max-height="450" border style="width: 100%">
          <el-table-column prop="name" fixed label="试卷名称" :show-overflow-tooltip="true" width="150"></el-table-column>
          <el-table-column prop="classroom" label="课堂名" :show-overflow-tooltip="true" width="150"></el-table-column>
          <el-table-column prop="examtype" label="试卷用途" width="80">
            <template slot-scope="scope">
              <el-tag size="medium">{{scope.row.examtype}}</el-tag>
              <!-- <el-tag type="danger" v-if="scope.row.examtype == 0" size="medium">测试</el-tag>
              <el-tag  v-if="scope.row.examtype == 1" size="medium">考试</el-tag> -->
            </template>
          </el-table-column>
          <el-table-column prop="timelimit" label="考试用时" width="100"></el-table-column>
          <el-table-column prop="disparktime" label="开放时间(分)" width="150" :show-overflow-tooltip="true"></el-table-column>
          <el-table-column prop="grade" label="总分" width="80"></el-table-column>
          <el-table-column prop="status" label="审核状态" width="100">
            <template slot-scope="scope">
              <el-tag type="danger"  v-if="scope.row.state_code == 1 || scope.row.state_code == 2 || scope.row.state_code == 3 || scope.row.state_code == 4 || scope.row.state_code == 5" size="medium">{{scope.row.state}}</el-tag>
              <el-tag type="success" v-if="scope.row.state_code == 6" size="medium">{{scope.row.state}}</el-tag>
              <el-tag type="danger"  v-if="scope.row.state_code == 7" size="medium">{{scope.row.state}}</el-tag>
            </template>
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="170">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="fndelete(scope.row.id)">删除</el-button>
              <el-button type="text" size="small" @click="fneditsj(scope.row.id)">试卷</el-button>
              <el-button type="text" size="small" @click="fneditst(scope.row.id)">试题</el-button>
              <el-button type="text" size="small" @click="fnPreview(scope.row.id)">预览</el-button>
              <el-button v-if="scope.row.state_code == 5" type="text" size="small" @click="fnPreview(scope.row.id)">审核</el-button>
              <el-button v-if="scope.row.state_code == 1 " type="text" size="small" @click="fnApply(scope.row.id)">申请审核</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div v-show="sideshowhide" class="flow-side-box">
        <div class="go-top" title="回到顶部" @click="fnGoTop">
          <i class="el-icon-arrow-up"></i>
        </div>
        <div class="add-bank-btn">  <!-- @click="fnAddPaper" -->
          <i class="el-icon-circle-plus"></i>
          <span>添加试题</span>
        </div>
      </div>
      
      <div class="paging">
        <el-pagination background 
        @size-change="handleSizeChange" 
        @current-change="handleCurrentChange" 
        :current-page="page.currentPage" 
        :page-sizes="[5,15,20,50]" 
        :page-size="page.pageSize" 
        layout="total,sizes,prev,pager,next,jumper" :total="page.total"></el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
     let thisVue = this;
     let params = [];
    return {
      search: {
        keywords: '',
        examtype: '',
        status:'',
        check: ''
      },
      // 分页数据
      page: {
        currentPage: 1,
        pageSize: 5,
        total: 0
      },
      paperexamtypeList: this.Const.paperexamtypeList,
      papercheckList: this.Const.papercheckList,
      // 侧边栏按钮显示影藏
      sideshowhide: false,
      dialogVisible: false,
      paperList: []     
    };
  },
  mounted() {  //预加载函数
    this.showhideSideBar();
    this.requestFn();
  },
  methods: {
    requestFn () {
      let params = {
        module : 'exam',
        controller : 'Quiz',
        action : 'getQuizList',
        classroom: this.Const.courseData.query.d,
        keywords: this.search.keywords,
        examtype: this.search.examtype,
        check: this.search.check,
        page: this.page.currentPage,
        rows: this.page.pageSize
      };
      this.$ajax.post(this.Const.apiUrl, params).then((response) => {
        console.log(response);
        let resData = response.data;
        if (resData.status == 0) {
          this.page.currentPage = resData.data.page.current_page;
          this.page.total = resData.data.page.total;
          this.paperList = resData.data.data;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取试卷失败！'});
        }
      }).catch((error) => {
        this.$message({type: 'error', message: error.message});
      });
    },
    //跳转新建
    fncreate () {
      this.$router.push({ "path": '/position/posi' });
    },
    //跳转编辑试卷
    fneditsj (id) {
      this.$router.push({ "path": '/position/posi?id='+id });
    },
    //跳转编辑试卷的试题
    fneditst (id) {
      this.$router.push({ "path": '/posi/additems?id='+id });
    },
    // 调换预览试卷 审核试卷
    fnPreview (id) {
      this.$router.push({ "path": '/posi/previewtest', "query": { d: id } });
    },
    // 申请审核
    fnApply (id) {
      this.$ajax.post(this.Const.apiUrl, {
        module: 'exam',
        controller: 'Quiz',
        action: 'applyAudit',
        id: id
      }).then(response => {
        let resData = response.data;
        if (resData.status == 0) {
          this.$message({type: 'success', message: '申请审核成功，其他管理员可以审核了！'});
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '申请审核失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: '申请审核错误！'});
      });
    },
    // 侧边楼梯导航显示隐藏
    showhideSideBar() {
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
    fnGoTop() { $("html, body").animate({ scrollTop: "0" }); },
    // 每页显示条数改变
    handleSizeChange(val) {
      this.page.pageSize = val;
      this.requestFn();
    },
    // 当前页码改变
    handleCurrentChange(val) {
      this.page.currentPage = val;
      this.requestFn();
    },
    //删除试卷
    fndelete (val) {
      this.$confirm('此操作将永久删除该试卷, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let params = {
          module : 'exam',
          controller: 'Quiz',
          action : 'delQuiz',
          id : val
        };
        this.$ajax.post(this.Const.apiUrl, params).then(res => {
          console.log('删除数据', res.data);
          if (res.data.status == 0) {
            this.$message({type: 'success', message: '删除成功！'});
            this.requestFn();
          } else {
            this.$message({type: 'info', message: res.data.data ? res.data.data : '删除失败！'});
          }
        }).catch((error) => {
          this.$message({type: 'error', message: res.data.data ? res.data.data : '删除失败！'});
        });
      }).catch(() => {
        this.$message({ type: 'info', message: '已取消删除' });          
      });
    }, 
    //添加试卷
    fnAddposition () {
      this.$router.push({ "path": '/paper/Addposition' });
    },
  },
  computed: { },
  
}
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