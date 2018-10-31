<template>
  <div id="appmanagement">
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
    <section id="management-center" class="management main clearfix">
      <aside class="side-nav fl">
          <app-sidenav></app-sidenav>
      </aside>
      <section class="lib-r fl">
        <router-view :key="key"></router-view>
      </section>
    </section>
    <footer>
      <app-footer></app-footer>
    </footer>
  </div>
</template>

<script>
import sidenav from '@/components/layout/sidenav';
import appfooter from '@/components/layout/footer';
export default {
  name: "appmanagement",
  activated () {
    // this.Const.userMsg = this.mytools.checkCookie();
  },
  methods: {
    
  },
  components: {
    'app-sidenav': sidenav,
    'app-footer': appfooter
  },
  computed: {
    activeDate () {
      return new Date();
    },
    key () {
      return new Date().getTime();
    }
  }
};
</script>

<style lang="scss">
.lib-r{
  margin-left: 10px;
  background: #ffffff;
  min-height: 773px;
  width: 940px;
  box-sizing: border-box;
  padding: 35px 28px 0;
}
</style>
