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
  userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537260682,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem(userMsg)
  projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
  def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNTM5MzI1NzM4IiwiZXhwIjoxNTM5MzMxNzM4LCJuYmYiOiIxNTM5MzI1NzM4IiwianRpIjoiMzQ1a2V5IiwidWlkIjoiQjYwOEFBNTM1RTBFQzlDNUE3QkM4NTQ4QUYzOUQ0Q0MiLCJ1c2VybmFtZSI6ImFkbWluIiwiaWRudW1iZXIiOiIxMjM0NTYiLCJwaWQiOiJCNjA4QUE1MzVFMEVDOUM1RERCQzg1NDhBRjM5RDRDQiIsImFkZHJlc3MiOiLpq5jmlrDopb_ljLrnmb7lj7bot6_kuIDlj7ciLCJwaWN0dXJlIjpudWxsLCJkZXNjcmlwdGlvbiI6IueuoeeQhuWRmCIsImVkdWNhdGlvbiI6IjQiLCJlbnRlcl90aW1lIjoyMDE1LCJsb2dpbnRpbWUiOiIyMDE4LTEwLTEwIDE1OjU3OjI0In0.fE7jexmA41HGkHtN7ZNDXgrNHQ9kvxtQe0O1oXSA0FY',// window.sessionStorage.getItem('auth_token')
  courseData: 'http://localhost/edu/public/edumodel/paperbank/?course=C9036CA7A41C95D9B919BC990A4E67B4#/index',// window.location.href
};

/*
// 线上模式下的配置
const devConfig = {
  userMsg: window.localStorage.getItem('userMsg'),
  projectRoot: window.localStorage.getItem('ProjectRoot'),
  def_auth_token: window.sessionStorage.getItem('auth_token'),
  courseData: window.location.href,
};
*/

// 全局变量
Vue.prototype.Const = {
  // 登录用户信息
  userMsg: devConfig.userMsg ? JSON.parse( devConfig.userMsg ) : null,
  // 后台请求地址
  apiUrl: devConfig.projectRoot + 'index/api/index',
  // 登录首页地址
  login: devConfig.projectRoot + 'index/index/login',
  STATIC: devConfig.projectRoot ? devConfig.projectRoot.replace('index.php/', 'public/edumodel/examine/') : '',
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
  papercheckList: [
    {
      value: '',
      name: '全部'
    },{
      value: '1',
      name: '创建成功'
    },{
      value: '2',
      name: '正在创建'
    },{
      value: '3',
      name: '正在考试'
    },{
      value: '4',
      name: '考试结束'
    },{
      value: '5',
      name: '正在审核'
    },{
      value: '6',
      name: '审核通过'
    },{
      value: '7',
      name: '审核未通'
    },
  ],
  // 试卷用途
  paperexamtypeList: [
    {
      value: '0,1',
      name: '全部'
    },{
      value: '0',
      name: '测试'
    },{
      value: '1',
      name: '考试'
    },
  ],
  // 审核状态
  checkList: [
    {
      value: '',
      name: '全部'
    },
    {
      value: '1',
      name: '可用'
    },{
      value: '2',
      name: '审核未通过'
    },{
      value: '3',
      name: '申请审核'
    },{
      value: '4',
      name: '创建完成'
    },{
      value: '5',
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
      name: "所有",
      value: ''
    }, {
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


// axios 改写为 Vue 的原型属性,方便全局调用
axios.defaults.timeout = 1000 * 100;
// let sess_auth_token = window.sessionStorage.getItem('auth_token');
// axios.defaults.headers.common['Authorization'] = sess_auth_token ? sess_auth_token : devConfig.def_auth_token;

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
  if (response.data.status == 0) {
    window.sessionStorage.setItem('auth_token', response.data.auth_token);
  } else if (response.data.status == 9999) {
    alert(response.data.data);
    window.location.href = devConfig.projectRoot + "index/index/login";
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
