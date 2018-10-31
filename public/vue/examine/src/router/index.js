import Vue from 'vue';
import Router from 'vue-router';
import index from '@/components/index';
import examinelogin from '@/components/login';
import examinecheckmsg from '@/components/checkmsg';
import examineing from '@/components/examineing';
import select from '@/components/select';

Vue.use(Router);

export default new Router({
  routes: [
    { path: '/', redirect: '/examinelogin' },
    // { path: '/index', name: 'index', component: index },
    { path: '/examinelogin', name: 'examinelogin', component: examinelogin },
    { path: '/select/:userid', name: 'selectexamine', component: select },
    { path: '/examinecheckmsg/:userid/:examingid', name: 'examinecheckmsg', component: examinecheckmsg },
    { path: '/examineing/:userid/:examingid', name: 'examineing', component: examineing }
  ]
});
