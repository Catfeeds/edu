import Vue from 'vue';
import Router from 'vue-router';
import index from '@/components/index';
import managementuser from '@/components/user/management-user';
import adduser from '@/components/user/adduser';
import showuser from '@/components/common/showuser';
import professionalmgt from '@/components/professional/professionalmgt';
import classmgt from '@/components/class/classmgt';
import organizationmgt from '@/components/organization/organization';
import userList from "@/components/common/userlist";

/* 课程库 */
import courselib from '@/components/course/index';
import chapters from '@/components/course/chapters';
import addcourses from '@/components/course/addcourses';
import courseware from '@/components/course/courseware';


import test from "@/components/layout/test";

Vue.use(Router);

export default new Router({
  routes: [
    { path: '/', redirect: '/library/course/2_1' },
    { path: '/library/course/:tag', name: 'courselib', component: courselib },
    { path: '/course/chapters', name: 'chapters', component: chapters },
    { path: '/course/addcourses', name: 'addcourses', component: addcourses },
    { path: '/course/courseware', name: 'courseware', component: courseware},


    { path: '/library/user/:tag', name: 'mgtuser', component: managementuser },
    { path: '/library/user/add/:tag', name: 'adduser', component: adduser },
    { path: '/library/user/:type/:userid', name: 'showuser', component: showuser },
    { path: '/library/pro/:tag', name: 'professionalmgt', component: professionalmgt },
    { path: '/library/classmgt/:tag', name: 'classmgt', component: classmgt },
    { path: '/library/org/:tag', name: 'organizationmgt', component: organizationmgt },

    { path: '/library/test', name: 'test', component: test },

    // 专业，班级，机构下成员管理 c:类型
    { path: '/library/userlist/:c', name: 'userlist', component: userList }
  ]
});
