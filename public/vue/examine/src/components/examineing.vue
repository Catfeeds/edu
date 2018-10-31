<template>
  <div id='examining' @oncontextmenu="fl()">
    <div class="hm" :style="{display:ishm}">
      <div class="hm-tk" @click="hh" :style="{top:istop+'%'}">我保证题目作答由个人独立完成，不在做题过程中获取来自网络、书籍、他人的帮助<br><button @click="allow">我同意</button></div>
    </div>
    <aside class="side-nav" :style="{left:isleft?0+'px':-170 + 'px',display:isdisplay}">
      <div>
        <img src="/static/heading.png">
      </div>
      <ul>
        <li @click="fnSwitvhType(item.tag)" :key="index"  v-for="(item, index) in examinPaper">{{item.tag}}{{ item.name+'('+(item.list.length-examinIndexs[item.tag].length)+'/'+item.list.length+')' }}</li>
      </ul>
    </aside>
    <div class="bk" @click="isshow" :style="{left:isleft?200+'px':30 + 'px'}">{{bkb}}</div>
    <section class="content">
      <div class="top">
        <div id="examtime" @click="time" :style="{top:top+'px'}"><span>11:50:50</span></div>
        <div id="examsubject">C语言</div>
        
      </div>
      <div class="examine-wrap">
        <div class="question">
          <div class="tigan"><strong style="padding-right:20px;border-right:1px solid white;">题目:</strong><strong style="margin-left:20px">6分</strong><br>{{currentQuesBank[currentQuesIndex].title}}<span>11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111</span></div>
          <template v-if="currentQuesType=='danxuan'">
            <el-radio-group  v-model="currentQuesBank[currentQuesIndex].value">
              <el-col>
              <el-radio-button class="radio" :key="i" v-for="(e,i) in currentQuesBank[currentQuesIndex].options" :label="e.id"><div @click="find(currentQuesType,currentQuesIndex+1,i)"><span class="badge">{{listabcd[i]}}</span>{{e.content}}</div></el-radio-button>
              </el-col>
            </el-radio-group>
             <div><span>已选择选项:<ul :key="index" v-for="(obj,index) in questionAns"><li><strong style="font-size:45px;color:red;text-shadow:4px 4px 2px black;">{{listabcd[obj[currentQuesIndex+1]]  }}</strong></li></ul></span></div>
          </template>

          <template v-if="currentQuesType=='duoxuan'">
            <el-checkbox-group v-model="currentQuesBank[currentQuesIndex].value">
              <el-checkbox-button :key="i" v-for="(e,i) in currentQuesBank[currentQuesIndex].options" :label="e.id"><div @click="find(currentQuesType,currentQuesIndex+1,i)"><span class="badge">{{listabcd[i]}}</span>{{e.content}}</div></el-checkbox-button>
            </el-checkbox-group>
             <div v-if="questionAns[currentQuesType]"><span>已选择选项:<ul :key="index" v-for="(obj,index) in questionAns[currentQuesType][currentQuesIndex+1]"><li style="float:left;"><strong style="font-size:45px;color:red;text-shadow:4px 4px 2px black;">{{listabcd[obj]}}</strong></li></ul></span></div>
          </template>


           <template v-if="currentQuesType=='jianda'">
             <el-input type="textarea" :rows="15" placeholder="请输入内容" v-model="currentQuesBank[currentQuesIndex].value"></el-input>
          </template>
          <br>
          <div class="kongbai"></div>
          <div id="nextques">
            <el-button class="zzz" type="primary"  @click="nextques()">{{ button2 }}</el-button>
          </div>
        </div>
        <div class="question-num">
          <ul>
            <li @click="quesbyindex(i)" :class="{'grey':currentQuesBank[i].submitted,'select':currentQuesIndex==i}" :key="i" v-for="(e,i) in currentQuesBank" >{{i+1}}</li>
          </ul>
          <el-button type="success">提交试卷</el-button>
        </div>
      </div>
      <div class="footer"><p><strong>备注:</strong><span>123132132</span></p></div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      top: 100,
      bkb:'<',
      istop:0,
      ishm:'block',
      isdisplay:'block',
      questionAns:null,
      isleft: true,
      top_1:1,
      button2: '下一题',
      // 当前在答的题型
      currentQuesType: "danxuan",
      //当前题目序号
      currentQuesIndex: "0",
      //当前题目类型题库
      examinIndexs:[],
      currentQuesBank: [],
      listabcd:['A','B','C','D','E'],
      // 试卷题目列表
      examinPaper: [
        {
          name: "单选题",
          tag: "danxuan",
          count:'',
          list: [
            {
              id: "a1",
              title: "一个C程序是从_",
              submitted:false,
              ismodify:true,
              value: [],
              options: [
                {
                  id: "1",
                  content: "本程序的main函数开始,到main函数结束"
                },
                {
                  id: "2",
                  content: "本程序的main函数开始,到本程序的最后一个函数结束"
                },
                {
                  id: "3",
                  content: "本程序文件的第一个函数开始,到本程序文件的最后一个函数结束"
                },
                {
                  id: "4",
                  content: "本程序文件的第一个函数开始,到本程序的main函数结束"
                }
              ]
            },
            {
              id: "a1",
              title: "C语言程序的基本单位是",
              submitted:false,
              ismodify:false,
              disabled:null,
              value: [],
              options: [
                {
                  id: "1",
                  content: "程序行"
                },
                {
                  id: "2",
                  content: "语句"
                },
                {
                  id: "3",
                  content: "函数"
                },
                {
                  id: "4",
                  content: "字符"
                }
              ]
            }, 
            {
              id: "a1",
              title: "在c语言中,要求运算数必须是整型的运算符是",
              submitted:false,
              ismodify:false,
              disabled:null,
              value: [],
              options: [
                {
                  id: "1",
                  content: "/"
                },
                {
                  id: "2",
                  content: "++"
                },
                {
                  id: "3",
                  content: "*="
                },
                {
                  id: "4",
                  content: "%"
                }
              ]
            }
          ]
        },
        {
          name: "多选题",
          tag: "duoxuan",
          count:'',
          list: [
            {
              id: "dx1",
              title: "题目1------多选题",
              submitted:false,
              ismodify:false,
              disabled:null,
              value: [],
              options: [
                {
                  id: "1",
                  content: "aaaaaaaaaaa"
                },
                {
                  id: "2",
                  content: "选择项2"
                },
                {
                  id: "3",
                  content: "选择项3"
                },
                {
                  id: "4",
                  content: "选择项4"
                }
              ]
            }
          ]
        },
        {
          name: "简答题",
          tag: "jianda",
          count:'',
          list: [
            {
              id: "jd1",
              value: '',
              title: "题目1------简答题",
              submitted:false,
              ismodify:false,
              disabled: null,
              options: [
                {
                  id: "1231",
                  content: "选择项1"
                }
              ]
            },{
              id: "jd3",
              value: '',
              title: "题目1",
              submitted:false,
              ismodify:false,
              disabled: null,
              options: [
                {
                  id: "1231",
                  content: "选择项1"
                }
              ]
            }
          ]
        }
      ]
    };
  },
 //初始化存储 当前试题列表 默认为 选择题 第一题
  created() {
    for(let i = 0; i < this.examinPaper.length; i++) {
      var tag = this.examinPaper[i].tag;
      this.examinIndexs[tag] = new Array();
      for (let k = 0; k < this.examinPaper[i].list.length; k++) {
       this.examinIndexs[tag].push(k);
      }
      this.examinPaper[i].count = this.examinPaper[i].list.length;
    }
    this.currentQuesBank = this.examinPaper[0].list;
    console.log(this.currentQuesBank);
    this.currentQuesIndex = 0;

  },
  methods: {
    fl(){
      return false;
    },
    find(...x){
      let arg = [...x] || ['0','0','0']
      this.questionAns = this.qj(arg[0],arg[1],arg[2])
    },
    qj: (() => {
      let timu = {
        '0':function(i){
          console.log('no')
        },
      'danxuan': function(i){
      let list = []
      list.pop()
      list.push(i)
      return list
      },
      'duoxuan':  (function(){
        let list = [];
        return function(i){
         let c = list.includes(i)?list.splice(list.indexOf(i),1):list.push(i)
        return list
        }
      }
    )()}
      let onlyOne = {}
      let array = []
      return function(obj,att,i){
          if(!onlyOne[obj]){
            onlyOne[obj] = {}
          }
          if(!onlyOne[obj][att]){
            onlyOne[obj][att] = []
          }
          let list = timu[obj](i)
          onlyOne[obj][att] = [...list]
          // onlyOne[obj][att].add(...list)
          // array = Array.from(onlyOne[obj][att]);

          return onlyOne;
           }
    })(),
   allow(){
      this.istop = '0'
      setTimeout(() => {
        this.ishm = 'none';
      }, 500);
    },
    hh(){
      setTimeout(() => {
        this.istop = 35
      }, 500);
    },
    isshow(){
      this.isleft = !this.isleft;
      setTimeout(() => {
              this.bkb = this.isleft? '<':'>'
        // this.isdisplay = this.isleft?'block':'none'
        // console.log(123)
      }, 400);
    },
    // 点击题型列表切换题型
    fnSwitvhType(tag) {
            this.currentQuesType=tag;
      // 切换题型，更新答题区和题目计数区
      this.button2="下一题";
      for(var i=0;i<this.examinPaper.length;i++)
      {
        if(this.examinPaper[i].tag==tag)
        {
          this.currentQuesBank=this.examinPaper[i].list;
          break;
        }
      }
        this.currentQuesIndex=0;
    },
    time(){
      this.top_1 = -1*this.top_1;

      this.top = this.top_1*100
    },
    //通过序号获取当前试题
    quesbyindex(index){
      this.currentQuesIndex=index;
    },
    deleteArrayInindex(index) {
      for(var i=0;i<this.examinIndexs[this.currentQuesType].length;i++)
      {
        if(this.examinIndexs[this.currentQuesType][i]==index)
        {
          this.examinIndexs[this.currentQuesType].splice(i,1);
        }
      }
    },
    sublmitquest(index) {
      this.deleteArrayInindex(index);
        if(this.currentQuesBank[index].ismodify==true)
        {
          /*
          提交代码
          */
          this.currentQuesBank[index].submitted=true;
          this.currentQuesBank[index].disabled=false;
        }else if(this.currentQuesBank[index].submitted==true){
          /*
          提交代码
          */
          this.currentQuesBank[index].disabled=true;
        }else{
           /*
          提交代码
          */
          this.currentQuesBank[index].submitted=true;
          this.currentQuesBank[index].disabled=true;
        }
    },
    nextques() {
      if (!(this.currentQuesBank[this.currentQuesIndex].value=='' || this.currentQuesBank[this.currentQuesIndex].value==[])) {
        this.sublmitquest(this.currentQuesIndex);
      } else {
        var res=confirm("你还没有作答，你确定提交吗？");
        if (res) {
          this.sublmitquest(this.currentQuesIndex);
        }
      }

      if (this.currentQuesBank.length-1 == this.currentQuesIndex) {
          for (var i=0;i<this.examinPaper.length;i++) {
            if (this.currentQuesType==this.examinPaper[i].tag) {
              if (this.examinPaper[i+1]!=null) {
                console.log(this.examinPaper[i+1].tag);
                this.fnSwitvhType(this.examinPaper[i+1].tag);
                break;
              } else { this.button2="结束考试"}
            }
        }
      } else {
        this.currentQuesIndex++;
      }
    }
  }
};
</script>

