import Vue from 'vue';
import Router from 'vue-router';
import index from '@/components/index';
import positionindex from '@/components/position/index';
import posi from '@/components/position/addposi';
import edittest from '@/components/position/edittest';
import previewtest from '@/components/position/previewtest';
import additems from '@/components/position/additems';


import paperindex from '@/components/paper/index';
import addpaper from '@/components/paper/addpaper';
import checkpaper from '@/components/paper/checkpaper';

Vue.use(Router);

export default new Router({
  routes: [
    { path: '/', redirect: '/index' },
    { path: '/index', name: 'index', component: index },
    { path: '/position', name: 'positionindex', component: positionindex },
    { path: '/position/posi', name: 'posi', component: posi },
    { path: '/posi/edittest', name: 'edittest', component: edittest },
    { path: '/posi/previewtest', name: 'previewtest', component: previewtest },
    { path: '/posi/additems', name: 'additems', component: additems },


    { path: '/paper', name: 'paperindex', component: paperindex },
    { path: '/paper/addpaper', name: 'addpaper', component: addpaper },
    { path: '/paper/checkpaper', name: 'checkpaper', component: checkpaper }
  ]
});
