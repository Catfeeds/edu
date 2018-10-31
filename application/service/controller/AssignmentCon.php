<?php
namespace app\service\controller;
use app\service\model\Assignment;

class AssignmentCon extends Common
{
    public function __construct() {
        parent::__construct();
    }
    /**
     * @author 陈博文
     * 添加作业
     */
    public function addHomework() {
        $data = $this->data;
        $data = [
            'uid' => '1DF445286C13EE160D034C654994D06C',
            'courseid' => '0502A31B000CC4D94FEA7B001408926E',
            'sectionid' => ['2933386D144C658760A53711DC19CA9C','9873386D144C658760A53711DC19CBUI'],
            'idnumber' => '',//作业编号，（前端没有传该值时，且为添加的第一道作业时，查询该课程的编号，尾数加“001”；如果不是添加的第一道作业，则使用上次添加的最后一道作业的编号加“1”）
            'type' => '单选',//作业类型（和试题类型使用一样的配置,目前只考虑综合题型和简答题）
            'name' => '这里是作业内容',
            'explain' => '这里是作业描述',
            'hash' => '6d05bdb1c7933681a216329201f67244,258a4efe3bdb6b155b2e5873fa5f53ba,3eff2dab55298c4625d250a2d4d70296',
            'answer' => [//题型为填空题时，该answer的count值为1，将问题保存到作业表，答案保存到作业答案表即可；题型为简答题时，该answer的count的值一定大于1，按试题的综合题的方式保存作业
                [
                    'question' => '子问题1',
                    'answer' => '子参考答案1',
                    'attention' => '作业答案特别说明（可有可无）',
                    'hash_question' => '子问题哈希值，使用逗号连接；没有文件时为空',
                    'hash_answer' => '子答案哈希值，使用逗号连接；没有时为空'
                ],
                [
                    'question' => '子问题2',
                    'answer' => '子参考答案2',
                    'attention' => '作业答案特别说明（可有可无）',
                    'hash_question' => '子问题哈希值，使用逗号连接；没有文件时为空',
                    'hash_answer' => '子答案哈希值，使用逗号连接；没有时为空'
                ],
                [
                    'question' => '',
                    'answer' => '主参考答案666',
                    'attention' => '主参考答案666作业答案特别说明',
                    'hash_question' => '',
                    'hash_answer' => ''
                ],
            ],
            'msglist' => [//前端有文件上传时需要必须上传的文件描述信息的格式，后端查看不用理会该信息
                [
                    'hash' => '6d05bdb1c7933681a216329201f67244',
                    'summary' => '文件描述,没有文件时，为空',
                    'newname' => '文件重命名1'
                ],
                [
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '文件描述，没有文件时为空',
                    'newname' => '文件重命名2'
                ],
                [
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '文件描述，没有文件时为空',
                    'newname' => '文件重命名2'
                ],
                [
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '文件描述，没有文件时为空',
                    'newname' => '文件重命名2'
                ],
            ],
            'upload_files' => [//有文件上传时，后端接收到的文件数据格式，前端不用理会该信息
                0 => [
                    'file' => 'Object（上传文件对象）0',
                    'name' => '上传文件名',
                    'hash' => '6d05bdb1c7933681a216329201f67244',
                    'summary' => '上传文件描述',
                    'flag' => 'answer'
                ],
                1 => [
                    'file' => 'Object（上传文件对象）1',
                    'name' => '上传文件名',
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '上传文件描述',
                    'flag' => 'answer'
                ],
                2 => [
                    'file' => 'Object（上传文件对象）2',
                    'name' => '上传文件名',
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '上传文件描述',
                    'flag' => 'homework'
                ],
                3 => [
                    'file' => 'Object（上传文件对象）3',
                    'name' => '上传文件名',
                    'hash' => '258a4efe3bdb6b155b2e5873fa5f53ba',
                    'summary' => '上传文件描述',
                    'flag' => 'answer'
                ],
                
            ],
            'upload_files_hashs' => [//有文件上传时，后端接收到的文件数据格式，前端不用理会该信息
                ['文件1哈希值'], ['文件2哈希值'], 
            ]
        ];
       
        $assignment = new Assignment();
        return $assignment->addAssignment($data);
    }
    /**
     * 根据课程id，课程章或节id（章节id可为空）获取其作业列表
     */
    public function homeworkList() {
        $data = $this->data;
        $assignment = new Assignment;
        return $assignment->homeworkList($data);
    }
    /*
     * 根据课程id，课程章或节id，作业id获取其作业详情
     */
    public function anserList() {
        $data = $this->data;
        $assignment = new Assignment;
        return $assignment->anserList($data);
    }
    public function delHomework() {
        $data = $this->data;
        $assignment = new Assignment;
        $ids = $data['ids'];
        return $assignment->delHomework($ids);
    }
    /*author 李桐
     * 更新作业
     */
    public function uphomework() {
        $data = $this->data;
        $assAns = new Assignment();
        $result = $assAns->uphomework($data);
        return $result;
    }
    
}