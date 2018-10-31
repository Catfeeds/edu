<template>
  <div class="bankindex paperbank">
    <div class="paperbody main" id="paper">
      <div class="title">
        <span><i class="el-icon-d-arrow-right"></i>试卷详情</span>
        <div class="fr"><el-button @click="$router.back(-1)" round type="primary" plain>返回</el-button></div>
      </div>
      <div class="body-content">
        <div class="test-list">
          <div class="list">
            <div class="list-title">
              <span>试卷基本信息</span>
              <i id="test-title"></i>
            </div>
            <div class="check-btn" v-if="bankInfo.check == 5">
              <el-button-group>
                <el-button type="success" icon="el-icon-check" @click="subPass"></el-button>
                <el-button type="warning" icon="el-icon-close" @click="subNoPass"></el-button>
              </el-button-group>
            </div>
            <div class="list-subject" v-if="questionTypeList.length">
              <ul class="subjectlist" id="listi">
                <li :key="index" v-for="(item, index) in questionTypeList" :class="{clickon: item.tag === selTag}" v-on:click="swichchapters(item.tag)">
                  {{ item.tag | transformTypeTag(bankTypeList) }}
                </li>
              </ul>
            </div>
            <div class="list-notes">
              <div class="notes-title">
                <span>试卷考法</span>
                <span>按总时长计时</span>
              </div>
              <div class="notes-others">
                <div class="lit">创建者：
                  <span>{{bankInfo.createdbyid}}</span>
                </div>
                <div class="lit" v-if="bankInfo.uploadbyid">最近修改者：
                  <span>{{bankInfo.uploadbyid}}</span>
                </div>
                <div class="lit">试卷类型:
                  <span>{{bankInfo.examtype | examtypeTransform}}</span>
                </div>
                <div class="lit" v-if="bankInfo.sectionname">所属章：<br>
                  <el-tag size="small" v-for="(item, index) in bankInfo.sectionname" :key="index">{{item}}</el-tag>
                  <!-- <span>{{bankInfo.sectionname}}</span> -->
                </div>
                <div class="lit" v-if="bankInfo.sectionsname">所属节：<br>
                  <el-tag size="small" v-for="(item, index) in bankInfo.sectionsname" :key="index">{{item}}</el-tag>
                  <!-- <span>{{bankInfo.sectionsname}}</span> -->
                </div>
                <div class="lit">试卷开始时间：<br>
                  <span>{{bankInfo.timeopen}}</span>
                </div>
                <div class="lit">试卷结束时间：<br>
                  <span>{{bankInfo.timeclose}}</span>
                </div>
                <div class="lit">试卷考试时间：
                  <span>{{bankInfo.timelimit}}</span>分钟
                </div>
                <div class="lit">选题方式：
                  <span>{{bankInfo.shufflequestions | shufflequestionsTranform}}</span>
                </div>
                <div class="lit">总题数：
                  <span>{{allBankList.length}}</span>题
                </div>
                <div class="lit">总分值：
                  <span>{{bankInfo.grade | gradeTransform}}</span>分
                </div>
                <div class="lit">评分方式：
                  <span>{{bankInfo.grademethod}}</span>分
                </div>
                <div class="lit">试卷说明：
                  <span>{{bankInfo.intro}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="test-content">
          <div class="content-title">
            <span class="pre">预览试卷</span>
            <div class="oth">
              <span>试卷名称：{{bankInfo.name}}</span>
              <p v-if="bankInfo.check == 6 || bankInfo.check == 2" class="check-status pass"><i class="el-icon-check"></i></p>
              <p v-else class="check-status nopass"><i class="el-icon-close"></i></p>
            </div>
          </div>
          <div class="bank-list">
            <div class="bank-list-wrap">
              <!-- 循环试题列表 -->
              <div v-for="(item, index) in previewList" :key="index" class="bank-item clearfix">
                <!-- 题干 -->
                <div class="title clearfix">
                  <div class="xxxx">
                    <div class="timu fl">
                      <span class="num-order">
                        <i>{{ index + 1 }}</i>、</span>
                      <span class="bank-type" v-text="tranforAll(item.qtype, bankTypeList)"></span>
                      <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span><div v-html="item.questiontext"></div></div>
                  </div>
                </div>
                <!-- 题干的图片区 -->
                <div v-if="item.imgs && item.imgs.length > 0" class="pic-box">
                  <div class="pic">
                    <el-carousel height="150px">
                      <el-carousel-item v-for="(item, index) in item.imgs" :key="index">
                        <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                      </el-carousel-item>
                    </el-carousel>
                  </div>
                  <div class="annotation">
                    <p>图片注释</p>
                  </div>
                </div>
                <!-- 答案区域 -->
                <div v-if="item.qtype === 'singlechoice' || item.qtype === 'multiplechoice'" class="answer-box">
                  <ul class="clearfix">
                    <li v-for="(item1, index1) in item.answer" :key="index1" :class="{'cankao-answer': item1.fraction === '100'}">
                      <p><i>{{Const.optionsTab[index1]}}</i>&nbsp;:&nbsp;</p>
                      <div class="answer-item">
                        <p class="answer-con-option">{{ item1.name }}</p>
                        <img v-if="item1.imgs && item1.imgs.length > 0" :src="item1.imgs[0]" alt="" height="100">
                      </div>
                    </li>
                  </ul>
                </div>
                <!-- 简答题 -->
                <div v-if="item.qtype === 'essay'" class="answer-box">
                  <div class="cankaodaan">
                    <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                    <span class="answer-con-option">{{ item.answer.name }}</span>
                    <p><img v-if="item.answer.imgs && item.answer.imgs.length > 0" :src="item.answer.imgs[0]" alt="" height="100"></p>
                  </div>
                </div>
                <!-- 填空题 -->
                <div v-if="item.qtype === 'shortanswer'" class="answer-box">
                  <div class="cankaodaan">
                    <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                    <span v-for="(item1, index1) in item.answer" :key="index1" class="answer-con-option">{{ item1 }}&nbsp;&nbsp;</span>
                  </div>
                </div>
                <!-- 判断题 -->
                <div v-if="item.qtype === 'truefalse'" class="answer-box">
                  <div class="cankaodaan">
                    <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                    <span v-if="item.answer.fraction=='0'" class="answer-con-option">正 确</span>
                    <span v-if="item.answer.fraction=='1'" class="answer-con-option">错 误</span>
                  </div>
                </div>
                <!-- 综合题 -->
                <div v-if="item.qtype === 'readingcomprehension'" class="answer-box comprehensive">
                  <div class="answer-list" v-for="(item1, index1) in item.option" :key="index1">
                    <p class="answer-name"><span>({{ index1+1 }})、&nbsp;&nbsp;</span><span class="bank-type" v-text="tranforAll(item1.qtype, bankTypeList)"></span>&nbsp;|&nbsp;{{ item1.name }}</p>
                    <template v-if="item1.qtype === 'essay'">
                      <div class="cankaodaan answer-con">
                        <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                        <span class="answer-con-option">{{ item1.answer.name }}</span>
                        <p><img v-if="item1.answer.imgs && item1.answer.imgs.length > 0" :src="item1.answer.imgs[0]" alt="" height="100"></p>
                      </div>
                    </template>
                    <template v-if="item1.qtype === 'singlechoice' || item1.qtype === 'multiplechoice'">
                      <ul class="clearfix">
                        <li v-for="(item2, index2) in item1.answer" :key="index2" :class="{'cankao-answer': item2.fraction === '100'}">
                          <p><i>A</i>&nbsp;:&nbsp;</p>
                          <div class="answer-item">
                            <p class="answer-con-option">{{ item2.name }}</p>
                            <img v-if="item2.imgs && item2.imgs.length > 0" :src="item2.imgs[0]" alt="" height="100">
                          </div>
                        </li>
                      </ul>
                    </template>
                    <template v-if="item1.qtype === 'truefalse'">
                      <div class="cankaodaan">
                        <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                        <span v-if="item1.answer.fraction=='0'" class="answer-con-option">正 确</span>
                        <span v-if="item1.answer.fraction=='1'" class="answer-con-option">错 误</span>
                      </div>
                    </template>
                  </div>
                </div>
                <!-- 阅读理解 -->
                <div v-if="item.qtype === 'comprehensive'" class="answer-box comprehensive">
                  <div class="answer-list" v-for="(item1, index1) in item.option" :key="index1">
                    <p class="answer-name"><span>({{ index1+1 }})、&nbsp;&nbsp;</span><span class="bank-type">简答题</span>&nbsp;|&nbsp;{{ item1.name }}</p>
                    <div class="cankaodaan answer-con">
                      <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                      <span class="answer-con-option">{{ item1.answer.name }}</span>
                      <p><img v-if="item1.answer.imgs && item1.answer.imgs.length > 0" :src="item1.answer.imgs[0]" alt="" height="100"></p>
                    </div>
                  </div>
                </div>
                <!-- 题目解析 -->
                <div class="gf">
                  <span class="gf-title">题目解析:&nbsp;&nbsp;</span>
                  <div class="gf-content" v-html="item.generalfeedback"></div>
                </div>
              </div>
            </div>
          </div>
          <!--分页 start-->
          <!-- <div class="block">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="currentPage" :page-sizes="pagesizes" :page-size="pagesize" layout="total, sizes, prev, pager, next, jumper" :total="totals">
            </el-pagination>
          </div> -->
          <!--分页 end -->
        </div>
        <div class="icon-gotop" :class="{goTop: isTop}">
          <i class="iconfont" id="iconfont" v-on:click="goBacktop()">&#xe61b;</i>
          <span>回到顶部</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    let thisVue = this;
    return {
      bankInfo: {
        createdbyid: '',
        uploadbyid: '',
        grade: '',
        check: '',
        intro: '',
        name: '',
        examtype: '',
        timeopen: '',
        timeclose: '',
        timelimit: '',
        grademethod: '',
        sectionname: '',
        sectionsname: '',
        shufflequestions: ''
      },
      bankTypeList: this.Const.bankTypeList,
      allBankList: [],
      previewList: [],
      // 当前选中的tag
      selTag: "danxuan",
      // 试卷列表数据
      questionTypeList: [],
      //回到顶部
      isTop: false,
      //分页
      pagesizes: [5, 10, 20, 30, 40],
      totals: 40,
      pagesize: 10,
      currentPage: 4
    };
  },
  mounted() {
    var _this = this;
    //当滚动条的位置处于距顶部100像素以上时，跳转链接出现，否则消失
    $(window).scroll(function() {
      _this.isTop = ($(window).scrollTop() > 200) ? true : false;
    });
    this.requAllBank();
    this.requBankInfo();
  },
  methods: {
    //导航内容切换
    swichchapters: function(tag) {
      this.selTag = tag;
    },
    //回到顶部
    goBacktop: function() {
      if ($("html").scrollTop()) {
        $("html").animate({ scrollTop: 0 }, 1000);
        return false;
      }
      $("body").animate({ scrollTop: 0 }, 1000);
      return false;
    },
    //页码显示
    handleSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    handleCurrentChange(val) {
      console.log(`当前页: ${val}`);
    },
    // 转换题型
    tranforAll (obj, list) {
      let typeArr = list.filter(item => (item.value == obj) ? true : false);
      return typeArr[0].name;
    },
    // 通过审核
    subPass () {
      this.$confirm('审核通过的试卷不能在做任何修改, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$ajax.post(this.Const.apiUrl, {
          module: 'exam',
          controller: 'Quiz',
          action: 'audit',
          id: this.$route.query.d,
          status: '0',
          msg: ''
        }).then(response => {
          let resDate = response.data;
          if (resDate.status == 0) {
            this.$message({type: 'success', message: '审核成功！'});
          } else {
            this.$message({type: 'info', message: '审核失败！'});
          }
        }).catch(error => {
          this.$message({type: 'error', message: '审核失败！'});
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        });          
      });
    },
    // 审核未通过
    subNoPass () {
      this.$prompt('请输入此试卷审核不通过原因', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputPattern: /\S/,
        inputErrorMessage: '请输入此试卷审核不通过原因'
      }).then(({ value }) => {
        this.$ajax.post(this.Const.apiUrl, {
          module: 'exam',
          controller: 'Quiz',
          action: 'audit',
          id: this.$route.query.d,
          status: '1',
          msg: value
        }).then(response => {
          let resDate = response.data;
          if (resDate.status == 0) {
            this.$message({type: 'success', message: '审核成功！'});
          } else {
            this.$message({type: 'info', message: '审核失败！'});
          }
        }).catch(error => {
          this.$message({type: 'error', message: '审核失败！'});
        });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '取消输入'
        });       
      });
    },
    // 获取试卷的试题信息
    requAllBank () {
      const loading = this.$loading({
        lock: true,
        text: 'Loading',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.7)'
      });
      this.$ajax.post(this.Const.apiUrl, {
        module: 'exam',
        controller: 'Quiz',
        action: 'getQuizQuestionList',
        quiz: this.$route.query.d
      }).then(response => {
        loading.close();
        console.log('试题list' ,response);
        let resDate = response.data;
        if (resDate.status == 0) {
          this.allBankList = resDate.data;
          for (let i = 0, item; item = this.allBankList[i]; i++) {
            let isIn = false;
            for (let j = 0, jtem; jtem = this.questionTypeList[j]; j++) {
              if (item.qtype === jtem.tag) {
                isIn = true;
                break;
              }
            }
            if (!isIn) {
              this.questionTypeList.push({ tag: item.qtype });
            }
          }
          this.selTag = this.questionTypeList.length ? this.questionTypeList[0]['tag'] : '';
        } else {
          this.allBankList = [];
          this.$message({type: 'info', message: resDate.data ? resDate.data : '获取信息失败！'});
        }
      }).catch(error => {
        loading.close();
        this.$message({type: 'error', message: '获取信息失败！'});
      });
    },
    // 获取试卷基础属性
    requBankInfo () {
      this.$ajax.post(this.Const.apiUrl, {
        module: 'exam',
        controller: 'Quiz',
        action: 'quizDetail',
        id: this.$route.query.d
      }).then(response => {
        let resDate = response.data;
        if (resDate.status == 0) {
          this.bankInfo = resDate.data;
        } else {
          this.$message({type: 'info', message: '获取试卷属性失败！'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: '获取试卷属性失败！'});
      });
    }
  },
  filters: {
    shufflequestionsTranform (val) {
      return (val == 0) ? '自动选题' : '手动选题';
    },
    gradeTransform (val) {
      return parseInt(val);
    },
    examtypeTransform (val) {
      return (val == 0) ? '测试' : '考试';
    },
    transformTypeTag (value, list) {
      let arr = list.filter(item => value == item.value);
      return arr.length ? arr[0].name : '';
    }
  },
  watch: {
    "$route": {
      handler (to, from) {
        if (to.query.d) {
          this.requBankInfo();
        }
      },
      deep: true
    },
    selTag: {
      handler () {
        this.previewList = this.allBankList.filter(item => this.selTag === item.qtype);
      },
      immediate: true
    }
  }
};
</script>

