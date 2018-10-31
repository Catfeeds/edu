// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';

Vue.use(ElementUI);


// 调试模式下的配置
const devConfig = {
  userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537260682,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem('userMsg')
  projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
  def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE1MzgwMzYyNjQsImV4cCI6MTUzODAzNjg2NCwibmJmIjoxNTM4MDM2MjY0LCJqdGkiOiIzNDVrZXkiLCJ1aWQiOiJCNjA4QUE1MzVFMEVDOUM1QTdCQzg1NDhBRjM5RDRDQyIsInVzZXJuYW1lIjoiYWRtaW4iLCJpZG51bWJlciI6IjEyMzQ1NiIsInBpZCI6IkI2MDhBQTUzNUUwRUM5QzVEREJDODU0OEFGMzlENENCIiwiYWRkcmVzcyI6IumrmOaWsOilv-WMuueZvuWPtui3r-S4gOWPtyIsInBpY3R1cmUiOm51bGwsImRlc2NyaXB0aW9uIjoi566h55CG5ZGYIiwiZWR1Y2F0aW9uIjoiNCIsImVudGVyX3RpbWUiOjIwMTUsImxvZ2ludGltZSI6IjIwMTgtMDktMjcgMTY6MTc6MjIifQ.72Xb9sNn93PuMxHDQ4xRBA5HL_ktj83UHCKDnvSJot8',// window.sessionStorage.getItem('auth_token')
  courseData: 'http://localhost/edu/public/edumodel/questionbank/?d=C9036CA7A41C95D9B919BC990A4E67B4#/index',// window.location.href
};
/*
// 生产模式下的配置
const devConfig = {
  userMsg: window.localStorage.getItem('userMsg'),
  projectRoot: window.localStorage.getItem('ProjectRoot'),
  def_auth_token: window.sessionStorage.getItem('auth_token'),
  courseData: window.location.href
};
*/
// 全局变量
Vue.prototype.Const = {
  // 登录用户信息
  userMsg: devConfig.userMsg ? JSON.parse( devConfig.userMsg ) : null,
  // 后台请求地址
  apiurl: devConfig.projectRoot + 'index/api/index',
  // 登录首页地址
  login: devConfig.projectRoot + 'index/index/login',
  courseData: (_ => {
    let reg = /^(?:([A-Za-z]+):)?(\/{0,3})([0-9.\-A-Za-z]+)(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
    let str = devConfig.courseData;
    let result = reg.exec(str);
    return {
      'url':result[0],
      'scheme':result[1],
      'slash':result[2],
      'host':result[3],
      'port':result[4] || '80',
      'path':result[5],
      'query':(str => {
        if (str.length === 0) {return {}};
        let params = {};
        for (let i = 0; i < str.length; i++) {
          let val = str[i].split('=');
          if (val.length != 2) {continue;}
          params[val[0]] = decodeURIComponent(val[1].replace(/\+/g, ' '));
        }
        return params;
      })(result[6].split('&')),
      'hash':result[7]
    }
  })(),
  optionsTab: 'ABCDEFGHIGKLMNOPQRSTUVWXYZ',
  // 审核状态
  checkList: [
    {
      id: '',
      name: '全部'
    },
    {
      id: '1',
      name: '可用'
    },{
      id: '2',
      name: '审核未通过'
    },{
      id: '3',
      name: '申请审核'
    },{
      id: '4',
      name: '创建完成'
    },{
      id: '5',
      name: '调整完成'
    }
  ],
  // 使用权限
  qusepermissList: [
    {
      value: '0',
      name: '通用'
    },{
      value: '1',
      name: '仅个人'
    },{
      value: '2',
      name: '仅考试'
    }
  ],
  // 题型
  bankTypeList: [
    {
      value: "singlechoice",
      name: "单选题"
    },
    {
      value: "multiplechoice",
      name: "多选题"
    },
    {
      value: "truefalse",
      name: "判断题"
    },
    {
      value: "shortanswer",
      name: "填空题"
    },
    {
      value: "match",
      name: "匹配题"
    },
    {
      value: "essay",
      name: "简答题"
    },
    {
      value: "comprehensive",
      name: "综合解答题"
    },
    {
      value: "readingcomprehension",
      name: "阅读理解"
    }
  ],
  // 难易程度
  complexityList: [
    {
      name: "容易",
      value: '1'
    },{
      name: "适中",
      value: '2'
    },{
      name: "困难",
      value: '3'
    }
  ]
};

Vue.prototype.mytools = {
  //  清除搜索对象中值为空的属性
  filterParams (obj) {
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
let sess_auth_token = window.sessionStorage.getItem('auth_token');
axios.defaults.headers.common['Authorization'] = sess_auth_token ? sess_auth_token : devConfig.def_auth_token;

// 添加请求拦截器，请求发送之前
axios.interceptors.request.use(config => {
  return config;
}, error => {
  return Promise.reject(error);
});
// 添加响应拦截器，浏览器接收之前
axios.interceptors.response.use(response => {
  if (response.data.status == 0) {
    window.sessionStorage.setItem('auth_token', response.data.auth_token);
  } else if (response.data.status == 9999) {
    window.location.href = devConfig.projectRoot + "index/index/login";
  }
  return response;
}, error => {
  return Promise.reject(error);
});
Vue.prototype.$ajax = axios;

Vue.config.productionTip = false;

// 注册全局组件
// ueditor
import VueUEditor from 'vue-ueditor';
Vue.component('VueUEditor',VueUEditor);

new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
});

// 公共样式
require('./style/common/reset.css');
require('./style/common/common.css');
