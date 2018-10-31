import Vue from 'vue';
import Router from 'vue-router';

// 课堂路由
import courmain from "@/components/room/coursesMain";
import addcourses from '@/components/room/addcourses';
import adduser from '@/components/room/addUser';
import courseDetails from '@/components/room/courseDetails';
import coursedetailslist from '@/components/room/coursChapterlist';



Vue.use(Router);

export default new Router({
  routes: [
    { path: '/', redirect: '/courmain/2_1' },
    { path: '/courmain/2_1', name: "courmain", component: courmain },
    { path: '/course/addcourses', name: "addcourses", component: addcourses },
    { path: '/course/adduser', name: "adduser", component: adduser },
    { 
      path: '/course',
      name: 'coursecon',
      component: courseDetails,
      children: [
        { path: 'details', component: coursedetailslist }
      ]
    },
  ]
});
