<template>
  <div id="addbank">
    <!-- <router-link to="/file">测试文件</router-link> -->
    <div class="title">
      <span v-if="!isEdit"><i class="el-icon-d-arrow-right"></i>添加试题</span>
      <span v-else><i class="el-icon-d-arrow-right"></i>修改试题</span>
      <div class="fr"><el-button @click="goback" round type="primary" plain>返回</el-button></div>
    </div>
    <section class="add-bank-wrap">
      <div class="bank-type line">
        <div class="item clearfix">
          <p><i class="icon bitian">*</i>题型:</p>
          <div class="btn-op">
            <el-radio v-for="(item, index) in bankTypeList" :key="index" @change="bankTypeChange" v-model="bankType" :label="item.value" border>{{ item.name }}</el-radio>
          </div>
        </div>
        <div class="item clearfix">
          <p><i class="icon bitian">*</i>使用权限:</p>
          <div class="btn-op">
            <el-select v-model="uselimits" placeholder="规定题目使用权限">
              <el-option v-for="(item,index) in uselimitsList" :key="index" :label="item.name" :value="item.value"></el-option>
            </el-select>
          </div>
        </div>
        <div class="item clearfix">
          <p><i class="icon bitian">*</i>知识点:</p>
          <div class="btn-op">
            <el-cascader placeholder="搜索" :options="sectionList" @change="sectionsChange" v-model="sections" :props="sectionProps" filterable change-on-select></el-cascader>
          </div>
        </div>
          <!-- <el-form-item label="学历" prop="education">
            <el-select v-model="userone.education">
              <el-option v-for="(item, index) in educationList" :label="item.name" :value="item.value" :key="index"></el-option>
            </el-select>
          </el-form-item> -->
      </div>
      <div class="bank-topic line">
        <div class="item clearfix">
          <p class="fl"><i class="icon bitian">*</i>题干:</p>
          <div class="myeditor fl">
            <VueUEditor :ueditorPath="ueditorRoot" @ready="editorReadyTiGan"></VueUEditor>
            <yk-upload :object="editorConTiganFile" :limit="upload.limit" :max-file-size="upload.maxFileSize" :on-select="handleSelected" :on-delete-old="handleDeleteOld"></yk-upload>
          </div>
        </div>
        <div class="item clearfix">
          <!-- 单选题 -->
          <div v-if="bankType == 'singlechoice'" ref="edAnsRadio" class="sel-answer-warp">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案项:</p>
              <div class="answer-wrap fl">
                <div class="myeditor answer-ed clearfix" v-for="(item, index) in editorConAnswerRadio.list" :key="index">
                  <el-radio :label="index+1" v-model="editorConAnswerRadio.selAnswer" @change="editorConAnswerRadio.selAnswer = item.id;"></el-radio>
                  <div class="option">
                    <el-input placeholder="请填写正确答案" type="textarea" v-model="item.name"></el-input>
                    <yk-upload :object="item" :limit="upload.answerlimit" :max-file-size="upload.maxFileSize" :on-select="handleSelected" :on-delete-old="handleDeleteOld"></yk-upload>
                    <i title="移除" @click="deleteAnwserRadio(item)" class="el-icon-close"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="addanswer">
              <el-button @click="addEditorRadio(editorConAnswerRadio)"><i class="el-icon-plus"></i>添加选项</el-button>
            </div>
          </div>
          <!-- 多选题 / 不定选项 -->
          <div v-if="bankType == 'multiplechoice'" ref="edAnsCheckbox" class="sel-answer-warp">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案项:</p>
              <div class="answer-wrap fl">
                <div class="myeditor answer-ed clearfix" v-for="(item, index) in editorConAnswerCheckbox" :key="index">
                  <el-checkbox :label="item.id" v-model="editorConAnswerCheckbox[index].isTrue"></el-checkbox>
                  <div class="option">
                    <el-input placeholder="请填写正确答案" type="textarea" v-model="editorConAnswerCheckbox[index].value"></el-input>
                    <yk-upload :object="item" limit=1 :max-file-size="upload.maxFileSize" :on-select="handleSelected" :on-delete-old="handleDeleteOld"></yk-upload>
                    <i title="移除" @click="deleteAnwserCheckbox(item)" class="el-icon-close"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="addanswer">
              <el-button @click="addEditorCheckbox"><i class="el-icon-plus"></i>添加选项</el-button>
            </div>
          </div>
          <!-- 填空题 -->
          <div v-if="bankType == 'shortanswer'" class="sel-answer-warp tiankongnum">
            <div class="clearfix">
              <p class="fl"><i class="icon bitian">*</i>填空数量:</p>
              <div class="answer-wrap fl">
                <el-select @change="completionNumChan(editorConAnswerCompletion.num, editorConAnswerCompletion)" v-model="editorConAnswerCompletion.num">
                  <el-option v-for="(item, index) in 10" :key="index" :label="item" :value="item"></el-option>
                </el-select>
              </div>
            </div>
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案项:</p>
              <div class="answer-wrap fl">
                <ul class="clearfix">
                  <li  v-for="(item,index) in editorConAnswerCompletion.list" :key="index" >
                    <span class="num">{{item.id}}</span>
                    <el-input type="textarea" placeholder="请填写正确答案" :rows="2" v-model="item.value"></el-input>
                  </li>
                  <li v-for="( item, index ) in editorConAnswerCompletion.list " :key="index" ></li>
                </ul>
                <span class="mark">{{ editorConAnswerCompletion.answerState }}</span>
              </div>
            </div>
          </div>
          <!-- 判断题 -->
          <div v-if="bankType == 'truefalse'" class="sel-answer-warp ">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>选项:</p>
              <div class="answer-wrap fl">
                <el-radio :key="index" v-model="editorConAnswertrueOfFalse.selAnswer" v-for="(item, index) in editorConAnswertrueOfFalse.list" :label="item.id">{{ item.value }}</el-radio>
              </div>
            </div>
          </div>
          <!-- 简答题 -->
          <div v-if="bankType == 'essay'" class="sel-answer-warp">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案:</p>
              <div class="answer-wrap fl">
                <el-input type="textarea" placeholder="请填写正确答案" :rows="2" v-model="editorConAnswerEssayquestion"></el-input>
              </div>
            </div>
          </div>
          <!-- 综合阅读题 -->
          <div v-if="bankType == 'comprehensive'" ref="readquestion" class="sel-answer-warp readquestion">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案:</p>
              <div class="answer-wrap fl">
                <div class="myeditor clearfix" :key="index" v-for="(item, index) in editorConAnswerReadquestion">
                  <el-input type="textarea" placeholder="问题题干" :rows="2" v-model="item.name"></el-input>
                  <el-input type="textarea" placeholder="问题参考答案" :rows="2" v-model="item.answer"></el-input>
                  <i title="移除" @click="deleteAnwserReadquestion(item)" class="el-icon-close"></i>
                </div>
              </div>
            </div>
            <div class="addanswer">
              <el-button @click="addEditorReadquestion"><i class="el-icon-plus"></i>添加选项</el-button>
            </div>
          </div>
          <!-- 匹配题 -->
          <div v-if="bankType == 'match'" class="sel-answer-warp readquestion">
            <div class="answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案:</p>
              <div class="answer-wrap fl">
                <div class="myeditor clearfix" :key="index" v-for="(item, index) in editorConAnswerMatchquestion">
                  <el-input type="textarea" placeholder="母题" :rows="2" v-model="item.name"></el-input>
                  <el-input type="textarea" placeholder="公题" :rows="2" v-model="item.answer"></el-input>
                  <i title="移除" @click="deleteAnwserMatchquestion(item)" class="el-icon-close"></i>
                </div>
              </div>
            </div>
            <div class="addanswer">
              <el-button @click="addEditorMatchquestion"><i class="el-icon-plus"></i>添加选项</el-button>
            </div>
          </div>
          <!-- 阅读理解 -->
          <div v-if="bankType == 'readingcomprehension'" class="sel-answer-warp readquestion">
            <div class="addanswer">
              <el-button v-if="!editorReadingcomprehension.formatData" @click="editorReadingDialog = true"><i class="el-icon-plus"></i>添加子题目</el-button>
              <el-button v-else @click="editorReadingDialog = true"><i class="el-icon-plus"></i>查看子题目</el-button>
            </div>
          </div>
          <div class="complexity clearfix">
            <p class="fl"><i class="icon bitian">*</i>难易程度:</p>
            <div class="complexity-slider fl">
              <el-radio-group v-model="complexity" size="small">
                <el-radio-button v-for="(item,index) in complexityList" :label="item.value" :key="index">{{ item.name }}</el-radio-button>
              </el-radio-group>
            </div>
          </div>
        </div>
      </div>
      <div class="analysis line">
        <div class="item clearfix">
          <p class="fl">题目解析:</p>
          <div class="myeditor fl">
            <VueUEditor :ueditorPath="ueditorRoot" @ready="editorReadyAnalysis"></VueUEditor>
          </div>
        </div>
      </div>
      <div class="submit">
        <el-button type="primary" @click="fnSubmit" round>提交</el-button>
      </div>
    </section>

    <!-- 阅读理解添加题目弹窗 -->
    <el-dialog title="阅读理解添加题目" :visible.sync="editorReadingDialog" :fullscreen="true">
      <div class="add-type-btn">
        <el-select @change="addReadingQuesSel" placeholder="添加题目" v-model="editorReadingcomprehension.anType">
          <el-option :key="index" v-for="(item, index) in editorReadingcomprehension.anTypeList" :label="item.name" :value="item.value"></el-option>
        </el-select>
      </div>
      <div id="add-reading" class="add-reading">
        <div v-for="(item, index) in editorReadingcomprehension.questionCon" class="ques-item" :key="index">
          <template v-if="item.type === 'singlechoice'">
            <p class="ques-type">单选题</p>
            <div class="add-title">
              <p class="fl"><i class="icon bitian">*</i>题干:</p>
              <el-input placeholder="请填写题干内容" :rows="2" type="textarea" v-model="item.name"></el-input>
              <yk-upload :limit="upload.limit"
                :object="item"
                :max-file-size="upload.maxFileSize"
                :on-select="handleSelected"
                :on-delete-old="handleDeleteOld"></yk-upload>
            </div>
            <div class="add-answer">
              <p class="fl"><i class="icon bitian">*</i>选项:</p>
              <div class="wrap">
                <div class="myeditor answer-ed clearfix" v-for="(item1, index1) in item.list" :key="index1">
                  <el-radio :label="index1+1" v-model="item.selAnswer" @change="item.selAnswer = item1.id;"></el-radio>
                  <div class="option">
                    <el-input placeholder="请填写正确答案" type="textarea" v-model="item1.name"></el-input>
                    <yk-upload
                      :object="item1"
                      :limit="upload.limit"
                      :max-file-size="upload.maxFileSize"
                      :on-select="handleSelected"
                      :on-delete-old="handleDeleteOld"></yk-upload>
                    <i title="移除" @click="deleteAnwserRadioAN(item1, index)" class="el-icon-close"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="addanswer">
              <el-button @click="addEditorRadio(item)"><i class="el-icon-plus"></i>添加选项</el-button>
            </div>
          </template>
          <template v-if="item.type === 'truefalse'">
            <p class="ques-type">判断题</p>
            <div class="add-title">
              <p class="fl"><i class="icon bitian">*</i>题干:</p>
              <el-input :rows="2" placeholder="请输入题干内容" type="textarea" v-model="item.name"></el-input>
              <yk-upload :limit="upload.limit" :object="item" :max-file-size="upload.maxFileSize" :on-select="handleSelected" :on-delete-old="handleDeleteOld"></yk-upload>
            </div>
            <div class="add-answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>选项:</p>
              <div class="answer-wrap">
                <el-radio :key="index1" v-model="item.selAnswer" v-for="(item1, index1) in item.list" :label="item1.id">{{ item1.value }}</el-radio>
              </div>
            </div>
          </template>
          <template v-if="item.type === 'shortanswer'">
            <p class="ques-type">填空题</p>
            <div class="add-title">
              <p class="fl"><i class="icon bitian">*</i>题干:</p>
              <el-input :rows="2" placeholder="请输入题干内容，填空处用（）标识" type="textarea" v-model="item.name"></el-input>
              <yk-upload :limit="upload.limit" :object="item" :max-file-size="upload.maxFileSize" :on-select="handleSelected" :on-delete-old="handleDeleteOld"></yk-upload>
            </div>
            <div class="add-quesnum clearfix">
              <p class="fl"><i class="icon bitian">*</i>填空数量:</p>
              <div class="answer-wrap">
                <el-select @change="completionNumChan(item.num, item)" v-model="item.num">
                  <el-option v-for="(item, index) in 10" :key="index" :label="item" :value="item"></el-option>
                </el-select>
              </div>
            </div>
            <div class="add-answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案项:</p>
              <div class="answer-wrap fl">
                <ul class="clearfix">
                  <li v-for="(item1, index1) in item.list" :key="index1" >
                    <span class="num">{{item1.id}}</span>
                    <el-input placeholder="请填写正确答案" type="textarea" :rows="2" v-model="item1.value"></el-input>
                  </li>
                </ul>
              </div>
            </div>
          </template>
          <template v-if="item.type === 'essay'">
            <p class="ques-type">简答题</p>
            <div class="add-title">
              <p class="fl"><i class="icon bitian">*</i>题干:</p>
              <el-input :rows="2" placeholder="请输入题干内容" type="textarea" v-model="item.name"></el-input>
              <yk-upload
                :limit="upload.limit"
                :object="item"
                :max-file-size="upload.maxFileSize"
                :on-select="handleSelected"
                :on-delete-old="handleDeleteOld"></yk-upload>
            </div>
            <div class="add-answer clearfix">
              <p class="fl"><i class="icon bitian">*</i>答案:</p>
              <div class="answer-wrap fl">
                <el-input placeholder="请填写正确答案" type="textarea" :rows="2" v-model="item.answer"></el-input>
              </div>
            </div>
          </template>
          <i @click="addDeleteItem(item)" title="删除该题目" class="delete-item el-icon-close"></i>
        </div>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button @click="editorReadingDialog = false">取 消</el-button>
        <el-button type="primary" @click="addReadingFormat">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import ykUpload from '@/components/upload';
  import { devConfig, ueditor_default } from '../config';
  window.UEDITOR_HOME_URL = devConfig.projectRoot.replace('index.php/', 'public/static/ueditor/');
  export default {
    data () {
      return {
        // 是否是编辑状态
        isEdit: (this.$route.name == 'edit') ? true : false,
        // 使用权限列表
        uselimitsList: this.Const.qusepermissList,
        // 题型列表
        bankTypeList: this.Const.bankTypeList,
        // 题目难易程度
        complexityList: this.Const.complexityList,
        // 章节
        sectionList: [
          /*{
            id: '123123',
            name: '第一章',
            child: [
              {
                id: '1234534',
                name: '1第一节'
              },{
                id: '1234534',
                name: '1第二节'
              }
            ]
          },{
            id: '123123',
            name: '第二章',
            child: [
              {
                id: '1234534',
                name: '2第一节'
              },{
                id: '1234534',
                name: '2第二节'
              }
            ]
          }*/
        ],
        sectionProps: {
          value: 'id',
          label: 'name',
          children: 'child'
        },
        //选中章节
        sections: [],
        // 当前选中的题型
        bankType: 'singlechoice',
        // 使用权限
        uselimits: '0',
        ueditorRoot: devConfig.projectRoot.replace('index.php/', 'public/static/ueditor/'),
        // 题干编辑器内容
        editorConTigan: ueditor_default.editorConTigan,
        // 题干的文件
        editorConTiganFile: {},
        // 题目解析编辑器内容
        editorAnalysis: ueditor_default.editorAnalysis,
        // 单选题答案选项
        editorConAnswerRadio: {
          list: [
            {
              // 答案内容
              id: 1,
              name: '',
              files_hash: null
            },{
              id: 2,
              name: '',
              files_hash: null
            },{
              id: 3,
              name: '',
              files_hash: null
            },{
              id: 4,
              name: '',
              files_hash: null
            }
          ],
          selAnswer: 1
        },
        // 多选题答案选项
        editorConAnswerCheckbox: [
          {
            id: 1,
            // 答案内容
            value: '',
            fileList: [],
            // 是否正确答案
            isTrue: true
          },{
            id: 2,
            value: '',
            fileList: [],
            isTrue: false
          },{
            id: 3,
            value: '',
            fileList: [],
            isTrue: false
          },{
            id: 4,
            value: '',
            fileList: [],
            isTrue: false
          }
        ],
        // 填空题
        editorConAnswerCompletion: {
          num: 1,
          tiganMsg: '请在题目中用( )表示需要填空的部分。每一个填空单独一个( )表示。',
          list: [
            {
              id: 1,
              value: ''
            },
          ],
          answerState: '说明：考生填写内容须与上方答案完全相同，才能得分。录入答案时，请不要加多余的空格等干扰字符'
        },
        // 判断题
        editorConAnswertrueOfFalse: {
          list: [
            {
              id: 0,
              value: '正确'
            },{
              id: 1,
              value: '错误'
            }
          ],
          selAnswer: 1
        },
        // 简答题
        editorConAnswerEssayquestion: '',
        // 综合阅读题
        editorConAnswerReadquestion: [
          {
            name: '',
            answer: ''
          },{
            name: '',
            answer: ''
          }
        ],
        // 匹配题
        editorConAnswerMatchquestion: [
          {
            name: '',
            answer: ''
          },{
            name: '',
            answer: ''
          }
        ],
        editorReadingDialog: false,
        // 阅读理解题
        editorReadingcomprehension: {
          anType: '',
          anTypeList: [
            {
              name: '单选题',
              value: 'singlechoice'
            },{
              name: '填空题',
              value: 'shortanswer'
            },{
              name: '判断题',
              value: 'truefalse'
            },{
              name: '简答题',
              value: 'essay'
            }
          ],
          questionCon: [
            {
              type: 'singlechoice',
              name: '',
              list: [
                {
                  // 答案内容
                  id: 1,
                  name: '',
                  files_hash: null
                },{
                  id: 2,
                  name: '',
                  files_hash: null
                },{
                  id: 3,
                  name: '',
                  files_hash: null
                },{
                  id: 4,
                  name: '',
                  files_hash: null
                }
              ],
              selAnswer: 1
            },{
              type: 'truefalse',
              name: '',
              list: [
                {
                  id: 0,
                  value: '正确'
                },{
                  id: 1,
                  value: '错误'
                }
              ],
              selAnswer: 1
            },{
              type: 'shortanswer',
              num: 1,
              name: '',
              list: [
                {
                  id: 1,
                  value: ''
                }
              ]
            },{
              type: 'essay',
              name: '',
              answer: ''
            }
          ],
          formatData: null
        },
        // 题目难易程度
        complexity: '1',
        upload: { 
          filesListAll: [],
          answerlimit: 1,
          limit: 5,
          maxFileSize: 1024 * 1024 * 5,
        }
      }
    },
    components: {
      "yk-upload": ykUpload
    },
    mounted() {
      this.requestSections();
      console.log(this.$route);
      if (this.isEdit) {// 编辑
        this.getBankInfo();
      }
    },
    methods: {
      // 查看试题详情
      getBankInfo () {
        if (this.$route.params['questionid']) {
          this.$ajax.post(this.Const.apiurl, {
            module: 'exam',
            controller: 'Question',
            action: 'queQuestion',
            id: this.$route.params['questionid']
          }).then(res => {
            console.log('试题详情', res);
            let resData = res.data;
            if (resData.status == 0) {
              this.bankType = resData.data.qtype;
              this.complexity = resData.data.diff;
              this.editorAnalysis = resData.data.generalfeedback;
              this.uselimits = resData.data.usepermise;
              this.editorConTigan = resData.data.questiontext;
              this.editorConTiganFile = resData.data.imgs;
              console.log(this.bankType);
              this.sections[0] = resData.data.sections ? resData.data.sections : '';
              this.sections[1] = resData.data.section ? resData.data.section : '';
              //目前除了知识点默认值的问题 其余都已经解决
              switch (this.bankType) {
                case 'singlechoice': 
                  this.editorConAnswerRadio.list = resData.data.answer;
                  this.editorConAnswerRadio.selAnswer = (_ => {
                    let arr = resData.data.answer.filter(item => {
                      if (item.fraction == '100') {
                        return true;
                      } else {
                        return false;
                      }
                    });
                    return (arr && arr.length) ? arr[0]['id'] : '';
                  })();
                  break;
                case 'multiplechoice':
                  this.editorConAnswerCheckbox = (_ => {
                    return resData.data.answer.map(item => {
                      return {
                        id: item.id,
                        value: item.name,
                        fileList: new Array(item.imgs),
                        isTrue: item.fraction == 100 ? true : false
                      }
                    });
                  })();
                  break;
                case 'shortanswer':
                this.editorConAnswerCompletion = resData.data.answer;
                case 'truefalse':
                this.editorConAnswertrueOfFalse = resData.data.answer;
                case 'essay':
                this.editorConAnswerEssayquestion = resData.data.option;
                case 'match':
                this.editorConAnswerMatchquestion = resData.data.option;
                case 'comprehensive':
                this.editorConAnswerReadquestion = resData.data.option;
                case 'readingcomprehension':
                this.editorReadingcomprehension = resData.data.option;
              }
            }
            
            
          }).catch(err => {
            this.$message({type: 'error', message: '请求出错，请检查本地网络！', onClose () {this.$router.go(-1);}});
          });
        }
      },
      // 请求章节数据
      requestSections () {
        this.$ajax.post(this.Const.apiurl, {
          module: 'service',
          controller: 'Course_Sections_Con',
          action: 'queSections',
          courseid: this.Const.courseData.query.d
        }).then(res => {
          let resData = res.data;
          if (resData.status === 0) {
             this.sectionList = resData.data;
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '知识点数据获取失败！'});
          }
        }).catch(err => {
          this.$message({type: 'error', message: '请求知识点数据失败！'});
        });
      },
      goback () { this.$router.go(-1); },
      // 章节改变
      sectionsChange (val) {
        this.sections = val;
      },
      // 题型改变
      bankTypeChange (val) {
        this.bankType = val;
      },
      editorReadyTiGan (editorInstance) {
        const This = this;
        editorInstance.setContent(this.editorConTigan);
        editorInstance.addListener('blur', function (e) {
          This.editorConTigan = editorInstance.getContent();
        });
      },
      editorReadyAnalysis (editorInstance) {
        const This = this;
        editorInstance.setContent(this.editorAnalysis);
        editorInstance.addListener('blur', function (e) {
          This.editorAnalysis = editorInstance.getContent();
        });
      },
      // 添加单选选项
      addEditorRadio (item) {
        item.list.push({
          id: item.length + 1,
          name: '答案内容',
          fileList: []
        });
      },
      addEditorRadioquestionAN (event, index) {
        console.log(event);
        let lastItem = event.path[3].querySelectorAll('.myeditor');
        this.editorReadingcomprehension.anAnswerRadio[index].list.push({
          id: lastItem.length + 1,
          value: '答案内容',
          fileList: []
        });
      },
      // 删除单选选项
      deleteAnwserRadio (item) {
        if (this.editorConAnswerRadio.list.length > 1) {
          this.editorConAnswerRadio.list.splice(this.editorConAnswerRadio.list.indexOf(item), 1);
        } else {
          this.$notify({
            title: '警告',
            message: '至少添加一个答案！',
            type: 'warning'
          });
        }
      },
      deleteAnwserRadioAN (item, index) {
        if (this.editorReadingcomprehension.questionCon[index].list.length > 1) {
          this.editorReadingcomprehension.questionCon[index].list.splice(this.editorReadingcomprehension.questionCon[index].list.indexOf(item), 1);
        } else {
          this.$notify({
            title: '警告',
            message: '至少添加一个答案！',
            type: 'warning'
          });
        }
      },
      // 添加多选选项
      addEditorCheckbox () {
        let lastItem = this.$refs.edAnsCheckbox.querySelectorAll('.myeditor');
        this.editorConAnswerCheckbox.push({
          id: lastItem.length + 1,
          value: '答案内容',
          fileList: []
        });
      },
      // 删除多选答案项
      deleteAnwserCheckbox (item) {
        if (this.editorConAnswerCheckbox.length > 1) {
          this.editorConAnswerCheckbox.splice(this.editorConAnswerCheckbox.indexOf(item), 1);
        } else {
          this.$notify({ title: '警告', message: '至少添加一个答案！', type: 'warning' });
        }
      },
      // 填空题的填空数改变
      completionNumChan (val, item) {
        console.log(val, item);
        let len = item.list.length;
        if (val < len) {
          // 减少题目
          item.list.splice(val, len - val);
        } else if ( val > len) {
          // 增加题目
          for (let i = len + 1; i <= val; i++) {
            let obj = {
              id: i,
              value: '答案内容'
            };
            item.list.push(obj);
          }
        }
      },
      // 添加综合阅读题答案
      addEditorReadquestion () {
        this.editorConAnswerReadquestion.push({ name: '', answer: '' });
      },
      // 添加匹配题匹配项
      addEditorMatchquestion () {
        this.editorConAnswerMatchquestion.push({ name: '', answer: '' });
      },
      // 删除综合阅读题答案
      deleteAnwserReadquestion (item) {
        if (this.editorConAnswerReadquestion.length > 1) {
          this.editorConAnswerReadquestion.splice(this.editorConAnswerReadquestion.indexOf(item), 1);
        } else {
          this.$notify({
            title: '警告',
            message: '至少添加一个答案！',
            type: 'warning'
          });
        }
      },
      // 删除匹配题答案
      deleteAnwserMatchquestion (item) {
        if (this.editorConAnswerMatchquestion.length > 1) {
          this.editorConAnswerMatchquestion.splice(this.editorConAnswerMatchquestion.indexOf(item), 1);
        } else {
          this.$notify({
            title: '警告',
            message: '至少添加一个匹配项！',
            type: 'warning'
          });
        }
      },
      // 删除阅读题下的题目
      addDeleteItem (obj) {
        this.editorReadingcomprehension.questionCon = this.editorReadingcomprehension.questionCon.filter((item) => {
          return (item === obj) ? false : true;
        });
      },
      // 添加综合阅读题的题目
      addReadingQuesSel (val) {
        if (val === 'singlechoice') {
          this.editorReadingcomprehension.questionCon.push({
            type: 'singlechoice',
            name: '',
            list: [
              {
                // 答案内容
                id: 1,
                name: '答案1',
                files_hash: null
              },{
                id: 2,
                name: '答案2',
                files_hash: null
              },{
                id: 3,
                name: '答案3',
                files_hash: null
              },{
                id: 4,
                name: '答案4',
                files_hash: null
              }
            ],
            selAnswer: 1
          });
        } else if (val === 'truefalse') {
          this.editorReadingcomprehension.questionCon.push({
            type: 'truefalse',
            name: '',
            list: [
              {
                id: 0,
                value: '正确'
              },{
                id: 1,
                value: '错误'
              }
            ],
            selAnswer: 1
          });
        } else if (val === 'shortanswer') {
          this.editorReadingcomprehension.questionCon.push({
            type: 'shortanswer',
            num: 1,
            name: '',
            list: [
              {
                id: 1,
                value: '答案内容'
              }
            ]
          });
        } else if (val === 'essay') {
          this.editorReadingcomprehension.questionCon.push({
            type: 'essay',
            name: '',
            answer: ''
          });
        }
      },
      handleSelected (fileList) {
        console.log('handleSelected-fileList', fileList, this.fileObj);
      },
      handleDeleteOld (fileId) {
        console.log('删除历史文件', fileId);
      },
      // 提交添加题目
      fnSubmit () {
        if (!this.sections.length) {
          this.$message({type: 'warning', message: '请先指定题目知识点'});
          return ;
        }
        if (!this.editorConTigan) {
          this.$message({type: 'warning', message: '请先设定题干内容'});
          return ;
        }
        switch(this.bankType){
          // 单选题
          case 'singlechoice': this.submitRadio(this.bankType);break;
          case 'multiplechoice': this.submitRadio(this.bankType);break;
          case 'shortanswer': this.submitShortanswer();break;
          case 'truefalse': this.submitTruefalse();break;
          case 'essay': this.submitEssay();break;
          case 'comprehensive': this.submitComprehensive();break;
          case 'match': this.submitMatch();break;
          case 'readingcomprehension': this.submitReading();break;
        }
      },
      requestAddQuestion (reqData) {
        let data = Object.assign({
          module: 'exam',
          controller: 'Question',
          action: 'addQuestion',
          qtype: this.bankType,
          course: this.Const.courseData.query.d,
          section: this.sections[0]? this.sections[0] : '',
          sections: this.sections[1] ? this.sections[1] : '',
          diff: this.complexity.toString(),
          usepermise: this.uselimits,
          name: this.editorConTigan,
          generalfeedback: this.editorAnalysis === ueditor_default.editorAnalysis ? '此题暂无题目解析' : this.editorAnalysis,
          answerformat: '1',
          files_hash: (list => { return list.map(file => { return file.file.hash; }); })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
        }, reqData);
        console.log('题提交内容', data);
        let formData = new FormData();
        for (let key in data) {
          if (key == 'msglist' || key == 'answer' || key == 'files_hash' || key == 'option') {
            formData.append(key, JSON.stringify(data[key]));
          } else { formData.append(key, data[key]); }
        }
        let config = {headers: {"Content-Type": 'multipart/form-data'}};
        this.$ajax.post(this.Const.apiurl, formData, config).then(response => {
          let resData = response.data;
          if (resData.status == 0) {
            this.$message({type: 'success', message: resData.data ? resData.data : '添加成功！', onClose(){window.location.reload();}});
            
          } else {
            this.$message({type: 'info', message: resData.data ? resData.data : '添加失败！'});
          }
        }).catch(err => {
          this.$message({type: 'error', message: '添加失败，请检查本地网络！'});
        });
      },
      // 选择题
      submitRadio (qtype) {
        const This = this;
        let reqData = {
          answer: (type => {
            if (type === 'singlechoice') {
              return This.editorConAnswerRadio.list.map((item, index) => {
                return {
                  name: item.name,
                  fraction: (item.id === This.editorConAnswerRadio.selAnswer) ? '100' : '0',
                  tab: This.Const.optionsTab[index],
                  files_hash: (item.files && item.files.length) ? item.files[0].file.hash : ''
                }
              });
            } else if (type === 'multiplechoice') {
              return This.editorConAnswerCheckbox.map((item, index) => {
                return {
                  name: item.value,
                  fraction: item.isTrue ? '100' : '0',
                  tab: This.Const.optionsTab[index],
                  files_hash: (item.files && item.files.length) ? item.files[0].file.hash : ''
                }
              });
            }
          })(qtype),
          files: ((list, tiganFile) => {
            let arr1 = [], arr2 = [];
            for (let i = 0, item; item = list[i]; i++) {
              if (item.files && item.files.length) {
                for (let j = 0, jtem; jtem = item.files[j]; j++) { arr1.push(jtem.file); }
              }
            }
            for (let i = 0, item; item = tiganFile[i]; i++) { arr2.push(item.file); }
            return arr1.concat(arr2);
          })(qtype === 'singlechoice' ? this.editorConAnswerRadio.list : this.editorConAnswerCheckbox, this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
          msglist: ((list, tiganFile) => {
            let arr1 = [], arr2 = [];
            for (let i = 0, item; item = list[i]; i++) {
              if (item.files && item.files.length) {
                for (let j = 0, jtem; jtem = item.files[j]; j++) { arr1.push({file_hash: jtem.file.hash, summary: jtem.summary}); }
              }
            }
            for (let i = 0, item; item = tiganFile[i]; i++) { arr2.push({file_hash: item.file.hash, summary: item.summary}); }
            return arr1.concat(arr2);
          })(qtype === 'singlechoice' ? this.editorConAnswerRadio.list : this.editorConAnswerCheckbox, this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
        };
        this.requestAddQuestion(reqData);
      },
      // 填空题
      submitShortanswer () {
        let reqData = {
          answer: (_ => {
            return this.editorConAnswerCompletion.list.map((item) => { return item.value });
          })(),
          files: ((tiganFile) => {
            return tiganFile.map((item) => {
              return item.file;
            });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
          msglist: ((tiganFile) => {
            return tiganFile.map((item) => { return {file_hash: item.file.hash, summary: item.summary}; });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
        };
        this.requestAddQuestion(reqData);
      },
      // 判断题
      submitTruefalse () {
        let reqData = {
          answer: {
            fraction: this.editorConAnswertrueOfFalse.selAnswer
          },
          files: ((tiganFile) => {
            return tiganFile.map((item) => { return item.file; });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
          msglist: ((tiganFile) => {
            return tiganFile.map((item) => { return {file_hash: item.file.hash, summary: item.summary}; });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
        };
        this.requestAddQuestion(reqData);
      },
      // 匹配题
      submitMatch () {
        let reqData = {
          option: this.editorConAnswerMatchquestion,
          files: ((tiganFile) => {
            return tiganFile.map((item) => {
              return item.file;
            });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
          msglist: ((tiganFile) => {
            return tiganFile.map((item) => {
              return {file_hash: item.file.hash, summary: item.summary};
            });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
        };
        this.requestAddQuestion(reqData);
      },
      // 简答题
      submitEssay () {
        let reqData = {
          answer: this.editorConAnswerEssayquestion,
          files: ((tiganFile) => {
            return tiganFile.map((item) => {
              return item.file;
            });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
          msglist: ((tiganFile) => {
            return tiganFile.map((item) => {
              return {file_hash: item.file.hash, summary: item.summary};
            });
          })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
        };
        this.requestAddQuestion(reqData);
      },
      // 综合阅读题
      submitComprehensive () {
        let reqData = {
            option: this.editorConAnswerReadquestion,
            files: ((tiganFile) => {
              return tiganFile.map((item) => {
                return item.file;
              });
            })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []),
            msglist: ((tiganFile) => {
              return tiganFile.map((item) => {
                return {file_hash: item.file.hash, summary: item.summary};
              });
            })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])
          };
        this.requestAddQuestion(reqData);
      },
      // 格式化添加的阅读题的题目
      addReadingFormat () {
        let This = this;
        let formatData = {};
        formatData.ques = this.editorReadingcomprehension.questionCon.map((item) => {
          let obj = {};
          if (item.type === 'singlechoice') {
            obj = {
              answer: (_ => {
                let arr = [];
                for (let i = 0, obj; obj = item.list[i]; i++) {
                  arr.push({
                    name: obj.name,
                    fraction: obj.id === item.selAnswer ? '100' : '0',
                    tab: This.Const.optionsTab[i],
                    files_hash: (obj.files && obj.files.length) ? item.files[0].file.hash : ''
                  });
                }
                return arr;
              })()
            };
          } else if (item.type === 'truefalse') {
            obj = {
              answer: {
                fraction: item.selAnswer
              }
            };
          } else if (item.type === 'shortanswer') {
            obj = {
              answer: item.list.map((obj) => {return obj.value;})
            };
          } else if (item.type === 'essay') {
            obj = {
              answer: item.answer
            };
          }
          return Object.assign({
            qtype: item.type,
            name: item.name,
            files_hash: (_ => {
              let arr = [];
              if (item.files && item.files.length) {
                for (let i = 0; i < item.files.length; i++) {
                  arr.push(item.files[i].file.hash);
                }
                return arr;
              } else {
                return ''
              }
            })()
          }, obj);
        });
        formatData.files = [];
        for (let i = 0, item; item = this.editorReadingcomprehension.questionCon[i]; i++) {
          if (item.files && item.files.length) {
            for (let j = 0; j < item.files.length; j++) {
              formatData.files.push(item.files[j]);
            }
          }
          if (item.type === 'singlechoice') {
            for (let j = 0; j < item.list.length; j++) {
              if ( (item.list[j].files && item.list[j].files.length) ) {
                formatData.files.push(item.list[j].files[0]);
              }
            }
          }
        }
        this.editorReadingcomprehension.formatData = formatData;
        console.log('formatData', formatData);
        this.editorReadingDialog = false;
      },
      // 阅读理解
      submitReading () {
        if (this.editorReadingcomprehension.formatData) {
          let reqData = {
            option: this.editorReadingcomprehension.formatData.ques,
            files: this.editorReadingcomprehension.formatData.files.map((item) => {return item.file;}).concat((tiganFile => {
              return tiganFile.map((item) => {
                return item.file;
              });
            })(this.editorConTiganFile.files ? this.editorConTiganFile.files : [])),
            msglist: this.editorReadingcomprehension.formatData.files.map((item) => {return {file_hash: item.file.hash, summary: item.summary};}).concat((tiganFile => {
              return tiganFile.map((item) => {
                return {file_hash: item.file.hash, summary: item.summary};
              });
            })(this.editorConTiganFile.files ? this.editorConTiganFile.files : []))
          };
          this.requestAddQuestion(reqData);
        } else {
          this.$message({type: 'warning', message: '该题目的子题目不能为空'});
        }
      }
    },
    watch: {
      $route: {
        handler (val, oldVal) {
          console.log('$route', val);
        },
        deep: true
      }
    }
  }
</script>

<style lang="scss">
#addbank{
  .title{
    @include middelH(50px);
    border-bottom: 1px solid gray;
    @include setTitle;
    i{
      padding-right: 10px;
    }
  }
  .add-bank-wrap{
    width: 95%;
    margin: 10px auto;
    .line {
      border-bottom: 1px solid #ccc;
      width: 100%;
    }
    .bank-type{
      float: left;
      .btn-op{
        width: 90%;
      }
    }
    .item{
      > *{
        float: left;
        width: 100%;
        margin: 10px 0 20px;
      }
      .answer-wrap{
        width: 80%;
        .el-checkbox__input, .el-radio__input{
          height: 40px;
          line-height: 40px;
        }
      }
      p{
        width: 80px;
        text-align: right;
        margin-right: 20px;
        line-height: 40px;
        i{
          color: #32B16C;
          padding-right: 5px;
        }
      }
      .myeditor{
        width: 80%;
        &.answer-ed{
          width: 100%;
          margin-bottom: 10px;
        }
        > * {
          float: left;
          margin-right: 10px;
        }
        .el-textarea{
          width: 100%;
        }
        > i{
          @include middelH(40px);
          font-size: 2.0rem;
          cursor: pointer;
        }
        .option {
          width: 80%;
        }
        .yk-upload{
          width: 100%;
        }
      }
      .addanswer{
        width: 100%;
        text-align: center;
        margin-top: 20px;
        button{
          border: 1px dotted #32b16c;
          padding: 12px 50px;
          i{
            margin-right: 5px;
          }
        }
      }
      .complexity{
        i {
          @include middelH(40px);
        }
      }
      .tiankongnum {
        .answer-wrap li{
          float: left;
          width: 40%;
          margin-right: 10px;
          margin-bottom: 5px;
          .num{
            width: 30px;
            height: 30px;
            background: #32B16C;
            color: #fff;
            text-align: center;
            line-height: 30px;
            display: block;
          }
        }
        .answer {
          margin-top: 10px;
        }
        .mark{
          display: block;
          text-align: center;
          color: #999;
        }
      }
      .readquestion .myeditor{
        margin-bottom: 10px;
        .el-textarea{
          width: 90%;
        }
      }
    }
    .up-img {
      .up-btn {
        @include middelH(40px);
        width: 80px;
        overflow: hidden;
        position: relative;
        .up-img-file {
          position: absolute;
          top: 0;
          left: 0;
          width: 80px;
          height: 32px;
          z-index: 101;
          opacity: 0.009;
          cursor: pointer;
          &:hover + button {
            background: #66b1ff;
            border-color: #66b1ff;
            color: #fff;
          }
        }
        button {
          position: absolute;
          top: 0;
          left: 0;
          z-index: 100;
        }
      }
    }
    .submit{
      margin: 20px auto 100px;
      text-align: center;
      button{
        width: 100px;
      }
    }
  }
  #add-reading {
    .ques-item {
      border: 1px solid #1e1e3a;
      border-radius: 4px;
      margin: 5px 0;
      padding: 10px 30px 10px;
      box-sizing: border-box;
      position: relative;
      .delete-item{
        position: absolute;
        top: 10px;
        right: 10px;
        width: 40px;
        height: 40px;
        line-height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 20px;
        text-align: center;
        &:hover{
          background: #1e1e3a;
          color: #fff;
        }
      }
      .fl{
        margin-right: 20px;
        width: 80px;
        text-align: right;
      }
      .add-title {
        .el-textarea {
          width: 80%;
        }
        margin-bottom: 20px;
      }
      .add-quesnum {
        margin-bottom: 20px;
      }
      .add-answer {
        width: 70%;
        .myeditor {
          width: 100%;
          border: 1px dashed #6d6d74;
          border-radius: 2px;
          margin: 5px 0;
          padding: 5px;
          .option{
            width: 80%;
            position: relative;
            i.el-icon-close{
              position: absolute;
              top: 5px;
              right: 5px;
              width: 30px;
              height: 30px;
              text-align: center;
              line-height: 30px;
              border-radius: 50%;
              cursor: pointer;
              &:hover {
                color: #fff;
                background: #1e1e3a;
              }
            }
            .el-textarea{
              width: 90%;
            }
          }
          > * {
            float: left;
          }
          label {
            margin-right: 10px;
          }
        }
        .wrap {
          display: inline-block;
          width: 60%;
        }
        .answer-wrap {
          width: 90%;
          float: left;
          li {
            float: left;
            width: 200px;
            margin: 0 10px 5px 0;
            span{
              width: 30px;
              height: 30px;
              line-height: 30px;
              text-align: center;
              background: #1e1e3a;
              display: block;
              color: #fff;
            }
          }
        }
      }
    }
    .ques-type {
      color: #32B16C;
      font-size: 22px;
      height: 30px;
      line-height: 30px;
      margin-bottom: 20px;
    }
  }
}
</style>

