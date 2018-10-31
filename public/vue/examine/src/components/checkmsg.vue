<template>
<div>
  <yk-header></yk-header>
  <div class="main-checking">
    <div class="exam-read">
      <h1 class="exam-to">在线考试须知</h1>
      <br><br><br>
      <h3 class="exam-title">考试纪律</h3>
      <p>考试全程请不要使用QQ、微信等lM软件及手机交流信息，不要佩戴耳机；按系统要求开启摄像头，对准面部；禁止替考。</p>
      <br>
      <h3 class="exam-title">作弊说明</h3>
      <p>因为信任，所以简单，请诚实作答。任意作弊行为一经核实，将取消资格，并记录在诚信档案中。</p>
      <br>
      <h3 class="exam-title">信息隐私</h3>
      <p>您的个人信息、答题信息将受到保护，不会向任何第三方透露；禁止拍照试题，泄露试题内容为侵权行为。</p>
      <br>
      <el-checkbox v-model="button_checked">我保证题目作答由个人独立完成，不在做题过程中获取来自网络、书籍、他人的帮助</el-checkbox>
    </div>
    <div class="exam-check">     
      <el-form ref="form" :model="form" label-width="80px">
        <h2 class="exam-to">信息核对</h2><br><br><br>
        <el-form-item label="用户名">
          <el-input :readonly="!check_info" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input :readonly="!check_info" v-model="form.email"></el-input>
        </el-form-item>
        <el-form-item label="手机">
          <el-input :readonly="!check_info" v-model="form.phone"></el-input>
        </el-form-item>
        <el-form-item label="场次名称">
          <el-input :readonly="!check_info" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button v-if="timeBtn" type="primary" disabled>{{timeBtn}} s</el-button>
          <el-button v-else :disabled="!button_checked" @click="startExaming" type="primary">开始考试</el-button>
          <el-button v-on:click="check_info = true">信息有误我要修改</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</div>
</template>

<script>
  export default {
    data() {
      return {
        // 设置至少10s阅读时间
        timeBtn: 10,
        button_checked: false,
        check_info: false,
        examingname: this.Const.examingname,
        form: {
          input_checked: true,
          email: '1720673152@qq.com',
          phone: '13540846105',
          name:'c语言考试1'
        }
      }
    },
    activated () {
      this.readTime();
    },
    methods: {
      startExaming () {
        this.$router.push({path: `/examineing/${this.$route.params.userid}/${this.$route.params.examingid}`});
      },
      readTime () {
        let timeClear = setInterval(_ => {
          this.timeBtn--;
          if (this.timeBtn <= 0) {
            this.timeBtn = 0;
            clearInterval(timeClear);
          }
        }, 1000);
      }
    }
  }
</script>

<style lang="scss">
.main-checking{
  width: 1200px;
  margin: 100px auto;
}
.exam-to{
  text-align: center;
}
.exam-read{
  float: left;
  width: 820px;
}
.exam-check{
  float: left;
  text-align: center;
  width: 380px;
}
.exam-title{
  color: red;
}
p{
  font-size: 1px;
}
</style>
