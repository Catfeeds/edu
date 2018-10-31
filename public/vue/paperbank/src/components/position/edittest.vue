<template>
    <div class="bankindex paperbank">
        <div id="editpaper">
            <div class="left">
                <div class="random-choice">
                    <span>随机选择试题</span>
                </div>
                <div class="manually-choice">
                    <span>手动选择试题</span>
                </div>
                <div class="selected">
                    <div class="choice-question">当前所选试题
                        <span>{{ selQuestionList.length }}</span>道</div>
                </div>
                <div class="submit-btn">
                    <span>提交</span>
                </div>
                <div class="showQuestion">
                    <span>查看所选试题</span>
                </div>
            </div>
            <div class="center">
                <div class="content">
                    <div class="title">
                        <div class="classify clearfix">
                            <span class=" fl">从题库中选题</span>
                        </div>
                    </div>
                    <div class="body">
                        <div class="nav">
                            <div class="search-content">
                                <div class="part part-1">
                                    <div class="type">
                                        <span>题目类型：</span>
                                        <div class="neirong">
                                            <el-select v-model="search.type" clearable placeholder="请选择">
                                                <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <div class="number">
                                        <span>试题编号：</span>
                                        <div class="neirong">
                                            <el-input v-model="search.number" placeholder="请输入内容"></el-input>
                                        </div>

                                    </div>
                                    <div class="key">
                                        <span>题干关键字：</span>
                                        <div class="neirong">
                                            <el-input v-model="search.keyword" placeholder="请输入内容"></el-input>
                                        </div>
                                    </div>
                                </div>
                                <div class="part part-2">
                                    <div class="type">
                                        <span>难度：</span>
                                        <div class="neirong">
                                            <el-select v-model="search.difficulty" clearable placeholder="请选择">
                                                <el-option v-for="item in difficulty" :key="item.value" :label="item.label" :value="item.value">
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <div class="number">
                                        <span>标签：</span>
                                        <div class="neirong">
                                            <el-input v-model="search.tag" placeholder="请输入内容"></el-input>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="search">
                                <div class="click-butn">
                                    <el-button type="primary">搜索试题</el-button>
                                </div>
                            </div>
                        </div>
                        <div class="main">
                            <div class="title">
                                <div class="capital">
                                    <div class="checkbox-all">
                                        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
                                    </div>
                                    <div class="others">
                                        <div ref="yuansu" class="pagenumbers">
                                            <div class="discripe">共
                                                <span class="all">{{ paperList.length }}</span>项查询结果
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="body-test">
                                <ul class="que">
                                    <li class="que-li" v-for="choice in paperList" :class="{clickon: choice.isSel}" v-on:click="swichchapters(choice.id)">
                                        <div class="tit">
                                            <el-checkbox @change="fnDealSelQuestion(choice)" v-model="choice.isSel">
                                                <i>1.</i>
                                                <span class="type">判断题</span>
                                                <i>|</i>
                                                <span class="cont">{{ choice.name }}</span>
                                            </el-checkbox>
                                        </div>
                                        <div class="bod">
                                            <div class="a list">
                                                <span>A.</span>
                                                <span>{{ choice.choiceA }}</span>
                                                <span class="isRight">[正解]</span>
                                            </div>
                                            <div class="b list">
                                                <span>B.</span>
                                                <span>{{ choice.choiceB }}</span>
                                            </div>
                                        </div>
                                        <div class="otherDiscript">
                                            <div class="diff">
                                                <span>难度：</span>
                                                <span>4</span>
                                            </div>
                                            <div class="score">
                                                <span>默认分值：</span>
                                                <span>5</span>
                                                <i>|</i>
                                            </div>
                                            <div class="num">
                                                <span>试题编号：</span>
                                                <span>14398643</span>
                                                <i>|</i>
                                            </div>

                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="icon-gotop" :class="{goTop: isTop}">
                        <i class="iconfont" id="iconfont" v-on:click="goBacktop()">&#xe61b;</i>
                        <span>回到顶部</span>
                    </div>
                </div>
                <!--分页 start-->
                <div class="block">
                    <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page="currentPage" :page-sizes="pagesizes" :page-size="pagesize" layout="total, sizes, prev, pager, next, jumper" :total="totals">
                    </el-pagination>
                </div>
                <!--分页 end -->
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        let thisVue = this;
        return {
            search: {
                type: null,
                number: null,
                keyword: null,
                difficulty: null,
                tag: null
            },
            options: [{
                value: '23123123',
                label: '选择题'
            }, {
                value: '124342342',
                label: '填空题'
            }, {
                value: '12312131',
                label: '问答题'
            },],
            //难度选择
            difficulty: [{
                value: '选项1',
                label: '难度A'
            }, {
                value: '选项2',
                label: '难度B'
            }, {
                value: '选项3',
                label: '难度C'
            },],
            selTag: 11,
            paperList: [
                {
                    id: '11',
                    name: '判断题',
                    choiceA: '是',
                    choiceB: '否',
                    author: '',
                    create: '',
                    nmber: '',
                    fenzhi: '',
                    isSel: false
                },
                {
                    id: '22',
                    name: '判断题',
                    choiceA: '是',
                    choiceB: '否',
                    isSel: false
                }],
            //复选框
            checkAll: false,
            isIndeterminate: false,
            checked: false,
            //题数选择
            topicNumber: [{
                value: '选项1',
                label: '每页15条'
            }, {
                value: '选项2',
                label: '每页10条'
            }, {
                value: '选项3',
                label: '每页5条'
            },],
            value: '',
            // 选中的题目id数组
            selQuestionList: [],
            //回到顶部
            isTop: false,
            //分页
            pagesizes: [
                5,
                10,
                20,
                30,
                40
            ],
            totals: 40,
            pagesize: 10,
            currentPage: 4
        }
    },
    mounted() {
        console.log(this.$refs);

        var _this = this
        //当滚动条的位置处于距顶部100像素以上时，跳转链接出现，否则消失
        $(window).scroll(function() {
            if ($(window).scrollTop() > 200) {
                _this.isTop = true
                //$("#iconfong").fadeIn();
                console.log('aaa')
            }
            else {
                //$("#iconfont").fadeOut();
                _this.isTop = false
                console.log('bbb')
            }
        });
    },
    methods: {
        //试卷类型选择
        swichchapters: function(id) {
            this.selTag = id;
            //console.log(this.selTag)
        },
        //复选框
        handleCheckAllChange(val) {
            /* if (this.checkAll == false) {
                this.isIndeterminate = false;
                this.checked = false;
            }if (this.checkAll == true) {
                this.isIndeterminate = true;
                this.checked = true;
            } */
            if (this.checkAll) {
                this.paperList = this.paperList.map((val) => {
                    val.isSel = true;
                    return val;
                });
                this.selQuestionList = this.paperList.map((val) => {
                    return val.id;
                });
            } else {
                this.paperList = this.paperList.map((val) => {
                    val.isSel = false;
                    return val;
                });
                this.selQuestionList = [];
            }
            this.isIndeterminate = false;
        },
        // 处理选中的题目
        fnDealSelQuestion(question) {
            if (question.isSel) {
                this.selQuestionList.push(question.id);
            } else {
                /* for (var i = 0; i < this.selQuestionList.length; i++) {
                    if (this.selQuestionList[i] === question.id) {
                        this.selQuestionList.splice(i, 1);
                        break;
                    }
                } */
                this.selQuestionList = this.selQuestionList.filter((val) => {
                    return (val !== question.id) ? true : false;
                });
            }
            if (this.selQuestionList.length && this.selQuestionList.length < this.paperList.length) {
                this.isIndeterminate = true;
            } else if (this.selQuestionList.length === this.paperList.length) {
                this.isIndeterminate = false;
                this.checkAll = true;
            } else {
                this.isIndeterminate = false;
                this.checkAll = false;
            }
        },
        //分页处理
        TODO() {
            // 1. 分页
            // 2. 全选的三种状态
            // 3. 回到顶部的效果
            // 4. 
        },
        //回到顶部
        goBacktop: function() {
            if ($('html').scrollTop()) {
                $('html').animate({ scrollTop: 0 }, 1000);
                return false;
            }
            $('body').animate({ scrollTop: 0 }, 1000);
            return false;
        },
        //页码显示
        handleSizeChange(val) {
            console.log(`每页 ${val} 条`);
        },
        handleCurrentChange(val) {
            console.log(`当前页: ${val}`);
        }
    },
    //监听
    watch: {
        questionListNumber(val, oldval) {
            //console.log(val, oldval)
            console.log('aaa')
        }
    }
}
</script>

