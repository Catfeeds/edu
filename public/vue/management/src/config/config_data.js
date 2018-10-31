import Vue from 'vue';



const devConfig = {
  userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537260682,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem(userMsg)
  projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
  def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOjE1MzcyNjA3MDAsImV4cCI6MTUzNzI2MTMwMCwibmJmIjoxNTM3MjYwNzAwLCJqdGkiOiIzNDVrZXkiLCJ1aWQiOiJCNjA4QUE1MzVFMEVDOUM1QTdCQzg1NDhBRjM5RDRDQyIsInVzZXJuYW1lIjoiYWRtaW4iLCJpZG51bWJlciI6IjEyMzQ1NiIsInBpZCI6IkI2MDhBQTUzNUUwRUM5QzVEREJDODU0OEFGMzlENENCIiwiYWRkcmVzcyI6IumrmOaWsOilv-WMuueZvuWPtui3r-S4gOWPtyIsInBpY3R1cmUiOm51bGwsImRlc2NyaXB0aW9uIjoi566h55CG5ZGYIiwiZWR1Y2F0aW9uIjoiNCIsImVudGVyX3RpbWUiOjIwMTUsImxvZ2ludGltZSI6MTUzNzI2MDY4Mn0.DnuvHqjQ3-nx9EDPiFTwYypiDea8MY8s5Wp0uvJ6F_I',// window.sessionStorage.getItem('auth_token')
};



// 全局变量
Vue.prototype.Const = {
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
  },
  // 平铺多维数组
  flattenArr (arr) {
    // 平铺二维数组
    const flattened = [].concat(...arr);
    // 迭代平铺多维
    return flattened.some(item => Array.isArray(item)) ? flattenArr(flattened) : flattened;
  }
};


// 调试模式下的配置
// module.exports =  devConfig;