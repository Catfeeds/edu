<template>
  <div class="bankindex questbank">
    <aside class="aside">
      <p class="title">
        <i class="el-icon-d-arrow-right"></i> 我的题库</p>
      <div class="total-bank">
        <span>题库总题数</span>
        <p>
          <i>{{ questionNum.totalbank }}</i>题</p>
      </div>
      <div class="total-bank pass">
        <span>可用题目</span>
        <p><i>{{ questionNum.pass }}</i>题</p>
      </div>
      <div class="total-bank nopass">
        <span>审核未通过题数</span>
        <p><i>{{ questionNum.nopass }}</i>题</p>
      </div>
      <div class="total-bank waitpass">
        <span>待审核题数</span>
        <p><i>{{ questionNum.waitpass }}</i>题</p>
      </div>
      <div class="total-bank waitpass">
        <span>创建完成</span>
        <p><i>{{ questionNum.create }}</i>题</p>
      </div>
      <div class="addbank" @click="fnAddBank">
        <i class="el-icon-circle-plus"></i>
        <span>添加试题</span>
      </div>
      <div class="addbank" @click="fnCheckBank">
        <i class="el-icon-edit-outline"></i>
        <span>审核试题</span>
      </div>
    </aside>
    <section class="wrap">
      <div class="head-condition clearfix">
        <p class="item-condition">
          <span>审核状态: </span>
          <el-select v-model="search.check" placeholder="请选择">
            <el-option v-for="(item,index) in checkList" :label="item.name" :key="index" :value="item.id"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>使用权限: </span>
          <el-select v-model="search.qusepermiss" placeholder="请选择">
            <el-option v-for="(item,index) in qusepermissList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>知识点: </span>
          <!-- <el-select v-model="search.knowledgePoint" placeholder="请选择">
            <el-option v-for="(item,index) in knowledgePointList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select> -->
          <el-cascader placeholder="搜索知识点" :options="knowledgePointList" v-model="search.knowledgePoint" :props="sectionProps" filterable change-on-select></el-cascader>
        </p>
        <p class="item-condition">
          <span>题目类型: </span>
          <el-select v-model="search.bankType" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option v-for="(item,index) in bankTypeList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>难易程度: </span>
          <el-select v-model="search.complexity" placeholder="请选择">
            <el-option label="全部" value=""></el-option>
            <el-option v-for="(item,index) in complexityList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>出题时间: </span>
          <el-date-picker :disabled="true" v-model="search.rangeTime" type="daterange" align="right" unlink-panels range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" :picker-options="pickerTimeOptions">
          </el-date-picker>
        </p>
        <p class="item-condition">
          <span>内容搜索: </span>
          <el-input placeholder="输入内容进行搜索" v-model="search.searchCon">
            <el-button slot="append" icon="el-icon-search"></el-button>
          </el-input>
        </p>
        <el-button class="search fl" type="primary" @click="requestBankAll">搜索试题</el-button>
      </div>
        <div class="bank-list">
          <!-- 全选 -->
          <div class="optionbar">
            <!-- <el-checkbox v-model="selectItem.selectAll" :indeterminate="selectItem.isIndeterminate" @change="addquesList" >全选</el-checkbox>
            <el-button type="danger" plain @click="fndelete"  >删除</el-button> -->
          </div>
          <div class="bank-list-wrap">
            <!-- <el-checkbox-group v-model="selectItem.quesIdList" @change="handleCheckedCitiesChange"> -->
              <div v-for="(item, index) in questionLists" :key="index" class="bank-item clearfix">
                <!-- 题干 -->
                <div class="title clearfix">
                  <div class="bank-stem clearfix fl">
                    <div class="timu fl">
                      <span class="num-order"><i>{{ index+1 }}</i>、</span>
                      <span class="bank-type" v-text="tranformL(item.qtype, bankTypeList)"></span>
                      <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>
                      <div class="bank-timu-con" v-html="item.questiontext"></div>
                    </div>
                  </div>
                  <!-- 题右上角按钮 -->
                  <div class="opr fr">
                    <el-button-group>
                      <el-button type="info" icon="el-icon-edit" @click="fnEdit(item.id)" size="mini"></el-button>
                      <el-button type="info" icon="el-icon-star-off" size="mini"></el-button>
                      <el-button type="info" icon="el-icon-delete" @click="fndelete(item.id)" size="mini"></el-button>
                    </el-button-group>
                  </div>
                </div>
                <!-- 题干的图片区 -->
                <div v-if="item.imgs && item.imgs.length > 0" class="pic-box">
                  <div class="pic">
                    <el-carousel height="150px">
                      <el-carousel-item v-for="(item1, index) in item.imgs" :key="index">
                        <img :d="item1" src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                      </el-carousel-item>
                    </el-carousel>
                  </div>
                  <!-- <div class="annotation">
                    <p>图片注释</p>
                  </div> -->
                </div>
                <!-- 答案区域 -->
                <div v-if="item.qtype === 'singlechoice' || item.qtype === 'multiplechoice'" class="answer-box">
                  <ul class="clearfix">
                    <li v-for="(item1, index1) in item.answer" :key="index1" :class="{'cankao-answer': item1.fraction === '100'}">
                      <p><i>{{item1.tab}}</i>&nbsp;:&nbsp;</p>
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
                    <span class="answer-con-option">{{ item.answer }}</span>
                    <!-- <p><img v-if="item.answer.imgs && item.answer.imgs.length > 0" :src="item.answer.imgs[0]" alt="" height="100"></p> -->
                  </div>
                </div>
                <!-- 填空题 -->
                <div v-if="item.qtype === 'shortanswer'" class="answer-box">
                  <div class="cankaodaan">
                    <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                    <div v-for="(item1, index1) in item.answer" :key="index1" class="answer-con-option">
                      <span v-for="(value, index2) in item1" :key="index2">{{value}}</span>
                    </div>
                  </div>
                </div>
                <!-- 判断题 -->
                <div v-if="item.qtype === 'truefalse'" class="answer-box">
                  <div class="cankaodaan">
                    <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                    <span v-for="(item1, index) in item.answer" :key="index" v-if="item1.fraction=='100'" class="answer-con-option">正 确</span>
                  </div>
                </div>
                <!-- 综合题 -->
                <div v-if="item.qtype === 'readingcomprehension'" class="answer-box comprehensive">
                  <div class="answer-list" v-for="(item1, index1) in item.option" :key="index1">
                    <div class="answer-name">
                      <span>({{ index1+1 }})、&nbsp;&nbsp;</span>
                      <span class="bank-type" v-text="tranformL(item1.qtype, bankTypeList)"></span>&nbsp;|&nbsp;
                      <div class="option-con" v-html="item1.questiontext"></div>
                    </div>
                    <template v-if="item1.qtype === 'essay'">
                      <div class="cankaodaan answer-con">
                        <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                        <span class="answer-con-option">{{ item1.answer }}</span>
                      </div>
                    </template>
                    <template v-if="item1.qtype === 'singlechoice' || item1.qtype === 'multiplechoice'">
                      <ul class="clearfix">
                        <li v-for="(item2, index2) in item1.answer" :key="index2" :class="{'cankao-answer': item2.fraction === '100'}">
                          <p><i>{{item2.tab}}</i>&nbsp;:&nbsp;</p>
                          <div class="answer-item">
                            <p class="answer-con-option">{{ item2.name }}</p>
                            <!-- <img v-if="item2.imgs && item2.imgs.length > 0" :src="item2.imgs[0]" alt="" height="100"> -->
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
                    <div class="answer-name">
                      <span>({{ index1+1 }})、&nbsp;&nbsp;</span>
                      <span class="bank-type" v-text="tranformL(item1.qtype, bankTypeList)"></span>&nbsp;|&nbsp;
                      <div class="option-con" v-html="item1.questiontext"></div>
                    </div>
                    <div class="cankaodaan answer-con">
                      <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                      <span class="answer-con-option">{{ item1.answer }}</span>
                      <!-- <p><img v-if="item1.answer.imgs && item1.answer.imgs.length > 0" :src="item1.answer.imgs[0]" alt="" height="100"></p> -->
                    </div>
                  </div>
                </div>
                <!-- 题目解析 -->
                <div class="gf">
                  <span class="gf-title">题目解析:&nbsp;&nbsp;</span>
                  <div class="gf-content" v-html="item.generalfeedback"></div>
                </div>
                <!-- 题目信息区 -->
                <div class="note fr">
                  <p>审核状态：
                    <span v-if="item.check == '1'" class="success">可用</span>
                    <span v-if="item.check == '2'" class="error" @click="fnReadCheckMsg(item.id)">审核未通过</span>
                    <span v-if="item.check == '3'" class="danger">申请审核</span>
                    <span v-if="item.check == '4'" class="success">创建完成</span>
                    <span v-if="item.check == '5'" class="success">调整完成</span> | 课题：
                    <span>{{ item.section }}/{{ item.sections }}</span> | 标签：
                    <span v-text="tranformL(item.usepermise, qusepermissList)"></span> | 难度：
                    <span v-text="tranformL(item.difficulty, complexityList)"></span> | 添加日期：
                    <span>{{ item.timecreated }}</span> | 作者：
                    <span>{{ item.username }}</span>
                  </p>
                </div>
              </div>
            <!-- </el-checkbox-group> -->
          </div>
        </div>
      <div v-show="sideshowhide" class="flow-side-box">
        <div class="go-top" title="回到顶部" @click="fnGoTop">
          <i class="el-icon-arrow-up"></i>
        </div>
        <div class="add-bank-btn" @click="fnAddBank">
          <i class="el-icon-circle-plus"></i>
          <span>添加试题</span>
        </div>
      </div>
      <div class="paging">
        <el-pagination background @size-change="handleSizeChange" 
        @current-change="handleCurrentChange" :current-page="page.currentPage" 
        :page-sizes="[5,10,20,50]" :page-size="page.pageSize" 
        layout="total,sizes,prev,pager,next,jumper" :total="page.total"></el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    let thisVue = this;
    return {
      courseid: this.Const.courseData.query.d,
      // 分页数据
      page: {
        currentPage: 1,
        pageSize: 5,
        total:  0 
      },
      // 审核状态
      checkList: this.Const.checkList,
      // 使用权限
      qusepermissList: this.Const.qusepermissList,
      // 知识点列表
      knowledgePointList: [],
      sectionProps: {
        value: 'id',
        label: 'name',
        children: 'child'
      },
      // 题型
      bankTypeList: this.Const.bankTypeList,
      // 难易程度
      complexityList: this.Const.complexityList,
      // 快捷时间
      pickerTimeOptions: {
        shortcuts: [
          {
            text: "最近一周",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近一个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "最近三个月",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            }
          }
        ]
      },
      //试题数量
      questionNum: {
        // 总共试题数量
        totalbank: 0 ,
        pass: 0,
        nopass:0,
        waitpass: 0,
        create: 0
      },
      //查询变量
      search: {
        // 审核状态
        check: '',
        // 使用权限
        qusepermise: '',
        // 选中的知识点
        knowledgePoint: [],
        // 选中的题型
        bankType: "",
        // 搜索内容
        searchCon: "",
        // 难易程度 1-10
        complexity: '',
        // 时间段
        rangeTime: ""
      },
      //选择多选
      selectItem: { 
        // 选择所有
        selectAll: false,
        isIndeterminate: false,
        quesIdList: []
      },
      // 侧边栏按钮显示影藏
      sideshowhide: false,
      //试题
      questionLists: [],
    };
  },
  mounted() {
    this.showhideSideBar();
    this.getBankNums();
    this.requestSections();
    this.requestBankAll();
  },
  methods: {
    //章节数据
    requestSections () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'service',
        controller: 'Course_Sections_Con',
        action: 'queSections',
        courseid: this.courseid
      }).then(res => {
        let resData = res.data;
        if (resData.status === 0) {
          this.knowledgePointList = resData.data;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '知识点数据获取失败！'});
        }
      }).catch(err => {
        this.$message({type: 'error', message: '请求知识点数据失败！'});
      });
    },
    // 试题查询
    requestBankAll () {
      let params = {
        module: 'exam',
        controller: 'Question',
        action: 'questionList',
        course: this.courseid,
        classroom: '',
        section: this.search.knowledgePoint[0] ? this.search.knowledgePoint[0] : '',
        sections: this.search.knowledgePoint[1] ? this.search.knowledgePoint[1] : '',
        kwords: this.search.searchCon,
        qtype: this.search.bankType,
        check: this.search.check,
        diff: this.search.complexity,
        usepermise: this.search.qusepermise,
        page: this.page.currentPage,
        rows: this.page.pageSize
      }
      this.$ajax.post(this.Const.apiurl, params).then(response => {
        console.log('成功', response);
        let resData = response.data;
        if (resData.status == 0) {
          this.questionLists = resData.data.data;
          this.page.currentPage = resData.data.page.current_page
          this.page.total = resData.data.page.total
        } else {
          this.$message({type: 'info', message: resData.data || '请求试题列表失败！'});
        }
      }).catch(err => {
        this.$message({type: 'error', message: '请求试题列表失败！'+err.message});
      });
    },
    // 侧边楼梯导航显示隐藏  完成
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
    fnGoTop() {
      $("html, body").animate({ scrollTop: "0" });
    },
    // 每页显示条数改变  完成
    handleSizeChange(val) {
      this.page.pageSize = val;
      this.requestBankAll();
    },
    // 当前页码改变    完成
    handleCurrentChange(val) {
      this.page.currentPage = val;
      this.requestBankAll();
    },
    //跳转添加页面 完成
    fnAddBank () { this.$router.push({ "path": '/addbank' }); },
    // 审核试题
    fnCheckBank () { this.$router.push({ "path": '/checkbank' }); },
    //试题删除 
    fndelete(sid){
      if (sid) {
        this.$confirm('此操作将永久删除该试题, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          let params={
            module: 'exam',
            controller: 'Question',
            action: 'delQuestion',
            id: sid
          }
          this.$ajax.post(this.Const.apiurl, params).then(response => {
            let res = response.data;
            if (res.status == 0) {
              this.$message({type: 'success', message: res.data ? res.data : '删除成功！'});
            } else {
              this.$message({type: 'info', message: res.data ? res.data : '删除失败！'});
            }
          }).catch(err => {
            this.$message({type: 'error', message: '请求错误，请检查本地网络！'});
          });
        }).catch(_ => {
          this.$message({type: 'info', message: '取消删除'});
        });
      }
    },
    // 编辑试题 完成
    fnEdit (questionid) { this.$router.push({"path": '/edit/'+questionid,}); },
    // 查看审核原因   TODO
    fnReadCheckMsg (id) {
      this.$message({type: 'error', message: '审核原因'+id});
    },
    // 转换题型
    tranformL (type, list) {
      let typeArr = list.filter(item => (item.value == type) ? true : false);
      return typeArr[0] ? typeArr[0].name : '';
    },
    addquesList(){   //取出全部id
      for( quesid in questionLists){
        console.log(index+":"+item);
      }
    },
    handleCheckAllChange(val) {   //全选
      this.quesIdList = val ? questionLists :[];
      console.log(val);
      console.log(quesIdList);
      this.selectItem.isIndeterminate = true;
    },
    handleCheckedCitiesChange(value) {   // 半多选
      let checkedCount = value.length;
      this.selectItem.selectAll = checkedCount === this.questionLists.length;
      this.selectItem.isIndeterminate = checkedCount > 0 && checkedCount < this.questionLists.length;
    },
    // 请求试题数量数据
    getBankNums () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'exam',
        controller: 'Question',
        action: 'getQuestionNum',
        id: this.Const.courseData.query.d
      }).then(res => {
        let resData = res.data;
        console.log('试题数量', res);
        if (resData.status == 0) {
          this.questionNum.totalbank = resData.data ? resData.data.number : 0;
          this.questionNum.pass = resData.data['1'] ? resData.data['1'].number : 0;
          this.questionNum.nopass = resData.data['2'] ? resData.data['2'].number : 0;
          this.questionNum.waitpass = resData.data['3'] ? resData.data['3'].number : 0;
          this.questionNum.create = resData.data['4'] ? resData.data['4'].number : 0;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取试题数量失败！'});
        }
      }).catch(err => {});
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
    .answer-con-option{display: inline-block;}
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
<!-- 单选题 -->
          <!-- <div class="bank-item clearfix">
            <div class="title clearfix">
              <div class="bank-stem clearfix fl">
                <el-checkbox class="fl"></el-checkbox>
                <p class="timu fl">
                  <span class="num-order">
                    <i>1</i>、</span>
                  <span class="bank-type">单选题</span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的
                </p>
              </div>
              <div class="opr fr">
                <el-button-group>
                  <el-button type="primary" @click="fnEdit('1231231231')" icon="el-icon-edit" size="mini"></el-button>
                  <el-button type="primary" icon="el-icon-star-off" size="mini"></el-button>
                  <el-button type="danger" icon="el-icon-delete" size="mini"></el-button>
                </el-button-group>
              </div>
            </div>
            <div class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item,index) in 3" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <div class="answer-box">
              <ul class="clearfix">
                <li>
                  <p>
                    <i>A</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li class="cankao-answer">
                  <p>
                    <i>B</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>C</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>D</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>E</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
              </ul>
            </div>
            <div class="note fr">
              <p>审核状态：
                <span v-if="true" class="success">通过</span>
                <span v-else @click="fnReadCheckMsg('1231231231')" class="error">未通过</span> | 标签：
                <span>通用</span> | 试题编号：
                <span>14317154</span> | 默认分值：
                <span>2</span> | 难度：
                <span>4</span> | 添加日期：
                <span>2017年03月</span> | 作者：
                <span>李晓华</span>
              </p>
            </div>
          </div> -->
          <!-- 多选题/不定项选择题 -->
          <!-- <div class="bank-item clearfix">
            <div class="title clearfix">
              <div class="bank-stem clearfix fl">
                <el-checkbox class="fl"></el-checkbox>
                <p class="timu fl">
                  <span class="num-order">
                    <i>1</i>、</span>
                  <span class="bank-type">多选题</span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的
                </p>
              </div>
              <div class="opr fr">
                <el-button-group>
                  <el-button type="primary" icon="el-icon-edit" size="mini"></el-button>
                  <el-button type="primary" icon="el-icon-star-off" size="mini"></el-button>
                  <el-button type="danger" icon="el-icon-delete" size="mini"></el-button>
                </el-button-group>
              </div>
            </div>
            <div class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item,index) in 1" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <div class="answer-box">
              <ul class="clearfix">
                <li class="cankao-answer">
                  <p>
                    <i>A</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <p class="answer-con-option">答案答案答案答案答案答案答案答案答案答案答案</p>
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="" height="100">
                  </div>
                </li>
                <li>
                  <p>
                    <i>B</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li class="cankao-answer">
                  <p>
                    <i>C</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <p class="answer-con-option">答案答案答案答案答案答案答案答案答案答案答案</p>
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="" height="100">
                  </div>
                </li>
                <li>
                  <p>
                    <i>D</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <p class="answer-con-option">答案答案答案答案答案答案答案答案答案答案答案</p>
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="" height="100">
                  </div>
                </li>
                <li>
                  <p>
                    <i>E</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <p class="answer-con-option">答案答案答案答案答案答案答案答案答案答案答案</p>
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="" height="100">
                  </div>
                </li>
              </ul>
            </div>
            <div class="note fr">
              <p>
                审核状态：
                <span v-if="false" class="success">通过</span>
                <span v-else @click="fnReadCheckMsg('1231231231')" class="error">未通过</span> | 标签：
                <span>通用</span> | 试题编号：
                <span>14317154</span> | 默认分值：
                <span>2</span> | 难度：
                <span>4</span> | 添加日期：
                <span>2017年03月</span> | 作者：
                <span>李晓华</span>
              </p>
            </div>
          </div> -->
          <!-- 问答题 -->
          <!-- <div class="bank-item clearfix">
            <div class="title clearfix">
              <div class="bank-stem clearfix fl">
                <el-checkbox class="fl"></el-checkbox>
                <p class="timu fl">
                  <span class="num-order">
                    <i>1</i>、</span>
                  <span class="bank-type">问答题</span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的
                </p>
              </div>
              <div class="opr fr">
                <el-button-group>
                  <el-button type="primary" icon="el-icon-edit" size="mini"></el-button>
                  <el-button type="primary" icon="el-icon-star-off" size="mini"></el-button>
                  <el-button type="danger" icon="el-icon-delete" size="mini"></el-button>
                </el-button-group>
              </div>
            </div>
            <div class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item,index) in 1" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <div class="answer-box">
              <div class="cankaodaan">
                <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                <span class="answer-con-option">宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的</span>
                <p><img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="" height="100"></p>
              </div>
            </div>
            <div class="note fr">
              <p>标签：
                <span>通用</span> | 试题编号：
                <span>14317154</span> | 默认分值：
                <span>2</span> | 难度：
                <span>4</span> | 添加日期：
                <span>2017年03月</span> | 作者：
                <span>李晓华</span>
              </p>
            </div>
          </div> -->
          <!-- 填空题 -->
          <!-- <div class="bank-item clearfix">
            <div class="title clearfix">
              <div class="bank-stem clearfix fl">
                <el-checkbox class="fl"></el-checkbox>
                <p class="timu fl">
                  <span class="num-order">
                    <i>4</i>、</span>
                  <span class="bank-type">填空题</span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>宋画强调写生，（）常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中（）的宋画强调写生，常运用谢赫六法中的宋画强调写（）生，常运用谢赫六法中的宋画强调写生，常运用谢赫（）六法中的
                </p>
              </div>
              <div class="opr fr">
                <el-button-group>
                  <el-button type="primary" icon="el-icon-edit" size="mini"></el-button>
                  <el-button type="primary" icon="el-icon-star-off" size="mini"></el-button>
                  <el-button type="danger" icon="el-icon-delete" size="mini"></el-button>
                </el-button-group>
              </div>
            </div>
            <div class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item,index) in 1" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <div class="answer-box">
              <ul class="clearfix">
                <li>
                  <p>
                    <i>A</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>B</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>C</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>D</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
                <li>
                  <p>
                    <i>E</i>&nbsp;:&nbsp;</p>
                  <div class="answer-item">
                    <span class="answer-con-option">答案</span>
                  </div>
                </li>
              </ul>
              <div class="cankaodaan">
                <span class="can-title">参考答案:&nbsp;&nbsp;</span>
                <span class="answer-con-option">B&nbsp;|&nbsp;F&nbsp;|&nbsp;C&nbsp;|&nbsp;D</span>
              </div>
            </div>
            <div class="note fr">
              <p>标签：
                <span>通用</span> | 试题编号：
                <span>14317154</span> | 默认分值：
                <span>2</span> | 难度：
                <span>4</span> | 添加日期：
                <span>2017年03月</span> | 作者：
                <span>李晓华</span>
              </p>
            </div>
          </div> -->
          <!-- 判断题 -->
          <!-- <div class="bank-item clearfix">
            <div class="title clearfix">
              <div class="bank-stem clearfix fl">
                <el-checkbox class="fl"></el-checkbox>
                <p class="timu fl">
                  <span class="num-order">
                    <i>1</i>、</span>
                  <span class="bank-type">判断题</span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span>宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的宋画强调写生，常运用谢赫六法中的
                </p>
              </div>
              <div class="opr fr">
                <el-button-group>
                  <el-button type="primary" icon="el-icon-edit" size="mini"></el-button>
                  <el-button type="primary" icon="el-icon-star-off" size="mini"></el-button>
                  <el-button type="danger" icon="el-icon-delete" size="mini"></el-button>
                </el-button-group>
              </div>
            </div>
            <div class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item,index) in 3" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <div class="answer-box">
              <div class="panduan">
                <p class="cankao-answer">
                  <i>A</i>&nbsp;:&nbsp;
                  <span>是</span>
                </p>
                <p>
                  <i>B</i>&nbsp;:&nbsp;
                  <span>否</span>
                </p>
              </div>
            </div>
            <div class="note fr">
              <p>标签：
                <span>通用</span> | 试题编号：
                <span>14317154</span> | 默认分值：
                <span>2</span> | 难度：
                <span>4</span> | 添加日期：
                <span>2017年03月</span> | 作者：
                <span>李晓华</span>
              </p>
            </div>
          </div> -->