<style lang="scss">
*{
  font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
}
.hm{
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  z-index: 1000;
  background: rgba($color: #6e6a6a, $alpha: 0.3);
  .hm-tk{
    width: 400px;
    text-align: center;
    color: white;
    padding: 50px;
    height: 200px;
    font-size: 15px;
    border-radius: 15px;
    background: rgb(66, 58, 58);
    margin: auto auto;
    position: relative;
    transition: all 0.5s;
    button{
      font-size: 25px;
      margin-top: 20px;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 1px 1px 5px 2px white;
      border: none;
      background-color: black;
      color: white;
    }
  }
}
#examining {
  min-width: 1200px;
  margin: 0 auto;
  width: 100%;
  height: 100vh;
  .side-nav {
    position: absolute;
    width: 200px;
    height: 100%;
    text-align: center;
    background: #393B6B;
    transition: all 0.5s;
  }
  .side-nav div img {
    margin-top: 30%;
    width: 100px;
    height: 100px;
    border-radius: 50%;
  }
  .bk{
    z-index: 20;
    border-right: 1px solid black;
    border-left: 1px solid transparent;
    border-bottom: 1px solid transparent;
    border-top:1px solid transparent;
    box-shadow:  1px 1px black;
    padding-top: 25%;
    transition: all 0.5s;
    position: absolute;
    height: 100%;
    font-size: 25px;
    cursor: pointer;
    color: white;
    text-align: center;
    left: 200px;
  width: 35px;
  background: #393B6B;
  &:hover{
    box-shadow: 1px 1px 1px white;
  }
}
  .side-nav ul {
    margin-top: 50%;
    li {
      width: 200px;
      font-size: 15px;
      color: white;
      transition: all 0.5s;
      @include middelH(40px);
      background: #001C58;
      cursor: pointer;
      &:hover {
        background: rgba(39, 143, 204, 0.705);
        font-size: 17px;
      }
    }
    li:after{
      content: '<';
      position: relative;
      left: 20px;
      transition: all 0.5s;
      opacity: 0;
    }
    li:hover::after{
      opacity: 1;
      left: 0;
    }
  }
  .content {
    height: 100%;
    min-width: 1000px;
    margin-left: 200px;
    background: #e9e9f1;
    .top {
      background-position-x: 10px;
      background-position-y: 30px;
      background-image: url('../images/main/CHUANDGE.png');
      background-repeat: no-repeat;
      background-color: #fff;
      background-size: 15%;
      height: 120px;
      position: relative;
      left: 30px;
    }
    .examine-wrap {
      margin: 20px 30px;
      box-sizing: border-box;
    }
  }
}
*{
  box-sizing: border-box;
}
.question {
  position: absolute;
  width: 72%;
  background: rgba(255, 255, 255, 1);
  height: 700px;
  padding: 20px 30px;
  box-sizing: border-box;
  .tigan {
    width: 85%;
    padding: 25px;
    word-wrap: break-word;
    line-height: 25px;
    font-size: 20px;
    font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    margin-bottom: 10px;
    margin-left: 10px;
    text-indent: 15px;
    background: white;
    color: black;
    
    margin-top: 50px;
    span{
     width: 105%;
     height: 125%;
     display: inline-block;
     border-top: 2px inset black;
     border-left: 2px inset black;
     border-right: 2px outset black;
     border-bottom: 2px solid black;
     border-radius: 5px;
     padding: 50px;
     
    }
  }
  .badge{
    font-size: 20px;
    padding-right: 15px;
    margin-right: 20px;
    border-right: 1px solid black;
  }
  .badge::before{
    content: '';
    width: 6px;
    height: 6px;
    border-left: 1px solid #666;
    border-bottom: 1px solid #666;
    transform: rotate(225deg);
    position: absolute;
    right: 9px;
    top: 20px;
  }
  .kongbai{
    width: 80%;
    margin-top: 50px;
    margin-left: 10px;
    border-bottom: 0.5px solid rgba($color: #9a9b96, $alpha: 0.8)
  }
  .tigan strong{
    font-size: 35px;
    font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    text-shadow: 1px 2px 2px black;
  }
  .el-checkbox-group, .el-radio-group {
    margin-top: 60px;
    padding-left: 10px;
    width: 80%;
    display: inline-block;
    label{
      margin: 10px 0;
      .el-radio-button__inner, .el-checkbox-button__inner {
        border-left: 1px solid #dcdfe6;
        border-radius: 4px 0 0 4px;
      }
    }
  }
  .time{
    display: block; 
    position: absolute;
    left: 0px;
    z-index: 50;
    height: 100px;
    width: 100px;
    background-color: red;
  }
  .el-radio-button{
    width: 100%;
    height: 100%;
    position: relative;
    transition: all 0.5s;
  }
  .el-radio-button:hover{
    width: 120%;
  }
  .el-textarea {
    margin-top: 30px;
    padding-left: 10px;
    width: 80%;
  }
}
.question-num {
  margin-left:85%;
  height: 700px;
  background: white;
  text-align: center;
  .el-button {
    margin-top: 150%;
    margin: 50% 25%;
    box-shadow: 1px 1px 1px 1px black;
    border-bottom: 1px solid rgb(134, 124, 124);
    border-radius: 5px;
    transition: 0.5s;
  }

  ul {
    padding: 100px 10px 10px 20px;
    width: 200px;
    li {
      padding-top: 15px;
      list-style: none;
      line-height: 30px;
      width: 25%;
      height: 60px;
      float: left;
      clip-path: circle();
      transition: all 0.5s;
      border: 0px solid white;
      display: block;
      text-align: center;
      background: rgb(238, 36, 36);
      color: white;
      cursor: pointer;
      &:hover {
        background: #fff;
        border-bottom: 20px solid black;
        color: #393B6B;
      }
      &.grey{
        background: #F7F7FA;
        color: #9FACFD;
      }
      &.select{
        background: white;
         border-bottom: 20px solid black;
        color:black;
      }
    }
    
  }
}
#examtime {
  line-height: 50px;
  text-align: center;
  position: absolute;
  right:250px;
  z-index: 20;
  cursor: pointer;
  clip-path: circle();
  top: 40px;
  width: 170px;
  font-size: 30px;
  height: 170px;
  transition: all 0.5s;
  color: #393B6B;
  background: linear-gradient(to bottom, #c3d462 0%,#46dbcf 100%);
  &:hover{
    background: linear-gradient(to bottom,rgba($color: #46dbcf, $alpha: 1.0) 0%,#c3d462 100%);
  }
}
#examtime span{
  position: relative;
  transition: all 0.5s;
  top: 60px;
}
#nextques{
  position: absolute;
  left:50%;
  bottom: 5%;
  background: red;
}
#examsubject{
  position: absolute;
  left:40px;
  top:60%;
  font-size: 18px;
}

.el-radio-button, .el-radio-button__inner,.el-checkbox-button, .el-checkbox-button__inner{
  text-align: left;
  display: block;
  margin-top: 20px;
  transition: all 0.5s;
  word-wrap: break-word;
  word-break: normal;
}
@media screen and (max-width: 1400px) {
  .examine-wrap .question {
    width: 70%;
    height: 450px;
  }
  .examine-wrap .question-num {
    margin-left: 83%;
    height: 450px;
  }
}
.footer{
  margin: 20px 30px;
  width: 96.5%;
  height: 50px;
  border:10px solid #ddd;
  border-image: linear-gradient(rgb(202, 236, 49),rgb(12, 214, 113)) 30 30;
  background: white;
}
.footer strong{
  display: inline-block;
  margin-left: 50px;
  font-size: 20px;
}
.footer span{
  display: inline-block;
  font-size: 15px;
  font-family: 'Times New Roman', Times, serif;
  margin-left: 20px;
}
.zzz{
    box-shadow: 1px 1px 1px 1px black;
    border-radius: 5px;
    border: none;
    transition: 0.5s;
}
</style>