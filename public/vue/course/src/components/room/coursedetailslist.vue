<template>
  <section id="chapters">
    <!--课程列表 start-->
    <div class="course-con">
      <div class="container">
          <!--课程的详细安排列表内容-->
        <div class="course-list">
          <div class="mod-chapters">
            <!--章节-->
            <el-menu default-active="1-4-1" class="el-menu-vertical-demo">
              <el-submenu :index="item.id" v-for="(item, index) in chaptersList" :key="index">
                <template slot="title">
                  <i class="el-icon-menu"></i>
                  <span slot="title">{{item.section}}</span>
                </template>
                <el-menu-item v-for="(item2, index2) in item.child" :key="index2" :index="item2.id"><i class="el-icon-document"></i><span>{{item2.sections}}</span></el-menu-item>
              </el-submenu>
            </el-menu>
            <!-- 添加章节 start -->
            <div class="add-chapter">
              <h3>
                <strong v-bind:courseId="curCourse.id" class="chapter-title" id="add-chapterlib" v-on:click="addchapter(curCourse.id)"><a href="javascript:void(0);">添加章节</a></strong>
              </h3>
            </div>
            <!-- 添加章节 end -->
          </div>
        </div>
      </div>
    </div>
    <!--课程列表 end-->

    <!-- 分页 start-->
    
    <!-- 分页 end-->
</section>
</template>

<script>
export default {
  data: function() {
    var thisVue = this;
    return {
      curCourse: "", // ,
      chaptersList: [
        {
          id: '1231231',
          section: '第一章',
          child: [
            {
              id: 'sjfusdfs',
              sections: '第1-1节'
            },{
              id: 'sjfus32dfs',
              sections: '第1-2节'
            }
          ]
        },{
          id: '123126731',
          section: '第二章',
          child: [
            {
              id: 'sjfus5dfs',
              sections: '第2-1节'
            },{
              id: 'sjfufrs32dfs',
              sections: '第2-2节'
            }
          ]
        }
      ]
    };
  },
  mounted: function() {

  },
  methods: {
    
  }
};
</script>

<style lang="scss">
#chapters {
  .el-submenu {
    background: #ecf5ff;
    margin: 0 0 5px 0;
    .el-submenu__title:hover {
      background-color: rgb(175, 171, 171);
    }
    .el-menu-item:focus, .el-menu-item:hover{
      background-color: rgb(175, 171, 171);
    }
    .el-menu-item.is-active{
      color: #fff;
    }
  }
}
</style>
