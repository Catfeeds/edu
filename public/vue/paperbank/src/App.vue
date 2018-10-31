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
      <div id="paper-content">
        <keep-alive>
          <router-view></router-view>
        </keep-alive>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "App"
};
</script>

<style lang="scss">
#paper-content {
  .title{
    @include middelH(50px);
    border-bottom: 1px solid gray;
    margin-bottom: 40px;
    @include setTitle;
    i{
      padding-right: 10px;
    }
  }
}
</style>
