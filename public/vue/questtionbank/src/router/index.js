import Vue from 'vue';
import Router from 'vue-router';
import index from '@/components/index';
import addbank from '@/components/addbank';
import file from '@/components/file';
import myfile from '@/components/myfile';
import checkbank from '@/components/checkbank';

Vue.use(Router)

export default new Router({
  routes: [
    { path: '/', redirect: '/index' },
    { path: '/index', name: 'index', component: index },
    { path: '/checkbank', name: 'checkbank', component: checkbank },
    { path: '/addbank', name: 'addbank', component: addbank },
    { path: '/edit/:questionid', name: 'edit', component: addbank },
    { path: '/file', name: 'file', component: file },
    { path: '/myfile', name: 'myfile', component: myfile }
  ]
})
