<template>
  <div class="bankindex paperbank main" id="paperbank">
    <aside class="aside">
      <p class="title"><i class="el-icon-d-arrow-right">考试信息</i></p>
      <div @click="examineing" class="typeitem">
        <i class="el-icon-news"></i>
        <span>考试入口</span>
      </div>
      <p class="title"><i class="el-icon-d-arrow-right"></i>课程信息</p>
      <ul class="course-info">
        <li>
          <span class="key">课程名称:</span>
          <span class="value">{{courseMsg.courseName}}</span>
        </li>
        <li>
          <span class="key">考试人数:</span>
          <span class="value">{{courseMsg.number}}</span>
        </li>
        <li>
          <span class="key">任课老师:</span>
          <span class="value">{{courseMsg.teacher}}</span>
        </li>
      </ul>
    </aside>
    <section class="section">
      <div class="top-news">
        <ul class="clearfix">
          <li class="show">
            <i class="el-icon-edit-outline"></i>
            <p><span class="num">{{ courseMsg.paperNum }}</span>场</p>
            <p>考试场次总数</p>
          </li>
          <li class="show">
            <i class="el-icon-document"></i>
            <p><span class="num">{{ courseMsg.posiNum }}</span>套</p>
            <p>试卷总数</p>
          </li>
          <li @click="fnRoutePaper" class="add">
            <i class="el-icon-circle-plus-outline"></i>
            <p>考试场次中心</p>
          </li>
          <li @click="fnRoutePositon" class="add">
            <i class="el-icon-circle-plus-outline"></i>
            <p>试卷中心</p>
          </li>
        </ul>
      </div>
      <div class="old-news">
        <p class="title"><i class="el-icon-d-arrow-right"></i>近期考试</p>
        <el-table :data="latelypaperList" max-height="300" border style="100%">
          <el-table-column prop="papername" label="考试场次" width="240"></el-table-column>
          <el-table-column prop="startTime" label="开始时间">
            <template slot-scope="scope">
              <el-popover trigger="hover" placement="top">
                <p>结束时间: {{ scope.row.endTime }}</p>
                <div slot="reference" class="name-wrapper">
                  <i class="el-icon-time"></i>
                  <span style="margin-left: 10px">{{ scope.row.startTime }}</span>
                </div>
              </el-popover>
            </template>
          </el-table-column>
          <el-table-column prop="examinee" label="考生人数" width="100"></el-table-column>
          <el-table-column prop="status" label="考试状态" width="180">
            <template slot-scope="scope">
              <el-tag type="success" v-if="scope.row.status == 0" size="medium">考试中</el-tag>
              <el-tag type="danger" v-if="scope.row.status == 1" size="medium">已结束</el-tag>
            </template>
          </el-table-column>
        </el-table>
        <p class="readmore" @click="fnRoutePaper">详  情</p>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      courseMsg: {
        courseName: '',
        number: '',
        teacher: '',
        // 场次多少
        posiNum: '',
        // 试卷多少
        paperNum: ''
      },
      latelypaperList: [
        {
          papername: 'c语言期末考试',
          startTime: '2018-06-11 14:30',
          endTime: '2018-06-11 16:30',
          examinee: 10,
          status: 1, // 0:考试中|1:已结束
        },{
          papername: '计算机网络考试',
          startTime: '2018-06-11 14:30',
          endTime: '2018-06-11 16:30',
          examinee: 10,
          status: 1
        }
      ]
    };
  },
  mounted() {
    this.requestMsg();
  },
  methods: {
    // 添加试卷
    fnRoutePositon () {
      let routerData = this.$router.resolve({path: '/position'});
      window.open(routerData.href, '_blank');
    },
    fnRoutePaper () {
      let routerData = this.$router.resolve({path: '/paper'});
      window.open(routerData.href, '_blank');
    },
    // 考试入口
    examineing () {
      window.open(this.Const.STATIC, '_blank');
    },
    // 获取课堂信息 试卷数 场次数 最近考试场次列表
    requestMsg () {
      // TODO
      this.courseMsg = {
        courseName: '测试',
        number: '测试',
        teacher: '测试',
        // 场次多少
        posiNum: '2',
        // 试卷多少
        paperNum: '2'
      };
    }
  }
};
</script>

<style lang="scss">
/* 这里可以写css预编译语言 */
#paperbank {
  .aside {
    width: 320px;
    background: #fff;
    float: left;
    margin-right: 20px;
    .title {
      @include middelH(50px);
      font-size: 1.6rem;
      font-weight: 700;
      padding-left: 10px;
      i {font-weight: 700;}
    }
    .typeitem {
      position: relative;
      margin: 10px 0;
      height: 60px;
      line-height: 60px;
      border: 1px solid #32b16c;
      box-sizing: border-box;
      color: #32b16c;
      cursor: pointer;
      margin-bottom: 20px;
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
    ul li {
      @include middelH(50px);
      padding-left: 20px;
      .key {
        letter-spacing: 4px;
      }
      .value {
        font-size: 2.0rem;
      }
    }
  }
  .section {
    width: 860px;
    box-sizing: border-box;
    float: left;
    background: #fff;
    padding: 10px;
    .top-news {
      li {
        width: 405px;
        height: 200px;
        float: left;
        margin-left: 10px;
        text-align: center;
        font-size: 2.0rem;
        line-height: 34px;
        box-sizing: border-box;
        padding-top: 40px;
        i{
          font-size: 5.0rem;
        }
        .num{margin-right: 10px; color: #ff8a00;font-size: 3.0rem;}
        &.show {
          background: rgb(114, 214, 212);
          color: #fff;
          &:hover {
            background: #2abcb8;
          }
        }
        &.add {
          background: rgb(235, 255, 254);
          border: 2px dashed #2abcb8;
          cursor: pointer;
          &:hover {
            color: #2abcb8;
          }
        }
      }
    }
    .old-news {
      margin-top: 20px;
      .title{
        font-size: 1.6rem;
        @include middelH(40px);
        font-weight: 700;
        padding-left: 10px;
        i {font-weight: 700;}
      }
    }
    .readmore {
      text-align: center;
      margin-top: 10px;
      @include middelH(40px);
      cursor: pointer;
      &:hover {
        color: #2abcb8;
      }
    }
  }
}
</style>
