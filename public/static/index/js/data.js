// 模拟数据
var userDatas = [{
        loginMsg: {
            name: 'xs'
        },
        privilege: {
            "HOME": {
                "HISTORY": {
                    "HISTORY": "105",
                    "VIEWENTRY": "106"
                },
                "COMMON": {
                    "GETAREAS": "91",
                    "GETORGBYAREA": "92",
                    "GETORGBYORGID": "93"
                },
                "ENTRY": {
                    "BASE2": "109",
                    "SAVEBASE2": "110",
                    "SUBMITBASE2": "111",
                    "JUDGEPRICE": "112",
                    "BASE3": "129",
                    "SAVEBASE3": "130",
                    "SUBMITBASE3": "131",
                    "BASE4": "142"
                }
            }
        },
        courses: {
            studyCourse: [{
                    courseId: 'z110',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: 'javascript',
                    gtasks: '1',
                    readed: '10'
                },
                {
                    courseId: 'z111',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: '大数据',
                    gtasks: '2',
                    readed: '13'
                }
            ]
        }
    },
    {
        loginMsg: {
            name: 'ls'
        },
        privilege: {
            "HOME": {
                "HISTORY": {
                    "HISTORY": "105",
                    "VIEWENTRY": "106"
                },
                "COMMON": {
                    "GETAREAS": "91",
                    "GETORGBYAREA": "92",
                    "GETORGBYORGID": "93"
                },
                "ENTRY": {
                    "BASE2": "109",
                    "SAVEBASE2": "110",
                    "SUBMITBASE2": "111",
                    "JUDGEPRICE": "112",
                    "BASE3": "129",
                    "SAVEBASE3": "130",
                    "SUBMITBASE3": "131",
                    "BASE4": "142",
                    "SUBMITBASE4": "143",
                    "SAVEBASE4": "144",
                    "SUBMITBASE1": "209",
                    "SAVEBASE1": "210",
                    "BASE1": "211"
                }
            }
        },
        courses: {
            teachCourse: [{
                    courseId: 'c100',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: 'C#语言',
                    gtasks: '1',
                    readed: '10'
                },
                {
                    courseId: 'c101',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: 'PHP',
                    gtasks: '2',
                    readed: '13'
                },
                {
                    courseId: 'c102',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: '数据结构',
                    gtasks: '6',
                    readed: '11'
                },
                {
                    courseId: 'c103',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: 'JAVA',
                    gtasks: '14',
                    readed: '17'
                }
            ],
            studyCourse: [{
                    courseId: 'z110',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: 'javascript',
                    gtasks: '1',
                    readed: '10'
                },
                {
                    courseId: 'z111',
                    courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                    courseName: '大数据',
                    gtasks: '2',
                    readed: '13'
                }
            ],
            otherCourse: [{
                courseId: 'z121',
                courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
                courseName: '网络营销',
                gtasks: '2',
                readed: '13'
            }]
        }

    },
    {
        loginMsg: {
            name: 'jw'
        },
        privilege: {
            "HOME": {
                "HISTORY": {
                    "HISTORY": "105",
                    "VIEWENTRY": "106"
                },
                "COMMON": {
                    "GETAREAS": "91",
                    "GETORGBYAREA": "92",
                    "GETORGBYORGID": "93"
                },
                "ENTRY": {
                    "BASE2": "109",
                    "SAVEBASE2": "110",
                    "SUBMITBASE2": "111",
                    "JUDGEPRICE": "112",
                    "BASE3": "129",
                    "SAVEBASE3": "130",
                    "SUBMITBASE3": "131",
                    "BASE4": "142",
                    "SUBMITBASE4": "143",
                    "SAVEBASE4": "144",
                    "SUBMITBASE1": "209",
                    "SAVEBASE1": "210",
                    "BASE1": "211"
                },
                "STATISTICS": {
                    "STATISTICSACCOUNT": "192"
                }
            }
        }
    }
];
// 课程数据
var courselibDatas = [
    {
        id: '1111',
        courseFullName: 'PHP程序设计--从入门到放弃',
        courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
        courseShortName: 'PHP',
        courseSummary: 'web应用程序开发，php世界上最好的语言。让后我就不知道说什么了',
        courseTime: '32',
        courseIdNumber: 'c100',
        courseCate: '必修课',
        chapterDatas: [
                {
                    id: '1000',
                    chapterId: 'ch01',
                    sort: '1',
                    chapterTitle: 'thinkphp 5.0 的安装及配置',
                    knobble: [
                        {'id': '1001','name': '关于MVC', 'sort': '1', 'summary':''},
                        {'id': '1002','name': 'Thinkphp的安装', 'sort': '2', 'summary':''},
                        {'id': '1003','name': 'Thinkphp目录介绍', 'sort': '3', 'summary':''},
                        {'id': '1004','name': '模块设计', 'sort': '4', 'summary':''},
                        {'id': '1005','name': '惯例配置', 'sort': '5', 'summary':''},
                        {'id': '1006','name': '应用配置', 'sort': '6', 'summary':''}
                    ]
                },
                {
                    id: '2000',
                    chapterId: 'ch02',
                    sort: '2',
                    chapterTitle: 'URL和路由',
                    knobble: [
                        {'id': '2001','name': '入口文件', 'sort': '1', 'summary':''},
                        {'id': '2002','name': '隐藏入口文件', 'sort': '2', 'summary':''},
                        {'id': '2003','name': '入口文件的绑定', 'sort': '3', 'summary':''},
                        {'id': '2004','name': '路由', 'sort': '4', 'summary':''}
                    ]
                },
                {
                    id: '3000',
                    chapterId: 'ch03',
                    sort: '3',
                    chapterTitle: '请求和相应',
                    knobble: [
                        {'id': '3001','name': '请求对象获取', 'sort': '1', 'summary':''},
                        {'id': '3002','name': '请求对象参数获取', 'sort': '2', 'summary':''},
                        {'id': '3003','name': 'input助手函数', 'sort': '3', 'summary':''},
                        {'id': '3004','name': '响应对象', 'sort': '4', 'summary':''}
                    ]
                }
            ]
    },
    {
        id: '2222',
        courseFullName: 'HTML5/CSS3程序设计与实践',
        courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
        courseShortName: 'HTML5/CSS3入门',
        courseSummary: 'html5标签语言',
        courseTime: '18',
        courseIdNumber: 'c101',
        courseCate: '选修课'
    },
    {
        id: '3333',
        courseFullName: 'JAVA高级程序设计',
        courseImg: 'http://localhost/edu/public/static/index/images/main/class_pic.png',
        courseShortName: 'java程序设计',
        courseSummary: 'java高级程序设计就是很牛逼的java编程',
        courseTime: '64',
        courseIdNumber: 'c102',
        courseCate: '体育课'
    }
];
// 章节数据
var chapterDatas = [
    {
        chapterId: 'ch01',
        chapterTitle: 'thinkphp 5.0 的安装及配置',
        knobble: ['关于MVC', 'Thinkphp的安装', 'Thinkphp目录介绍', '模块设计', '惯例配置', '应用配置']
    },
    {
        chapterId: 'ch02',
        chapterTitle: 'URL和路由',
        knobble: ['入口文件', '隐藏入口文件', '入口文件的绑定', '路由']
    },
    {
        chapterId: 'ch03',
        chapterTitle: '请求和相应',
        knobble: ['请求对象获取', '请求对象参数获取', 'input助手函数', '响应对象']
    }
];

/*
var a = {
    "status": 0,
    "data": {
        "uid": "B608AA535E0EC9C5A7BC8548AF39D4CD",
        "username": "qw",
        "idnumber": "123",
        "department": "123",
        "pid": "123",
        "address": "123",
        "picture": "123",
        "description": "123",
        "education": "",
        "profession": "",
        "enter_time": 0,
        "logintime": 1499846999,
        "access_list": {
            "QQQQQQQQW": []
        }
    }
};*/
