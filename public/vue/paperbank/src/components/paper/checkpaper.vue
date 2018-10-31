<template>
  <section id="checkpaper" class="main clearfix">
    <div class="title">
      <span v-if="$route.query.type == 'check'"><i class="el-icon-d-arrow-right"></i>审核考试场次</span>
      <span v-if="$route.query.type == 'read'"><i class="el-icon-d-arrow-right"></i>查看考试场次</span>
      <div class="fr"><el-button @click="$router.back(-1)" round type="primary" plain>返回</el-button></div>
    </div>
    <aside class="aside-bar">
      <div class="list-title"><span>考试场次信息</span> <i id="test-title"></i></div>
      <div class="check-btn" v-if="$route.query.type == 'check'">
        <el-button-group>
          <el-button type="primary" @click="subPass"><i class="el-icon-check"></i></el-button>
          <el-button type="primary" @click="subNoPass"><i class="el-icon-close"></i></el-button>
        </el-button-group>
      </div>
    </aside>
    <div class="check-wrap">
      <div class="item">
        <p class="item-title">试卷名称</p>
        <p class="item-con"><el-button @click="readPosition(paperdata.quiz)" plain type="success"><i class="el-icon-view"></i> {{paperdata.quiz_name}}</el-button></p>
      </div>
      <div class="item">
        <p class="item-title">场次名称</p>
        <p class="item-con">{{paperdata.name}}</p>
      </div>
      <div class="item">
        <p class="item-title">场次状态</p>
        <p class="item-con">
          <el-tag v-if="paperdata.check == 1" type="danger">考试未开始</el-tag>
          <el-tag v-if="paperdata.check == 2" type="danger">考试中</el-tag>
          <el-tag v-if="paperdata.check == 3" type="danger">考试结束</el-tag>
          <el-tag v-if="paperdata.check == 4" type="danger">待审核</el-tag>
          <el-tag v-if="paperdata.check == 5" type="danger">审核未通过</el-tag>
        </p>
      </div>
      <div class="item">
        <p class="item-title">开放时间</p>
        <p class="item-con">{{paperdata.starttime}} ~ {{paperdata.endtime}}</p>
      </div>
      <div class="item">
        <p class="item-title">考试时长</p>
        <p class="item-con"><el-tag type="danger">{{paperdata.limittime}}</el-tag></p>
      </div>
      <div class="item">
        <p class="item-title">答题次数</p>
        <p class="item-con"><el-tag type="warning">{{paperdata.attempts}} 次</el-tag></p>
      </div>
      <div class="item">
        <p class="item-title">创建者名</p>
        <p class="item-con"><el-tag type="success">{{paperdata.auth}}</el-tag></p>
      </div>
      <div class="item" style="height: 80px">
        <p class="item-title">所属章节</p>
        <p class="item-con">
          <el-tag v-for="(item, index) in paperdata.section" :key="index">{{item}}</el-tag>
          <el-tag v-for="(item, index) in paperdata.sections" :key="index">{{item}}</el-tag>
        </p>
      </div>
      <div class="item">
        <p class="item-title">场次描述</p>
        <p class="item-con">{{paperdata.message}}</p>
      </div>
      <p v-if="paperdata.check == 5 || paperdata.check == 4" class="check-status nopass"><i class="el-icon-close"></i></p>
      <p v-else class="check-status pass"><i class="el-icon-check"></i></p>
    </div>
  </section>
</template>
<script>
export default {
  data () {
    return {
      paperdata: {
        id: '',
        name: '',
        message: '',
        starttime: '',
        endtime: '',
        limittime: '',
        check: '',
        attempts: '',
        quiz: '',
        quiz_name: '',
        auth: '',
        timecreated: '',
        sections: {
          section: [],
          sections: []
        }
      }
    }
  },
  mounted () {
    this.getPaperInfo();
  },
  methods: {
    // 查看试卷
    readPosition (id) { console.log(id); },
    // 通过审核
    subPass () {
      this.$confirm('审核通过的考试场次不能在做任何修改, 是否继续?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$ajax.post(this.Const.apiUrl, {
          module: 'exam',
          controller: 'project',
          action: 'audit',
          id: this.$route.query.d,
          status: 1,
          msg: ''
        }).then(response => {
          console.log('审核通过的提交', response);
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: '审核成功！'});
          } else {
            this.$message({type: 'info', message: resData.data || '审核失败！'});
          }
        }).catch(error => {
          this.$message({type: 'error', message: '审核失败！'});
        });
      }).catch(() => { });
    },
    // 审核未通过
    subNoPass () {
      this.$prompt('请输入此考试场次审核不通过原因', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        inputPattern: /\S/,
        inputErrorMessage: '请输入此考试场次审核不通过原因'
      }).then(({ value }) => {
        this.$ajax.post(this.Const.apiUrl, {
          module: 'exam',
          controller: 'project',
          action: 'audit',
          id: this.$route.query.d,
          status: 2,
          msg: value
        }).then(response => {
          console.log('审核bu通过的提交', response);
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: '审核成功！'});
          } else {
            this.$message({type: 'info', message: resData.data || '审核失败！'});
          }
        }).catch(error => {
          this.$message({type: 'error', message: '审核失败！'});
        });
      }).catch(() => { });
    },
    // 获取场次信息
    getPaperInfo () {
      this.$ajax.post(this.Const.apiUrl, {
        module: 'exam',
        controller: 'project',
        action: 'details',
        id: this.$route.query.d
      }).then(response => {
        let resData = response.data;
        if (resData.status == 0) {
          this.paperdata = resData.data;
        } else {
          this.$message({type: 'info', message: resData.data || '获取场次信息失败'});
        }
      }).catch(error => {
        this.$message({type: 'error', message: '获取场次信息失败:'+error.message});
      });
    }
  },
  watch: {
    '$route': {
      handler (newVal, oldVal) {
        if (newVal.query.d) {
          this.getPaperInfo();
        }
      },
      deep: true
    }
  }
}
</script>
<style lang="scss">
#checkpaper {
  background-color: #f8f9fb;
  box-sizing: border-box;
  padding: 20px;
  .aside-bar {
    width: 200px;
    float: left;
    .check-btn {
      margin: 10px auto 40px;
    }
    .el-button{
      border-radius: 0;
      width: 100px;
      height: 50px;
    }
    .list-title {
      height: 35px;
      margin: 0 auto;
      background-color: #28bdb9;
      color: white;
      text-align: center;
      font-size: 0.1rem;
      line-height: 35px;
      cursor: pointer;
    }
  }
  .check-wrap {
    position: relative;
    float: left;
    width: 920px;
    margin-left: 40px;
    background: #fff;
    box-shadow: 0px 1px 1px #dde0e7;
    padding: 20px;
    box-sizing: border-box;
    overflow: hidden;
    .item {
      height: 40px;
      line-height: 40px;
      margin-bottom: 22px;
      > * {float: left;}
      .item-title {
        width: 100px;
        text-align: right;
        margin-right: 20px;
      }
      .item-con {
        max-width: 70%;
      }
      &.check {
        text-align: center;
        margin-bottom: 50px;
        .item-con {max-width: 100%;float: none;}
      }
    }
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

</style>