<style lang="scss">
#paper {
  position: absolute;
  /*min-height: 400px;*/
  height: auto;
  margin: 0 auto;
  padding: 20px;
  box-sizing: border-box;
  background-color: #f8f9fb;
  .test-list {
    position: relative;
    width: 200px;
    min-height: 500px;
    float: left;
    .list {
      .list-title {
        position: relative;
        width: 200px;
        height: 35px;
        margin: 0 auto;
        background-color: #28bdb9;
        color: white;
        text-align: center;
        font-size: 0.1rem;
        line-height: 35px;
        cursor: pointer;
      }
      .list-notes {
        position: relative;
        width: 200px;
        height: 205px;
        margin: 0 auto;
        color: black;
        text-align: center;
        border: 1px solid #f0f0f7;
        margin-top: 10px;
        background: white;
        .notes-title {
          position: relative;
          top: 10px;
          width: 180px;
          height: 65px;
          background: #fafafc;
          border: 1px solid #fafafc;
          margin: 0 auto;
          span:nth-child(1) {
            color: #baa8a0;
            font-size: 0.1rem;
            display: block;
            margin-top: 10px;
            text-align: left;
            margin-left: 10px;
          }
          span:nth-child(2) {
            color: black;
            font-size: 0.2rem;
            display: block;
            text-align: left;
            margin-left: 10px;
          }
        }
        .notes-others {
          position: relative;
          margin-top: 30px;
          text-align: left;
          .lit {
            position: relative;
            margin-left: 10px;
            margin-bottom: 8px;
            span {
              color: #f2842a;
              margin-right: 8px;
              font-weight: bold;
            }
          }
        }
      }
      .check-btn {
        width: 200px;
        margin: 10px auto 40px;
        .el-button {
          width: 100px;
          border-radius: 0;
          height: 50px;
        }
      }
    }
    .list-subject{
      margin-top: 10px;
      ul li {
        position: relative;
        width: 200px;
        @include middelH(42px);
        color: black;
        text-align: center;
        font-size: 0.1rem;
        border: 1px solid #f0f0f7;
        background: #fbfbfc;
        margin: 0 auto;
        box-sizing: border-box;
        cursor: pointer;
        box-shadow: 0px 1px 1px #dde0e7;
        .click {
          color: #28bdb9;
          border: 1px solid #28bdb9;
        }
        &.clickon,
        &:hover {
          color: #28bdb9;
          border-color: #28bdb9;
        }
      }
    } 
    
  }
  .test-content {
    position: relative;
    width: 920px;
    height: auto;
    margin-left: 40px;
    float: left;
    .content-title {
      position: relative;
      @include middelH(100px);
      background: white;
      box-shadow: 0px 1px 1px #dde0e7;
      .pre {
        color: gray;
        margin-left: 10px;
        float: left;
      }
      .oth {
        position: relative;
        text-align: center;
        color: black;
        overflow: hidden;
        .check-status{
          position: absolute;
          top: -4px;
          right: -38px;
          width: 110px;
          height: 50px;
          color: #fff;
          line-height: 60px;
          font-size: 20px;
          transform: rotate(45deg);
          text-align: center;
          &.pass{background: #28bdb9;}
          &.nopass{ background: #f56c6c;}
          i {
            transform: rotate(-45deg);
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
    .block {
      text-align: right;
      margin-top: 40px;
      margin-bottom: 20px;
    }
  } 
  //回到顶部图标样式
  .icon-gotop {
    position: fixed;
    bottom: 120px;
    right: 55px;
    cursor: pointer;
    display: none;
    height: 40px;
    span {
      color: gray;
      display: block;
      margin-left: -10px;
    }
  }

  .iconfont {
    font-family: "iconfont" !important;
    font-size: 30px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    color: gray;
  }

  .goTop {
    display: block;
  }
}

/* 回到顶部图标引入*/

@font-face {
  font-family: "iconfont";
  /* project id 666106 */
  src: url("https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.eot");
  src: url("https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.eot?#iefix")
      format("embedded-opentype"),
    url("https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.woff")
      format("woff"),
    url("https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.ttf")
      format("truetype"),
    url("https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.svg#iconfont")
      format("svg");
}
</style>
