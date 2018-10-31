<template>
  <div class="questbank" id="checkbank">
    <aside class="aside">
      <p class="title"><i class="el-icon-d-arrow-right"></i> 审核试题</p>
      <div class="total-bank nopass">
        <span>审核未通过题数</span>
        <p><i>{{ questionNum.nopass }}</i>题</p>
      </div>
      <div class="total-bank waitpass">
        <span>待审核题数</span>
        <p><i>{{ questionNum.waitpass }}</i>题</p>
      </div>
      <div class="addbank" @click="$router.go(-1)">
        <i class="el-icon-back"></i>
        <span>返回</span>
      </div>
      <div class="addbank" @click="fnAddBank">
        <i class="el-icon-circle-plus"></i>
        <span>添加试题</span>
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
            <el-option v-for="(item,index) in qusepermissList" :label="item.name" :key="index" :value="item.id"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>知识点: </span>
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
        <el-button class="search fl" type="primary" @click="requestBankAll" >搜索试题</el-button>
      </div>
        <div class="bank-list">
          <div class="optionbar"></div>
          <div class="bank-list-wrap">
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
                    <el-button type="info" @click="checkNoPass(item.id)" icon="el-icon-close" size="mini"></el-button>
                    <el-button type="success"  @click="checkPass(item.id)" icon="el-icon-check" size="mini"></el-button>
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
                  <span v-if="item.check == '2'" class="error">审核未通过</span>
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
        <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="page.currentPage" 
        :page-sizes="[5,10,20,50]" :page-size="page.pageSize" layout="total,sizes,prev,pager,next,jumper" :total="page.total"></el-pagination>
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
        total: 0 
      },
      // 审核状态
      checkList: [
        {
          id: '2',
          name: '审核未通过'
        },{
          id: '3',
          name: '申请审核'
        }
      ],
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
        nopass: 0,
        waitpass: 0
      },
      //查询变量
      search: {
        // 审核状态
        check: '3',
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
          this.$message({type: 'info', message: resData.data ? resData.data : '请求试题列表失败，请检查本地网络！'});
        }
      }).catch(err => {
        this.$message({type: 'error', message: '请求试题列表失败，请检查本地网络！'});
      });
    },
    // 试题审核通过
    checkPass (id) {
      this.$confirm('审核通过的试题不能再做任何修改, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        let params={
          module:'exam',
          controller:'Question',
          action: 'checkQuestion',
          id: id,
          status: '1',
          msg: ''
        }
        this.$ajax.post(this.Const.apiurl, params).then(response => {
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: resData.data ? resData.data : '操作成功！'});
            this.requestBankAll();
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '操作失败！'});
          }
        }).catch(err=>{
          this.$message({type: 'error', message: '请求失败，请检查本地网络！'});
        });
      }).catch(() => { });
    },
    // 试题审核不通过
    checkNoPass (id) {
      this.$prompt('试题审核不通过原因', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputPattern: /\S/,
        inputErrorMessage: '试题审核不通过原因不能为空'
      }).then(({ value }) => {
        let params={
          module:'exam',
          controller:'Question',
          action: 'checkQuestion',
          id: id,
          status: '2',
          msg: value
        }
        this.$ajax.post(this.Const.apiurl, params).then(response => {
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: resData.data ? resData.data : '操作成功！'});
            this.requestBankAll();
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '操作失败！'});
          }
        }).catch(err=>{
          this.$message({type: 'error', message: '请求失败，请检查本地网络！'});
        });
      }).catch(() => {
        this.$message({ type: 'info', message: '取消输入' });
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
    fnGoTop() { $("html, body").animate({ scrollTop: "0" }); },
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
    // 转换题型
    tranformL (type, list) {
      let typeArr = list.filter(item => (item.value == type) ? true : false);
      return typeArr[0] ? typeArr[0].name : '';
    },
    // 请求试题数量数据
    getBankNums () {
      this.$ajax.post(this.Const.apiurl, {
        module: 'exam',
        controller: 'Question',
        action: 'getQuestionNum',
        id: this.Const.courseData.query.d
      }).then(res => {
        console.log('试题数量', res);
        let resData = res.data;
        if (resData.status == 0) {
          this.questionNum.nopass = resData.data['2'] ? resData.data['2'].number : 0;
          this.questionNum.waitpass = resData.data['3'] ? resData.data['3'].number : 0;
        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '请求试题数量错误！'});
        }
      });
    }
  },
    
      
  computed: {
    
  }
};
</script>