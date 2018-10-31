<template>
  <div class="select-examine">
    <yk-header></yk-header>
    <section class="examine-list main">
      <!-- <div class="title">
        <span><i class="el-icon-d-arrow-right"></i>添加试题</span>
        <div class="fr"><el-button @click="goback" round type="primary" plain>返回</el-button></div>
      </div> -->
      <ul class="clearfix">
        <li v-for="(item, index) in examineList" :key="index">
          <h3>{{ item.name }}</h3>
          <p class="message">{{ item.message }}</p>
          <p>开始时间：{{ item.startTime }}</p>
          <p>结束时间：{{ item.endTime }}</p>
          <p class="limittime">作答时间：<span>{{ item.limitTime }}</span> (小时)</p>
          <el-button @click="examingStart(item)" type="success">进入考试</el-button>
        </li>
      </ul>
    </section>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        examineList: [
          {
            id: '12312sadasd',
            name: 'c语言考试1',
            message: '大一相关专业c语言考试',
            startTime: '2018-06-11 14:30:00',
            endTime: '2018-06-11 16:30:00',
            status: '0', // 0:待考试|1：考试中
            limitTime: 2
          },{
            id: '12312sada123sd',
            name: 'c语言考试2',
            message: '大一相关专业c语言考试',
            startTime: '2018-06-11 14:30:00',
            endTime: '2018-06-11 16:30:00',
            status: '1', // 0:待考试|1：考试中
            limitTime: 1.5
          },{
            id: '12312sada123sd',
            name: 'c语言考试3',
            message: '大一相关专业c语言考试',
            startTime: '2018-06-11 14:30:00',
            endTime: '2018-06-11 16:30:00',
            status: '1', // 0:待考试|1：考试中
            limitTime: 1.5
          },{
            id: '12312sada123sd',
            name: 'c语言考试4',
            message: '大一相关专业c语言考试',
            startTime: '2018-06-11 14:30:00',
            endTime: '2018-06-11 16:30:00',
            status: '1', // 0:待考试|1：考试中
            limitTime: 1.5
          },{
            id: '12312sada123sd',
            name: 'c语言考试5',
            message: '大一相关专业c语言考试',
            startTime: '2018-06-11 14:30:00',
            endTime: '2018-06-11 16:30:00',
            status: '1', // 0:待考试|1：考试中
            limitTime: 1.5
          }
        ]
      };
    },
    methods: {
      goback () {
        this.$router.go(-1);
      },
      // 获取考试列表
      requestExamList () {
        // TODO
      },
      // 进入考试
      examingStart (item) {
        if (this.$route.params.userid) {
          this.Const.examingname = item.name;
          this.$router.push({path: `/examinecheckmsg/${this.$route.params.userid}/${item.id}`});
        }
      }
    }
  }
</script>

<style lang="scss">
.examine-list {
  margin-top: 100px !important;
  .title{
    @include middelH(50px);
    border-bottom: 1px solid gray;
    @include setTitle;
    i{
      padding-right: 10px;
    }
  }
  li {
    float: left;
    width: 290px;
    height: 300px;
    position: relative;
    margin: 10px 10px 0 0;
    border: 1px solid #c0ccda;
    box-sizing: border-box;
    padding: 10px;
    border-radius: 6px;
    transition: all .3s;
    &:hover {
      border-color: #409eff;
    }
    button {
      position: absolute;
      bottom: 20px;
      right: 20px;
    }
    h3 {
      @include middelH(40px);
      font-size: 16px;
      color: #07111B;
      word-wrap: break-word;
      overflow: hidden;
      text-overflow: ellipsis;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
    }
    p {
      @include middelH(30px);
      &.message {
        margin-bottom: 20px;
      }
      &.limittime span {
        color: rgb(179, 80, 24);
        font-size: 2.0rem;
      }
    }
  }
}
</style>
