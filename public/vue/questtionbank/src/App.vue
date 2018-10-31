<template>
  <div id="app">
    <header id="nav">
      <div class="nav-wrap">
        <div class="nav-center">
          <div class="main clearfix">
            <p class="nav-location fl">
              <a href="javascript:void(0);">
                <img src="./images/main/logo.png" width="286" height="54" alt="" />
              </a>
            </p>
            <div class="nav-fun fr">
              <p class="top-status fr">
                <span>[&nbsp;
                  <a href="javascript:void(0);">退出</a>&nbsp;]</span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </header>
    <div class="whiteboard"></div>
    <section id="index-content" class="main clearfix">
      <div id="ques-content">
        <keep-alive><router-view :key="key"></router-view></keep-alive>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "App",
  
  mounted () {
    console.log('元数据Const', this.Const);
  },
  methods:{},
  computed: {
    key () {
      return this.$route.name !== undefined? this.$route.name +new Date(): this.$route +new Date();
    }
  }
};
</script>

<style lang="scss">

</style>
