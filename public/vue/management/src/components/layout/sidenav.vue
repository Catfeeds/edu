<template>
  <div class="wrap aside">
    <div class="user-msg">
      <img src="../../images/main/avatar.png" alt="" class="avatar">
      <p class="name">admin 系统管理员</p>
      <a href="" class="account-control">[&nbsp;账号管理&nbsp;]</a>
    </div>
    <div class="user-fn">
      <p class="fn-title">常用功能栏</p>
      <ul class="fn-list">
        <li v-bind:key="index" v-for="(item, index) in navList.libraryNav.list"><a :class="{hover: selCurrentTag===item.tags}" @click="switchNav(item.url, item.tags)" href="javascript:void(0)"><i class="icon"></i>{{ item.name }}</a><p v-if="item.line" class="line"></p></li>
      </ul>
    </div>
  </div>
</template>

<script>
/* eslint-disable */
export default {
  data () {
    return {
      // 当前选中的导航
      selCurrentTag: '2_1',
      navList: {
        libraryNav: {
          title: '库中心',
          tag: 'library',
          list: [
            {
              name: '课程库',
              url: '/library/course',
              tag: 'library',
              line: false,
              tags: '2_1'
            },
            {
              name: '用户中心',
              url: '/library/user',
              tag: 'library',
              line: false,
              tags: '2_2'
            },
            {
              name: '专业管理',
              url: '/library/pro',
              tag: 'library',
              line: false,
              tags: '2_3'
            },
            {
              name: '班级管理',
              url: '/library/classmgt',
              tag: 'library',
              line: false,
              tags: '2_4'
            },
            {
              name: '机构中心',
              url: '/library/org',
              tag: 'library',
              line: true,
              tags: '2_5'
            },
            {
              name: '作业库',
              url: '/library/tasklib',
              tag: 'library',
              line: false,
              tags: '2_6'
            },
            {
              name: '其他库',
              url: '/library/other',
              tag: 'library',
              line: false,
              tags: '2_7'
            }
          ]
        }
      }
    }
  },
  mounted(){
    this.selCurrentTag = window.sessionStorage.getItem('switchNav') || this.$route.params.tag || '2_2';
  },
  methods: {
    switchNav (url, tags) {
      this.selCurrentTag = tags;
      this.$router.push({
        'path': url+'/'+tags
      });
      window.sessionStorage.setItem('switchNav', tags);
    }
  }
}
</script>

<style lang="scss">
.aside{
  width: 238px;
  height: 100%;
  margin-right: 1%;
  box-sizing: border-box;
  background: rgba(26,26,54,.5);
  color: #fff;
  font-size: 1.8rem;
  .user-msg{
    text-align: center;
    padding: 35px 0 20px 0;
    img{
      width: 130px;
      height: 130px;
      display: block;
      margin: 0 auto;
      text-align: center;
      background: gray;
      border-radius: 50%;
    }
    .name{
      line-height: 40px;
    }
    .account-control{
      color: #32b16c;
      font-size: 14px;
    }
  }
  .user-fn{
    padding: 0 0 100px 0;
  }
  .fn-title{
    text-align: center;
    line-height: 40px;
    margin-top: 20px;
    border-bottom: 1px solid #ccc;
  }
  .fn-list{
    li{
      a{
        position: relative;
        height: 50px;
        line-height: 50px;
        padding-left: 60px;
        display: block;
        color: #fff;
        text-align: left;
        transition: all .3s;
        i{
          position: absolute;
          top: 9px;
          left: 20px;
          width: 32px;
          height: 32px;
          display: block;
          background-image: url('../../images/main/icon.png');
          background-repeat: no-repeat;
        }
        &:hover, &.hover{
          background: #32b16c;
        }
      }
      .line{
        width: 90%;
        height: 0;
        border-bottom: 1px solid #ccc;
        margin: 6px auto;
      }
      &:nth-child(1) a i{
        background-position: 5px -204px;
      }
      &:nth-child(2) a i{
        background-position: 3px -169px;
      }
      &:nth-child(3) a i{
        background-position: 2px -139px;
      }
      &:nth-child(5) a i{
        background-position: 3px -105px;
      }
      &:nth-child(6) a i{
        background-position: 2px -63px;
      }
      &:nth-child(8) a i{
        background-position: 0 -28px;
      }
    }
  }
}
.aside .fn-list li:nth-child(1) a:hover i,.aside .fn-list li:nth-child(1) a.hover i{
  background-position: -35px -204px;
}
.aside .fn-list li:nth-child(2) a:hover i,.aside .fn-list li:nth-child(2) a.hover i{
  background-position: -37px -169px;
}
.aside .fn-list li:nth-child(3) a:hover i,.aside .fn-list li:nth-child(3) a.hover i{
  background-position: -38px -139px;
}
.aside .fn-list li:nth-child(5) a:hover i,.aside .fn-list li:nth-child(5) a.hover i{
  background-position: -37px -105px;
}
.aside .fn-list li:nth-child(6) a:hover i,.aside .fn-list li:nth-child(6) a.hover i{
  background-position: -38px -63px;
}
.aside .fn-list li:nth-child(8) a:hover i,.aside .fn-list li:nth-child(8) a.hover i{
  background-position: -40px -28px;
}
</style>
