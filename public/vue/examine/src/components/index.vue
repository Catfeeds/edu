<template>
  <div class="examinesys">
    考试中心，与其他模块完全独立，从登陆考试到提交试卷整个流程。
    页面布局设计，自己解决（可以参考一些其他的在线考试系统设计，例如：赛码在线考试），有疑问的地方我们讨论。
    这个你们自己定，根据业务需求设计页面布局。
    其中考试页面有设计图，基本已经定了。只需要一些其他的辅助页面设计。
    考试系统做全面屏，最小宽度1200px。
    目前先根据业务需求做页面开发，不会与后台进行交互，如果后台可以请求数据了，在做数据交互。页面中用到的数据
    都做假数据。后台数据请求可能会出问题，就暂时先不管，和面会给你们统一说一下这个数据请求。先在只做页面功能开发。
    考试系统内页-2.png。考试主页。其他页面风格和这一页保持一致。
    <footer>
      <router-link to="/examinelogin">登录考试系统</router-link>
      <router-link to="/examinecheckmsg">考生信息核对和考试须知</router-link>
      <router-link to="/examineing">开始考试</router-link>
    </footer>
  </div>
</template>

<script>
export default {
  data() {
    let thisVue = this;
    return {
      
    };
  },
  mounted() {
    
  },
  methods: {
    
  }
};
</script>

<style lang="scss">
/* 这里可以写css预编译语言 */
footer{
  border: 1px solid red;
  @include middelH(40px);
  a{
    padding: 0 10px;
  }
}
</style>
