/**
 * 配置目录
 */

 const config = {
  
  // 开发环境下的配置
  devConfig: {
    // 当前登录的用户信息
    userMsg: '{"uid":"B608AA535E0EC9C5A7BC8548AF39D4CC","username":"admin","idnumber":"123456","pid":"B608AA535E0EC9C5DDBC8548AF39D4CB","address":"高新西区百叶路一号","picture":null,"description":"管理员","education":"4","enter_time":2015,"logintime":1537260682,"org_name":"电子科技大学成都学院","role":"系统管理员"}', // window.localStorage.getItem('userMsg')
    // 项目目录
    projectRoot: 'http://localhost:80/edu/index.php/',// window.localStorage.getItem('ProjectRoot')
    // token
    def_auth_token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNTM5NTcyNTU5IiwiZXhwIjoxNTY5MDMzNjc3LCJuYmYiOiIxNTM5NTcyNTU5IiwianRpIjoiMzQ1a2V5IiwidWlkIjoiQjYwOEFBNTM1RTBFQzlDNUE3QkM4NTQ4QUYzOUQ0Q0MiLCJ1c2VybmFtZSI6ImFkbWluIiwiaWRudW1iZXIiOiIxMjM0NTYiLCJwaWQiOiJCNjA4QUE1MzVFMEVDOUM1RERCQzg1NDhBRjM5RDRDQiIsImFkZHJlc3MiOiLpq5jmlrDopb_ljLrnmb7lj7bot6_kuIDlj7ciLCJwaWN0dXJlIjpudWxsLCJkZXNjcmlwdGlvbiI6IueuoeeQhuWRmCIsImVkdWNhdGlvbiI6IjQiLCJlbnRlcl90aW1lIjoyMDE1LCJsb2dpbnRpbWUiOiIyMDE4LTEwLTE1IDA5OjQ4OjAyIn0.8nPOByqQP6eQVyJOjZCscskLKSv8quh7-xQAQ3Muz1c',// window.sessionStorage.getItem('auth_token')
    courseData: 'http://localhost/edu/public/edumodel/questionbank/?d=C9036CA7A41C95D9B919BC990A4E67B4#/index',// window.location.href
  },
  /*
  // 生产模式下的配置
  devConfig: {
    userMsg: window.localStorage.getItem('userMsg'),
    projectRoot: window.localStorage.getItem('ProjectRoot'),
    def_auth_token: window.sessionStorage.getItem('auth_token'),
    courseData: window.location.href
  },
  */
  ueditor_default: {
    editorConTigan: '<p>请填写题干内容</p>',
    editorAnalysis: '<p>请填写题目考察的知识方向、解题思路等信息，便于后期交流、讨论。不会显示给考生。（默认：此题暂无题目解析）</p>'
  }
  
 };

 module.exports = config;