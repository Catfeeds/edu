<template>
<div class="class-wrap content" id="course">
  <!--课程顶部信息 start-->
  <aside class="top-nav">
    <div class="cont fr">
      <span class="cont-big">课程名称</span>
      <span>专业基础课</span>
      <div class="course-data">
        <div class="study study-range">
          <span>适用范围</span>
          <span>移动互联方向</span>
        </div>
        <div class="study study-class">
          <span>课程数</span>
          <span>48节</span>
        </div>
        <div class="study study-num">
          <span>学习人数</span>
          <span>31</span>
        </div>
      </div>
    </div>
  </aside>
  <!--课程顶部信息 end-->
  <div class="classify-wrap">
    <!--课堂导航列表 start-->
    <div class="classify clearfix">
      <ul class="fl">
        <li class="check">
            <i class="before" style="left: 0;"></i>
            <router-link class="click" to="/course/">章节</router-link>
        </li>
        <li class="">
            <i class="before" style="left: 0;"></i>
            <router-link to="/course/tasks">作业</router-link>
        </li>
        <li class="">
            <i class="before" style="left: 0;"></i>
            <a href="javascript:void(0);">实验</a>
        </li>
        <li class="">
            <i class="before" style="left: 0;"></i>
            <a href="javascript:void(0);">分组</a>
        </li>
        <li class="">
            <i class="before" style="left: 0;"></i>
            <router-link to="/course/members">成员</router-link>
        </li>
      </ul>
    </div>
    <!--课堂导航列表 end-->
    <router-view></router-view>
  </div>
</div>
</template>

<script>
export default {
  data: function(){
    return {
      
    }
  },
  mounted: function(){

  }
}
</script>

<style lang="scss">
  .classify-wrap {
    .classify {
      margin-bottom: 10px;
    }
  }
</style>
