// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';
import '../static/js/jquery.cookie';
// import './config/config_data';

Vue.use(ElementUI);

// 开发模式下的配置
const devConfig = {
  userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537352334,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem('userMsg')
  projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
  def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNTQwMjc2MjcxIiwiZXhwIjoxNTY5MDMzNjc3LCJuYmYiOiIxNTQwMjc2MjcxIiwianRpIjoiMzQ1a2V5IiwidWlkIjoiQjYwOEFBNTM1RTBFQzlDNUE3QkM4NTQ4QUYzOUQ0Q0MiLCJ1c2VybmFtZSI6ImFkbWluIiwiaWRudW1iZXIiOiIxMjM0NTYiLCJwaWQiOiJCNjA4QUE1MzVFMEVDOUM1RERCQzg1NDhBRjM5RDRDQiIsImFkZHJlc3MiOiLpq5jmlrDopb_ljLrnmb7lj7bot6_kuIDlj7ciLCJwaWN0dXJlIjpudWxsLCJkZXNjcmlwdGlvbiI6IueuoeeQhuWRmCIsImVkdWNhdGlvbiI6IjQiLCJlbnRlcl90aW1lIjoyMDE1LCJsb2dpbnRpbWUiOiIyMDE4LTEwLTIzIDExOjE4OjEyIn0.1iAlZDEz9c2x35Ltm33lHvOvMEjByD11RaYdlTZJXQI',// window.sessionStorage.getItem('auth_token')
};
/* 
// JWT 生产模式下的配置
const devConfig = {
  userMsg: window.localStorage.getItem('userMsg'),
  projectRoot: window.localStorage.getItem('ProjectRoot'),
  def_auth_token: window.sessionStorage.getItem('auth_token')
};
*/
// 全局变量
Vue.prototype.Const = {
  // 项目根目录
  projectRoot: devConfig.projectRoot,
  // 后台请求地址
  apiurl: devConfig.projectRoot + 'index/api/index',
  // 登录首页地址
  login: devConfig.projectRoot + 'index/index/login',
  // 登录用户信息
  userMsg: devConfig.userMsg ? JSON.parse( devConfig.userMsg ) : null,
  role: [
    {
    value: '',
    name: '所有用户'
    },
    {
    value: '3',
    name: '系管理员'
    },{
    value: '5',
    name: '老师'
    },{
    value: '8',
    name: '学生'
    }
  ],
  // 学历
  educationList: [
    {
    value: '0',
    name: '小学'
    },{
    value: '1',
    name: '初中'
    },{
    value: '2',
    name: '高中'
    },{
    value: '3',
    name: '中专'
    },{
    value: '4',
    name: '大专'
    },{
    value: '5',
    name: '本科'
    },{
    value: '6',
    name: '硕士'
    },{
    value: '7',
    name: '博士'
    }
]
};

// 工具函数
Vue.prototype.mytools = {
  //  清除搜索对象中值为空的属性
  filterParams(obj){
    let form = obj, newPar = {};
    for (let key in form) {
      if (form[key] !== null && form[key] !== "") {
        newPar[key] = form[key].toString();
      }
    }
    return newPar;
  },
  checkCookie () {
    if(!$.cookie('userMsg')){
      window.location.href = window.localStorage.getItem('ProjectRoot')+'index/index/login';
      window.sessionStorage.clear();
      window.localStorage.clear();
    } else {
      return JSON.parse( $.cookie('userMsg') );
    }
  }
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
  el: '#appmanagement',
  router,
  components: { App },
  template: '<App/>'
});

// 公共样式
require('./style/common/reset.css');
require('./style/common/common.css');
