// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';
// 注册自定义组件
import yk_components from './plugins/components';

Vue.use(ElementUI);
Vue.use(yk_components);

// 开发模式下的配置
const devConfig = {
  userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537352334,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem('userMsg')
  projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
  def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE1Mzc0MjcxOTcsImV4cCI6MTUzNzQyNzc5NywibmJmIjoxNTM3NDI3MTk3LCJqdGkiOiIzNDVrZXkiLCJ1aWQiOiJCNjA4QUE1MzVFMEVDOUM1QTdCQzg1NDhBRjM5RDRDQyIsInVzZXJuYW1lIjoiYWRtaW4iLCJpZG51bWJlciI6IjEyMzQ1NiIsInBpZCI6IkI2MDhBQTUzNUUwRUM5QzVEREJDODU0OEFGMzlENENCIiwiYWRkcmVzcyI6IumrmOaWsOilv-WMuueZvuWPtui3r-S4gOWPtyIsInBpY3R1cmUiOm51bGwsImRlc2NyaXB0aW9uIjoi566h55CG5ZGYIiwiZWR1Y2F0aW9uIjoiNCIsImVudGVyX3RpbWUiOjIwMTUsImxvZ2ludGltZSI6MTUzNzQyMzM2OH0.4PKxgCy_NrKwmAIZGKta5AxYf2o7MRskkuNvweTKdTk',// window.sessionStorage.getItem('auth_token')
};
/* 
 // JWT 生产模式下的配置
const devConfig = {
  userMsg: window.localStorage.getItem('userMsg'),
  projectRoot: window.localStorage.getItem('ProjectRoot'),
  def_auth_token: window.sessionStorage.getItem('auth_token')
};
*/

Vue.prototype.Const = {
  examingname: '',
  // 项目根目录
  projectRoot: devConfig.projectRoot,
  // 后台请求地址
  apiurl: devConfig.projectRoot + 'index/api/index',
  // 登录首页地址
  login: devConfig.projectRoot + 'index/index/login',
  // 登录用户信息
  userMsg: devConfig.userMsg ? JSON.parse( devConfig.userMsg ) : null
};

// axios 改写为 Vue 的原型属性,方便全局调用
axios.defaults.timeout = 1000 * 10;
// 添加请求拦截器，请求发送之前
axios.interceptors.request.use(config => {
  let sess_auth_token = window.sessionStorage.getItem('auth_token');
  config.headers['Authorization'] = sess_auth_token ? sess_auth_token : devConfig.def_auth_token;
  return config;
}, error => {
  return Promise.reject(error);
});
// 添加响应拦截器，浏览器接收之前
axios.interceptors.response.use(response => {
  // console.log(response);
  if (response.data.status == 0) {
    window.sessionStorage.setItem('auth_token', response.data.auth_token);
  } else if (response.data.status == 9999) {
    window.location.href = devConfig.projectRoot + 'index/index/login';
  }
  return response;
}, error => {
  return Promise.reject(error);
});
Vue.prototype.$ajax = axios;



Vue.config.productionTip = false;

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
});

// 公共样式
require('./style/common/reset.css');
require('./style/common/common.css');
