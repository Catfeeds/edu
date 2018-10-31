<template>
  <div id="app">
    <!-- <div class="whiteboard"></div> -->
    <section id="index-content" class="clearfix">
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
#paper-content{
  height: 100%;
}
</style>