<style lang="scss">
/* css预编译语言 */

/*添加试卷样式*/

#editpaper {
    width: 100%;
    height: auto;
    margin: 0 auto;
    .left {
        position: relative;
        width: 180px;
        height: auto;
        float: left;
        margin-top: 120px;
        margin-right: 20px;
        color: white;
        .random-choice {
            cursor: pointer;
            height: 40px;
            line-height: 40px;
            background: orange;
            text-align: center;
        }
        .manually-choice {
            cursor: pointer;
            height: 40px;
            line-height: 40px;
            background: #2ABCB8;
            text-align: center;
            margin-top: 5px;
        }
        .selected {
            cursor: pointer;
            position: relative;
            height: 75px;
            background: orange;
            text-align: center;
            margin-top: 70px;
            .choice-question {
                line-height: 75px;
                span {
                    font-size: 2.0rem;
                }
            }
        }
        .submit-btn {
            cursor: pointer;
            position: relative;
            height: 30px;
            background: #2ABCB8;
            line-height: 30px;
            text-align: center;
            margin-top: 5px;
        }
        .showQuestion {
            cursor: pointer;
            position: relative;
            height: 30px;
            background: red;
            line-height: 30px;
            text-align: center;
            margin-top: 5px;
        }
    }
    .center {
        position: relative;
        /*width: 72%;*/
        width: 1000px;
        height: auto;
        margin: 0 auto;
        float: left;
        .content {
            background: #fff;
            height: auto;
            margin-top: 20px;
            padding: 20px;
            .title .classify {
                height: 50px;
                line-height: 50px;
                border-bottom: 1px solid gray;
                span {
                    position: relative;
                    /* z-index: 100; */
                    padding: 5px 20px;
                    font-size: 1.5rem;
                    font-weight: bold;
                }
            }
            .body {
                position: relative;
                width: 100%;
                top: 15px;
                margin: 0 auto;
                .nav {
                    position: relative;
                    width: 100%;
                    height: auto;
                    border: 1px solid #F0F0F7;
                    background: #F5F5F6;
                    margin: 0 auto;
                    .search-content {
                        position: relative;
                        width: 95%;
                        height: 130px;
                        margin: 0 auto;
                        .part-1 {
                            width: 100%;
                            height: 40px;
                            margin-top: 20px;
                        }
                        .part {
                            .type {
                                float: left;
                                width: 200px;
                                height: 40px;
                                line-height: 40px;
                                margin-left: 10px;
                            }
                            .neirong {
                                float: right;
                                width: 110px;
                            }
                            span {
                                float: left;
                                /* margin: 10px; */
                                height: 40px;
                                line-height: 40px;
                            }
                        }
                    }
                }
                .main {
                    position: relative;
                    width: 100%;
                    /*border: 1px solid #F0F0F7;*/
                    margin: 0 auto;
                    .title {
                        position: relative;
                        width: 100%;
                        height: 55px;
                        background: white;
                        margin: 0 auto;
                        .capital {
                            position: relative;
                            width: 100%;
                            height: 55px;
                            margin-top: 10px;
                            line-height: 55px;
                            .checkbox-all {
                                width: 70px;
                                float: left;
                            }
                            .others {
                                position: relative;
                                float: right;
                                .pagenumbers {
                                    position: relative;
                                    margin-right: 25px;
                                    float: left;
                                    .all {
                                        color: #29BDB9;
                                        font-weight: bold;
                                        margin: 5px;
                                    }
                                }
                                .pagechoice {
                                    float: right;
                                }
                            }
                        }
                    }
                    .body-test .que .que-li {
                        border: 1px solid #F0F0F7;
                        margin-top: 5px;
                        cursor: pointer;
                        .tit {
                            /* height: 40px; */
                            background: #FAFAFC;
                            line-height: 40px;
                            i {
                                margin-left: 10px;
                            }
                            .type {
                                color: #28BDB9;
                            }
                            .cont {
                                /* margin-left: 10px; */
                                width: 630px;
                                /*display: block;
                                margin-left: 125px;
                                margin-top: -41px;*/
                            }
                        }
                        .bod .list {
                            margin-left: 100px;
                            margin-top: 8px;
                            span:nth-child(2) {
                                margin-left: 12px;
                            }
                            .isRight {
                                margin-left: 25px;
                                color: orange;
                            }
                        }
                        .otherDiscript {
                            height: 40px;
                            width: 100%;
                            color: #B3AEB7;
                            font-size: 0.1rem;
                        }
                        .otherDiscript>div {
                            float: right;
                            position: relative;
                            margin-right: 10px;
                        }
                        .otherDiscript i {
                            margin: 5px 5px;
                        }
                    }
                }
            }
        }
        .block {
            height: 50px;
            position: relative;
            bottom: -55px;
            text-align: right;
        }
    }
    .el-checkbox__inner {
        position: relative;
        margin-left: 10px;
    }
    /*搜索内容样式 start*/
    .el-select {
        width: 110px;
        height: 40px;
    }
    .el-input {
        width: 110px !important;
    }
    .center .content .body {
        .nav {
            .search-content {
                .part {
                    .number {
                        float: left;
                        width: 200px;
                        height: 40px;
                        line-height: 40px;
                        /* margin-top: 20px; */
                        margin-left: 25px;
                    }
                    .key {
                        float: left;
                        width: 200px;
                        height: 40px;
                        line-height: 40px;
                        /* margin-top: 20px; */
                        margin-left: 25px;
                    }
                }
                .part-2 {
                    width: 100%;
                    height: 40px;
                    margin-top: 10px;
                }
            }
            .search .click-butn {
                position: relative;
                width: 100px;
                margin: 0 auto;
                margin-top: -15px;
                margin-bottom: 10px;
            }
        }
        .main .body-test .que .clickon {
            border: 1px solid #29BDB9;
        }
    }

    .el-button--primary {
        color: #fff;
        background-color: #29BDB9;
        border-color: #29BDB9;
    }
    .el-button {
        padding: 8px 18px;
    }

    /*搜索内容样式 end*/

    /* 回到顶部样式*/

//图标样式
.icon-gotop {
    position: fixed;
    bottom: 120px;
    right: 10px;
    cursor: pointer;
    display: none;
    height: 40px;
    span {
        color: gray;
        display: block;
        margin-left: -10px;
    }
}

.iconfont {
    font-family: "iconfont" !important;
    font-size: 30px;
    font-style: normal;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    color: gray;
}

.goTop {
    display: block;
}
}

@font-face {
    font-family: 'iconfont';
    /* project id 666106 */
    src: url('https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.eot');
    src: url('https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.eot?#iefix') format('embedded-opentype'),
    url('https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.woff') format('woff'),
    url('https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.ttf') format('truetype'),
    url('https://at.alicdn.com/t/font_666106_t6a30nu9cjj4te29.svg#iconfont') format('svg');
}


</style>