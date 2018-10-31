<template>
  <div class="bankindex questbank">
    <aside class="aside">
      <p class="title">
        <i class="el-icon-d-arrow-right"></i> 基本信息</p>
      <div class="total-bankSecond">
        <span>试卷名</span>
        <p><i>{{ questionNum.papername }}</i></p>
      </div>
      <div class="total-bankSecond time">
        <span>考试时间</span>
        <p><i>{{ questionNum.startTime }}</i></p>
      </div>
      <div class="total-bankSecond time">
        <span>结束时间</span>
        <p><i>{{ questionNum.endTime }}</i></p>
      </div>
      <div class="total-bank">
        <span>可操作总题数</span>
        <p><i>{{ questionNum.totalbank }}</i>题</p>
      </div>    
      <div class="total-bank">
        <span>当前试卷总分</span>
        <p><i>{{ questionNum.totalScore }}</i></p>
      </div>
      <div class="addbank" @click="dialogTableVisible = true" >
        <i class="el-icon-tickets"></i>
        <span>预览</span>
      </div>
      <!-- 预览框详情 -->
      <el-dialog title="已加入试题详情" :visible.sync="dialogTableVisible" width="90%">
        <el-table :data="searchPrevArr" class="table_data" style="cursor:pointer;">         
          <el-table-column type="index" label="序号" width="130"></el-table-column>
          <el-table-column property="qtype" label="题型" width="200" :formatter="tranforQtypeTable"></el-table-column>
          <el-table-column property="name" label="题名" fit="boolean">
            <template slot-scope="scope">
              <el-popover trigger="hover" placement="top">
                <p v-html="scope.row.name">题名:</p>
                <div slot="reference" class="name-wrapper">
                  <el-tag size="medium" v-html="scope.row.name"></el-tag>
                </div>
              </el-popover>
            </template>
          </el-table-column>
          <el-table-column property="grade" label="分数" width="200"></el-table-column>
          <el-table-column label="操作" width="100">
            <template slot-scope="scope">
              <i class="el-icon-delete cursorPoint" @click="recoverList(scope.row)"></i>
            </template>
          </el-table-column>          
        </el-table>         
        <div slot="footer" class="dialog-footer">
          <el-radio-group @change="showPartType" v-model="radioType" style="float:left">
            <!-- 预览题型标签循环 -->
            <el-radio label="all" border>全部</el-radio>
            <el-radio :label="item.value" border v-for="(item, index) in addBankType" :key="index"><span v-text="item.name"></span></el-radio>
          </el-radio-group>
            <el-button @click="dialogTableVisible = false">取 消</el-button>
            <el-button type="primary" @click="submitAddQues">提交</el-button>
        </div>
      </el-dialog>
      <div class="addbank sub" @click="submitAddQues">
        <i class="el-icon-check"></i>
        <span>提交</span>
      </div>
    </aside>
    <section class="wrap">
      <!-- 用户选择题型、个数、分数后显示数据列表 -->
      <template>
        <el-table :data="statusChoose" :highlight-current-row="true" style="width: 100%; cursor: pointer;" @current-change="openDetailsChange" >
          <el-table-column type="index" label="序号" width="150"></el-table-column>
          <el-table-column prop="qtype" label="题型" width="180" :formatter="tranforQtypeTable"></el-table-column>
          <el-table-column prop="number" :formatter="formatterQuesNum" label="题目个数" width="200"></el-table-column>
          <el-table-column prop="grade" label="单个题分数"></el-table-column>
          <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag type="danger" size="medium" v-if="scope.row.status == 0">未选题</el-tag>
            <el-tag type="info" size="medium" v-if="scope.row.status == 1">选题中</el-tag>
            
          </template>
        </el-table-column>
      </el-table>
      </template>
      <!-- 第一级框 -->
      <div class="head-condition clearfix">       
        <el-button class="search fl" type="primary" @click="dialogFormVisible = true">添加试题</el-button>
        <el-dialog title="试题添加" :visible.sync="dialogFormVisible" width="450px">
        <el-form :model="userChooseForm" label-width="100px" status-icon :rules="rules" ref="userChooseForm">
          <el-form-item label="题型：" prop="type">
            <el-select v-model="userChooseForm.type" placeholder="请选择要添加的题型">
              <el-option :label="item.name" :value="item.value" v-for="(item, index) in bankTypeList" :key="index"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="题型个数：" prop="number">
            <el-input style="width:205px" v-model.number="userChooseForm.number"></el-input>
          </el-form-item>
          <el-form-item label="题型分数：" prop="grade">
            <el-input style="width:205px"  v-model.number="userChooseForm.grade"></el-input>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="customByUserMethod('userChooseForm')">确 定</el-button>
        </div>
      </el-dialog>
      </div>
      <!-- 精确搜索框 -->
      <div class="head-condition clearfix">
        <!-- <p class="item-condition">
          <span>审核状态: </span>
          <el-select v-model="search.check" placeholder="请选择">
            <el-option v-for="(item, index) in checkList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p> -->
        <p class="item-condition">
          <span>使用权限: </span>
          <el-select v-model="search.qusepermise" placeholder="请选择">
            <el-option v-for="(item,index) in qusepermiseList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>章节: </span>
          <el-cascader
            expand-trigger="hover"
            :options="options"
            v-model="search.selectedOptions">
          </el-cascader>
        </p>
        <p class="item-condition">
          <span>难易程度: </span>
          <el-select v-model="search.complexity" placeholder="请选择">
            <el-option v-for="(item,index) in complexityList" :label="item.name" :key="index" :value="item.value"></el-option>
          </el-select>
        </p>
        <p class="item-condition">
          <span>出题时间: </span>
          <el-date-picker v-model="search.rangeTime" type="daterange" align="right" unlink-panels range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" :picker-options="pickerTimeOptions">
          </el-date-picker>
        </p>
        <p class="item-condition">
          <span>内容搜索: </span>
          <el-input placeholder="输入内容进行搜索" v-model="search.searchCon">
            <!-- <el-button slot="append" icon="el-icon-search"></el-button> -->
          </el-input>
        </p>
        <el-button class="search fl" type="primary" @click="getQuestionData">搜索试题</el-button>
      </div>
      <div class="bank-list">
        <div class="bank-list-wrap">
          <!-- 循环试题列表 -->
          <div v-for="(item, index) in searchQuestionList" :key="index" class="bank-item clearfix">
            <!-- 题干 -->
            <div class="title clearfix">
              <div class="xxxx">
                <div class="timu fl">
                  <span class="num-order">
                    <i>{{ index + 1 }}</i>、</span>
                  <span class="bank-type" v-text="tranforAll(item.qtype, bankTypeList)"></span>
                  <span>&nbsp;&nbsp;|&nbsp;&nbsp; </span><div v-html="item.questiontext"></div></div>
              </div>
              <!-- 按钮+号 -->
              <div class="opr fr" v-if="userChooseForm.isadd">
                 <el-button type="primary" icon="el-icon-plus" circle @click="pushArr(item, index)"></el-button>
              </div>
            </div>
            <!-- 题干的图片区 -->
            <div v-if="item.imgs && item.imgs.length > 0" class="pic-box">
              <div class="pic">
                <el-carousel height="150px">
                  <el-carousel-item v-for="(item, index) in 3" :key="index">
                    <img src="https://ss0.bdstatic.com/k4oZeXSm1A5BphGlnYG/newmusic/english.png" alt="">
                  </el-carousel-item>
                </el-carousel>
              </div>
              <div class="annotation">
                <p>图片注释</p>
              </div>
            </div>
            <!-- 答案区域 -->
            <div v-if="item.qtype === 'singlechoice' || item.qtype === 'multiplechoice'" class="answer-b+ox">
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
            <!-- 题目信息区 -->
            <div class="note fr">
              <p>审核状态：
                <span v-if="item.check === '1'" class="success">可用</span>
                <span v-if="item.check === '2'" @click="fnReadCheckMsg(item.id)" class="error">审核未通过</span>
                <span v-if="item.check === '3'" class="danger">申请审核</span>
                <span v-if="item.check === '4'" class="success">创建完成</span>
                <span v-if="item.check === '5'" class="success">调整完成</span> | 课题：
                <span>{{ item.section }}/{{ item.sections }}</span> | 标签：
                <span v-text="tranforAll(item.usepermise, qusepermiseList)"></span> | 难度：
                <span v-text="tranforAll(item.difficulty, complexityList)"></span> | 添加日期：
                <span>{{ item.createtime }}</span> | 作者：
                <span>{{ item.auth }}</span>
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
        <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="page.currentPage" :page-sizes="[5,10,20,50]" :page-size="page.pageSize" layout="total,sizes,prev,pager,next,jumper" :total="page.total"></el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    let thisVue = this;
    return {
      courseid: thisVue.$route.query.id,
      // 分页数据
      page: {
        currentPage: 1,
        pageSize: 5,
        total: 0
      },
      //用户添加题型数据相关内容后存入此数组中
      statusChoose:[],
      //全选按钮是否禁用
      weather : false,
      //弹出框序号计数
      num: 1,
      // 审核状态
      checkList: this.Const.checkList,
      // 使用权限
      qusepermiseList: this.Const.qusepermissList,
      // 知识点列表
      knowledgePointList: [
        {
          value: "1231",
          name: "知识点1"
        },
        {
          value: "123112312",
          name: "知识点2"
        }
      ],
      // 题型
      bankTypeList: this.Const.bankTypeList,
      // 难易程度
      complexityList: this.Const.complexityList,
      //预览弹窗数组 选中题目
      previewArr: [],
      //选题类型、分数、个数、数组
      customByUser: [],
      // 试题列表的过滤筛选
      searchPrevArr: [],
      //预览框的类型
      radioType: 'all',
      //增加这句可关闭弹窗
      dialogTableVisible: false,
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
      questionNum: {
        // 总共试题数量
        totalbank: 200,
        pass: 100,
        nopass: 50,
        waitpass: 50,
        papername:'',
        startTime:'',
        endTime:'',
        totalScore:0
      },
      //精确搜索具体信息
      search: {
        // 审核状态
        // check: '1',
        // 使用权限
        qusepermise: '0',
        // 选中的知识点
        knowledgePoint: "",
        // 选中的题型
        bankType: "",
        // 搜索内容
        searchCon: "",
        // 难易程度 ''-3
        complexity: '',
        // 时间段
        rangeTime: "",
        //章节
        selectedOptions:[],
      },
      selectItem: {
        // 选择所有
        selectAll: false,
        isIndeterminate: true,
        quesIdList: []
      },
      //章节下拉
      options: [{
          value: '章id',
          label: '第一章',
          children: [{
            value: '节id1',
            label: '第一节',
          },{
            value: '节id2',
            label: '第二节',
          }],
        }],
        selectedOptions: [],
      //表单数据对话框数据
      dialogFormVisible: false,
      userChooseForm: {
        number: '',
        type: '',
        grade: '',
        isadd: false
      },
      //表格宽度
      formLabelWidth: '80px',
      //用户自定义题型、分数、个数表单验证
       rules: {
          grade: [
            { required: true, message: '请输入分数', trigger: 'blur' },
            { type: "number", message: '只能为数字' },
          ],
          number: [
            { required: true, message: '请输入个数', trigger: 'blur' },
            { type: "integer", message: '只能为整数数字'},
          ],
          type: [
            { required: true, message: '请至少选择一个题目类型', trigger: 'change' }
          ],
        },
      // 添加题目的题型
      addBankType: [],
      // 侧边栏按钮显示影藏
      sideshowhide: false,
      // 后台返回的所有题目
      searchQuestionList: []
    };
  },
  mounted() {
    this.getQuestionData();
    this.showhideSideBar();
    this.getQuizDetail();
    this.getQuestionNum();
  },
  methods: {

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
      console.log(val);
    },
    // 当前页码改变
    handleCurrentChange(val) {
      console.log(val);
    },
    fnAddBank () {
      this.$router.push({ "path": '/addbank' });
    },
    // 编辑试题
    fnEdit (questionid) {
      this.$router.push({ "path": '/edit/' + questionid, });
    },
    //添加用户定义的加题类型、个数、分数 方法
    customByUserMethod (userChooseForm) {
      this.$refs[userChooseForm].validate((valid) => {
          if (valid) {
            let isHaveAr = this.statusChoose.filter(item => (item.qtype == this.userChooseForm.type && item.grade == this.userChooseForm.grade) ? true : false);
            if (isHaveAr.length) {
              for (let i = 0, objitem; objitem = this.statusChoose[i]; i++) {
                if (objitem['qtype'] == this.userChooseForm.type) {
                  objitem.number += this.userChooseForm.number;
                }
              }
            } else {
              this.statusChoose.push({
                qtype: this.userChooseForm.type,
                number: this.userChooseForm.number,
                grade: this.userChooseForm.grade,
                status: 0
              });
            }
            this.search.bankType = this.userChooseForm.type;
            this.dialogFormVisible = false;
            this.userChooseForm.isadd = true;
            this.getQuestionData();
          } else {
            return false;
          }
        });
    },
    // 每一行表格被单击后筛选列表中为具体类型的数据
    openDetailsChange (currentRow, oldCurrentRow) {
      let currentQues = this.previewArr.filter(item => (item.qtype == currentRow.qtype && item.grade == currentRow.grade) ? true : false);
      if (currentQues.length == currentRow.number) {
        
      } else {
        this.userChooseForm = {
          number: currentRow.number,
          type: currentRow.qtype,
          grade: currentRow.grade,
          isadd: true
        };
        this.search.bankType = currentRow.qtype;
        this.getQuestionData();
      }
    },
    // 查看审核原因
    fnReadCheckMsg (id) {
      this.$message({type: 'error', message: '审核原因'});
    },
    // 转换题型
    tranforAll (obj, list) {
      let typeArr = list.filter(item => (item.value == obj) ? true : false);
      return typeArr[0].name;
    },
    tranforQtypeTable (row, column, cellValue, index) {
      let typeArr = this.bankTypeList.filter(item => (item.value == cellValue) ? true : false);
      return typeArr[0].name;
    },
    // 格式化    待选题目个数/选中题目个数
    formatterQuesNum (row, column, cellValue, index) {
      let currentQues = this.previewArr.filter(item => (item.qtype == row.qtype && item.grade == row.grade) ? true : false);
      return `${currentQues.length} / ${row.number}`;
    },
    //对话框点击删除后，向试题列表中恢复元素
    recoverList (scoperow) {
      this.previewArr = this.previewArr.filter(item => {
        return (scoperow.id == item.id) ? false : true;
      });
      //点击删除后减少总分
      this.questionNum.totalScore = this.questionNum.totalScore - scoperow.grade;
      //更新预览框数据
      this.showPartType(this.radioType);
      //删除数据返回到试题列表中
      this.searchQuestionList.push(scoperow);
      // 判断移除题型按钮
      let isHave = false;
      for (let i = 0; i < this.searchPrevArr.length; i++) {
        if (this.searchPrevArr[i].qtype === scoperow.qtype) {
          isHave = true;
        }
      }
      if (!isHave) {
        this.addBankType = this.addBankType.filter(item => {
          return (item.value === scoperow.qtype) ? false : true;
        });
      }
    },
    //根据预览框选择类型显示
    showPartType(type) {
      if (type === 'all') {
          this.searchPrevArr = this.previewArr;
        } else {
          this.searchPrevArr = this.previewArr.filter(item => (type === item.qtype) ? true : false);
      }
    },
    //向试卷中添加试题的主方法，包括计算分数等
    pushArr (item, index) {
      let This = this;
      // 判断当前需要添加的题型是否已经加满 
      let currentQues = this.previewArr.filter(itemobj => (this.userChooseForm.type === itemobj.qtype) ? true : false);
      if (currentQues.length == this.userChooseForm.number) {
        // 题型已加满
        this.$message({type: 'info', message: '当前题型已经添加完成，如还需添加题目，请继续选择添加试题！'});
        // 初始化
        this.userChooseForm.isadd = false;
      } else {
        for (let i = 0, itemobj; itemobj = this.previewArr[i]; i++) {
          if (itemobj.id == item.id) {
            this.$message({type: 'info', message: '该题目已经添加了'});
            return ;
          }
        }
        item.grade = this.userChooseForm.grade;
        this.previewArr.push(item);  
      }
      //计算选题总分 
      let totalSum = 0;
      for (let i = 0; i < this.previewArr.length; i++) {
        totalSum += this.previewArr[i]['grade'];
      }
      if (totalSum <= 100) {
        this.questionNum.totalScore = totalSum;
        if (100 - this.questionNum.totalScore <= 10) {
          this.$message({type: 'info', message: '快到100分啦，请合适分配剩下的分数！'});
        }
      } else {
        //移出数组最后一个元素
        this.previewArr.pop();
        this.$message({type: 'error', message: '已经超出100分，请重新选题！'});
      }
      //记录问题
      this.searchPrevArr = this.previewArr;
      //调用删除子试题选项方法
      this.searchQuestionList.splice(index, 1);
      let newArr = this.bankTypeList.filter(obj => {
        let isHave = (function () {
          for (let i = 0; i < This.addBankType.length; i++) {
            if (This.addBankType[i].value === item.qtype) {
              return true;
            }
          }
          return false;
        })();
        return (obj.value === item.qtype && !isHave) ? true : false;
      });
      if (newArr.length) {
        this.addBankType.push(newArr[0]);
      } 
    },
    
    //调用API接口从后台获取试题信息
    getQuestionData() {
      let params = {
        module: 'exam',
        controller: 'Question',
        action: 'classroomList',
        course: '',
        classroom: this.Const.courseData.query.d,
        section: (this.search.selectedOptions[1]) ? this.search.selectedOptions[0] : '',
        sections: (this.search.selectedOptions[1]) ? this.search.selectedOptions[1] : '',
        qtype: this.search.bankType,
        kwords: this.search.searchCon,
        check: this.search.check,
        diff: this.search.complexity,
        usepermise: this.search.qusepermise,
        page: this.page.currentPage,
        rows: this.page.pageSize,
      };
      this.$ajax.post(this.Const.apiUrl, params).then(res => {
        let resData = res.data;
        if (resData.status == 0) {
          this.searchQuestionList = resData.data.data;
          this.page.currentPage = resData.data.page['current_page'];
          this.page.total = resData.data.page['total'];

        } else {
          this.$message({type: 'info', message: resData.data ? resData.data : '获取数据失败！'});
        }
      }).catch(err => {
        this.$message({type: 'error', message: '查询失败，请检查本地网络！'});
      });
    },
    //获取试卷详情接口
    getQuizDetail () {
      let params = {
        module: 'exam',
        controller: 'Quiz',
        action: 'quizDetail',
        id: this.$route.query.id,
      };
      this.$ajax.post(this.Const.apiUrl, params).then(res => {
          let resData = res.data;
          if (resData.status == 0) {
            this.questionNum.papername = resData.data.name;
            this.questionNum.startTime = resData.data.timeopen;
            this.questionNum.endTime = resData.data.timeclose;
          } else {
            this.$message({type: 'error', message: '请求API接口失败！'});
          }
      }).catch(error => {
        this.$message({type: 'error', message: '查询失败，请检查本地网络！'});
      });
    },
    // 提交添加的试题
    submitAddQues () {
      this.dialogTableVisible = false;
      console.log('previewArr:', this.previewArr);
      let typeAndGradeArr = [];
      this.searchPrevArr = this.previewArr.filter(item => (type === item.qtype) ? true : false);
      // for (let i = 0, item; item = this.previewArr[i]; i++) {
      //   if (typeAndGradeArr.length) {
      //     for (let j = 0, jtem; jtem = typeAndGradeArr[j]; j++) {
      //       if (jtem.qtype == item.qtype && jtem.score == item.grade && !jtem.question.join().includes(item.id)) {
      //         jtem.question.push(item.id);
      //         jtem.qtype = item.qtype;
      //         jtem.score = item.grade;
      //         //typeAndGradeArr.push(jtem);
      //       } else if (jtem.qtype != item.qtype || jtem.score != item.grade) {
      //         typeAndGradeArr.push({
      //           qtype: item.qtype,
      //           score: item.grade,
      //           question: new Array(item.id)
      //         });
      //       }
      //     }
      //   } else {
      //     typeAndGradeArr.push({
      //       qtype: item.qtype,
      //       score: item.grade,
      //       question: new Array(item.id)
      //     });
      //   }
      // }
      
      
      let params = {
        module: 'exam',
        controller: 'Quiz',
        action: 'handAddQuestion',
        quiz: this.$route.query.id,
        questions: typeAndGradeArr
      };
      console.log('typeAndGradeArr:', typeAndGradeArr);
      let arroo = this.removeDuplicatedItem(typeAndGradeArr);
      console.log('typeAndGradeArr去重后:', arroo);
      
      //   this.$ajax.post(this.Const.apiUrl, params).then(res => {
      //     let resData = res.data;
      //     if (resData.status == 0) {
      //       this.$message({type: 'success', message: resData.data ? '提交成功！' : '提交失败！'});
      //     } else {
      //       this.$message({type: 'error', message: '提交失败！'});
      //     }
      // }).catch(error => {
      //   this.$message({type: 'error', message: '查询失败，请检查本地网络！'});
      // });
    },
    //二维数组去重
    removeDuplicatedItem(arr) {
      let arrNew = [];
      for (let i = 0; i < arr.length; i++) {
        for (let j = 0; j < arr.length; j++) {
          for (let k = 0; k < arr[j]['question'].length; k++) {
            if (arr[i]['qtype'] == arr[j]['qtype'] && arr[i]['score'] == arr[j]['score']) {

            }
          }
        }
      }
    },
    //获取试题数量
    getQuestionNum () {
      let params = {
        module: 'exam',
        controller: 'Question',
        action: 'getQuestionNum',
        id: this.Const.courseData.query.d
      };
      this.$ajax.post(this.Const.apiUrl, params).then(res => {
          let resData = res.data;
          if (resData.status == 0) {
            this.questionNum.totalbank = resData.data.number;
          } else {
            this.$message({type: 'error', message: resData.data || '请求API接口失败！'});
          }
      }).catch(error => {
        this.$message({type: 'error', message: '查询失败，请检查本地网络！'});
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
    width: 195px;
    background: #fff;
    float: left;
    margin-right: 5px;
    .title {
      @include middelH(50px);
      @include setTitle;
    }
    .total-bank {
      height: 80px;
      background: #ff8a00;
      padding-left: 30px;
      color: #fff;
      span {
        @include middelH(40px);
      }
      i {
        font-size: 2.2rem;
        margin-right: 10px;
      }
    }
    .total-bankSecond {
      height: 80px;
      background:#f56c6c;
      padding-left: 30px;
      color: #fff;
      span {
        @include middelH(40px);
      }
      i {
        font-size: 2.2rem;
        margin-right: 10px;
      }
    }
    .time {
      i {
        font-size: 1.4rem;
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
    .sub {
      color: #ffffff;
      background-color:darkgreen;
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
.cursorPoint {
  cursor: pointer;
  font-size: 18px;
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
