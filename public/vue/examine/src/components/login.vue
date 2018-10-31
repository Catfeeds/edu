<template>
  <div class="login_box clearfix">
    <div class="wrap clearfix">
      <div class="login_l_img"><img src="../images/main/login-img.png" /></div>
      <div class="login">
        <div class="login_logo"><a href="javascript: void(0);"><img src="../images/main/login_logo.png" /></a></div>
        <div class="login_name">
          <p>用户登陆</p>
        </div>
        <form>
          <input v-model="user.name" type="text" placeholder="用户名" />             
          <input v-model="user.password" type="password" id="password" placeholder="密码" />
          <input type="button" @click="reqSubmit" value="登录"/>
        </form>
      </div>
      <div class="copyright">某某有限公司 版权所有©2016-2018 技术支持电话：000-00000000</div>
  </div>
</div>
</template>

<script>
  export default {
    data () {
      return {
        user: {
          name: 'admin',
          password: '123123'
        }
      }
    },
    methods: {
      reqSubmit () {
        // TODO 提交登录
        let userid = this.Const.userMsg['uid'];
        let This = this;
        this.$message({type: 'success', message: '登录成功！', onClose () {
          This.$router.push({path: `/select/${userid}`});
        }});
      }
    }
  }
</script>

<style lang="scss" scoped>
a { color:#27A9E3; text-decoration:none; cursor:pointer; }
img{ border:none;}
.login_box{ width: 100%; background:#93defe; padding-top: 100px; height: 100%; min-height: 650px; box-sizing: border-box;
  .wrap {
    width: 1200px;
    margin: 0 auto;
    padding: 0 100px;
    box-sizing: border-box;
  }
  .login_l_img{ 
    float:left;
    margin-right: 100px;
    img{width:500px; height:440px;}
  }
  .login {
    height:360px; 
    width:400px; 
    padding:50px; 
    background-color: #ffffff;
    border-radius:8px;
    box-sizing: border-box; 
    float: left; 
    position:relative; 
    margin-top:50px;
    .login_logo{ width:120px; height:120px; border:5px solid #93defe;border-radius:100px; background:#fff; text-align:center; line-height:110px; position:absolute; top:-60px; right:140px;
      img{ width:60px; height:60px;display: inline-block; vertical-align: middle;}
    }
    .login_name{ width:100%; float:left; text-align:center; margin-top:20px;
      p{ width:100%; text-align:center; font-size:18px; color:#444; padding:10px 0 20px;}
    }
  }
}
input[type=text], input[type=file], input[type=password], input[type=email], select { border: 1px solid #DCDEE0; vertical-align: middle; border-radius: 3px; height: 50px; padding: 0px 16px; font-size: 14px; color: #555555; outline:none; width:88.5%;margin-bottom: 15px;line-height:50px; color:#888;}
input[type=text]:focus, input[type=file]:focus, input[type=password]:focus, input[type=email]:focus, select:focus { border: 1px solid #27A9E3; }
input[type=submit], input[type=button] { display: inline-block; vertical-align: middle; padding: 12px 24px; margin: 0px; font-size:16px; line-height: 24px; text-align: center; white-space: nowrap; vertical-align: middle; cursor: pointer; color: #ffffff; background-color: #27A9E3; border-radius: 3px; border: none; -webkit-appearance: none; outline:none; width:100%; }
.copyright { font-size:14px; color:#fff; display:block;width:100%; text-align:center; position: fixed; bottom: 50px;left: 0; right: 0;margin: 0 auto;}

</style>
