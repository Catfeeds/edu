<?php
namespace app\exam\controller;
use app\service\controller\Common;
use app\service\model\Attachments;
use app\service\model\Classroom;
use app\exam\model\ExamAnswer;
use app\service\model\ClassroomSections;
use app\service\model\CourseSections;
use app\service\model\Course;

class Question extends Common
{
    public function __construct(){
        parent::__construct();
    }
    /**
     * 试题添加
     * 根据不同qtype类型值选择不同添加方法
     * @author 陈博文
     */
    public function addQuestion() {
        $data = $this->data;//前端传过来的所有参数
        $data['files_hash'] = json_decode($data['files_hash'], true);
        switch ($data['qtype']) {
            case 'singlechoice':
            case 'multiplechoice':
                $data['answer'] = json_decode($data['answer'], true);
                $add = $this->addChoice($data);//选择题
                break;
            case 'shortanswer':
                $data['answer'] = json_decode($data['answer'], true);
                $add = $this->addShort($data);//填空题
                break;
            case 'truefalse':
                $data['answer'] = json_decode($data['answer'], true);
                $add = $this->addTruefalse($data);//判断题
                break;
            case 'match':
                $data['option'] = json_decode($data['option'], true);
                $add = $this->addMatch($data);//匹配题
                break;
            case 'essay':
                $add = $this->addEssay($data);//简答题
                break;
            case 'comprehensive':
                $data['option'] = json_decode($data['option'], true);
                $add = $handAddQuestionthis->addComprehensive($data);//综合题
                break;
            case 'readingcomprehension':
                $data['option'] = json_decode($data['option'], true);
                $add = $this->addReadingcomprehension($data);//阅读理解
                break;
        }
        if ($add) {
            return getBack(0, '添加成功！');
        } else {
            return getBack(1, '添加成功！');
        }
    }
    /**
     * 试题编辑
     * 根据不同qtype类型值选择不同更新方法
     * @author 陈博文
     */
    public function upQuestion() {
        $data = $this->data;//前端传过来的所有参数
        $Question = model('ExamQuestion');
        if (!isset($data['id'])) {
            echo getBack(1, '参数错误');
        }
        $feild_arr = $Question->queField($data['id']);
        $data['files_hash'] = json_decode($data['files_hash'], true);
        if ($feild_arr == false) {
            return getBack(1, '该试题不存在');
        }
        switch ($feild_arr['qtype']) {
            case 'singlechoice':
            case 'multiplechoice':
                $data['answer'] = json_decode($data['answer'],  true);
                $up = $this->upChoice($data, $feild_arr);//选择题
                break;
            case 'shortanswer':
                $data['answer'] = json_decode($data['answer'],  true);
                $up = $this->upShort($data, $feild_arr);//填空题
                break;
            case 'truefalse':
                $data['answer'] = json_decode($data['answer'],  true);
                $up = $this->upTruefalse($data, $feild_arr);//判断题
                break;
            case 'match':
                $data['option'] = json_decode($data['option'],  true);
                $up = $this->upMatch($data, $feild_arr);//匹配题
                break;
            case 'essay':
                $up = $this->upEssay($data, $feild_arr);//简答题
                break;
            case 'comprehensive':
                $data['option'] = json_decode($data['option'],  true);
                $up = $this->upComprehensive($data, $feild_arr);//综合题
                break;
            case 'readingcomprehension':
                $data['option'] = json_decode($data['option'],  true);
                $up = $this->upReadingcomprehension($data, $feild_arr);//阅读理解
                break;
        }
        if ($up) {
            return getBack(0, '编辑成功！');
        } else {
            return getBack(1, '编辑失败！');
        }
    }
    /**
     * ok
     * 选择题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addChoice($data) {
        $return_data = $this->addCommon_AnHash($data);
        if ($return_data == false) {
            return false;
        }
        $info = $this->addChoiceAnswer($data, $return_data['new_id'], $return_data['all_hash_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 填空题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addShort($data) {
        $return_data = $this->addCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        $info = $this->addShortAnswer($data, $return_data['new_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 判断题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addTruefalse($data) {
        $return_data = $this->addCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        $info = $this->addTruefalseAnswer($data, $return_data['new_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 匹配题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addMatch($data) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->addCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        $seq = $Question->where(['pid' => $return_data['new_id'], 'status' => 0])->max('seq');
        foreach ($data['option'] as $k => $v) {
            $seq++;
            $list = [
                'pid' => $return_data['new_id'],
                'parentstr' => '0,' . $return_data['new_id'] . ',',
                'qtype' => $data['qtype'],
                'seq' => $seq,
                'courseid' => $data['course'],
                'sectionid' => $data['section'],
                'sectionsid' => $data['sections'],
                'name' => $name,
                'questiontext' => $v['name'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid'],
                'usepermise' => $data['usepermise'],
                'generalfeedback' => $data['generalfeedback'],
                'img' => $return_data['question_id_string']
            ];
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$k]['id'] = $getInfo[0]['id'];
        }
        $Result = $this->addMatchAnswer($data);
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 简答题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addEssay($data) {
        $return_data = $this->addCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        $info = $this->addEssayAnswer($data, $return_data['new_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 综合题试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addComprehensive($data) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->addCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        for ($i = 0; $i < count($data['option']); $i++) {
            $seq = $Question->where(['pid' => $return_data['new_id'], 'status' => 0])->max('seq');
            $list = [
                'pid' => $return_data['new_id'],
                'parentstr' => '0,' . $return_data['new_id'] . ',',
                'qtype' => $data['qtype'],'seq' => $seq + 1,
                'courseid' => $data['course'],
                'sectionid' => $data['section'],
                'sectionsid' => $data['sections'],
                'name' => $name,
                'questiontext' => $data['option'][$i]['name'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid'],
                'usepermise' => $data['usepermise'],
                'generalfeedback' => $data['generalfeedback'],
                'img' => $return_data['question_id_string']
            ];
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$i]['id'] = $getInfo[0]['id'];
        }
        $Result = $this->addComprehensiveAnswer($data);
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 阅读理解试题添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addReadingcomprehension($data) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->addCommon_AnHash($data);
        if ($return_data == false) {
            return false;
        }
        for ($i = 0; $i < count($data['option']); $i++) {
            $list = $this->getListArr($data, $return_data['new_id'], $i, $return_data['question_id_string'], $name);
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$i]['id'] = $getInfo[0]['id'];
        }
        $info = $this->addReadingcomprehensionAnswer($data, $return_data['all_hash_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 子试题公用添加
     * @author 陈博文
     * @param  $data                前端总数据
     * @param  $newID               父试题id
     * @param  $i                   循环次数
     * @param  $question_id_string  试题图片id字符串
     * @param  $name                试题简介
     */
    public function getListArr($data, $newID, $i, $question_id_string, $name) {
        $list = [
            'pid' => $newID,
            'parentstr' => '0,' . $newID . ',',
            'qtype' => $data['option'][$i]['qtype'],
            'courseid' => $data['course'],
            'sectionid' => $data['section'],
            'sectionsid' => $data['sections'],
            'name' => $name,
            'questiontext' => $data['option'][$i]['name'],
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid'],
            'usepermise' => $data['usepermise'],
            'generalfeedback' => $data['generalfeedback'],
            'img' => $question_id_string
            ];
        return $list;
    }
    /**
     * ok
     * 选择题答案添加,批量添加
     * @author 陈博文
     * @param  $data        前端总数据
     * @param  $newID       试题id
     * @param  $all_hash_id 总hash=>id
     */
    public function addChoiceAnswer($data, $newID, $all_hash_id) {
        $Answer = model('ExamAnswer');
        foreach ($data['answer'] as $k => $v) {
            $list[] = [
                'questionid' => $newID,
                'qtype' => $data['qtype'],
                'answer' => $v['name'],
            //    'answerformat' => $data['answerformat'],
                'fraction' => $v['fraction'],
                'tab' => $v['tab'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid'],
                'img' => empty($v['files_hash']) ? '' : $all_hash_id[$v['files_hash']]
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * ok
     * 填空题答案添加,批量添加
     * @author 陈博文
     * @param  $data  前端总数据
     * @param  $newID 试题id
     */
    public function addShortAnswer($data, $newID) {
        $Answer = model('ExamAnswer');
        $Answer->data([
            'questionid' => $newID,
            'qtype' => $data['qtype'],
            'answer' => join('||',$data['answer']),
        //    'answerformat' => $data['answerformat'],
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid']
            ]);
        $info = $Answer->save();
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 简答题答案添加
     * @author 陈博文
     * @param  $data  前端总数据
     * @param  newID  试题id
     */
    public function addEssayAnswer($data, $newID) {
        $Answer = model('ExamAnswer');
        $Answer->data([
            'questionid' => $newID,
            'qtype' => $data['qtype'],
            'answer' => $data['answer'],
        //    'answerformat' => $data['answerformat'],
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid']
            ]);
        $info = $Answer->save();
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 判断题答案添加
     * @author 陈博文
     * @param  $data   前端总数据
     * @param  $newID  试题id
     */
    public function addTruefalseAnswer($data, $newID) {
        $Answer = model('ExamAnswer');
        $answer = [config('truefalse_con.0'), config('truefalse_con.1')];
        for ($i = 0; $i < 2; $i++) {
            $list[] = [
                'questionid' => $newID,
                'qtype' => $data['qtype'],
                'answer' => $answer[$i],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'fraction' => $data['answer']['fraction'] == $i ? 100 : null,
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 匹配题答案添加，批量添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addMatchAnswer($data) {
        $Answer = model('ExamAnswer');
        for ($i = 0; $i < count($data['option']); $i++) {
            $list[] = [
                'questionid' => $data['option'][$i]['id'],
                'qtype' => $data['qtype'],
                'answer' => $data['option'][$i]['answer'],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * ok
     * 综合题答案添加，批量添加
     * @author 陈博文
     * @param  $data  前端总数据
     */
    public function addComprehensiveAnswer($data) {
        $Answer = model('ExamAnswer');
        for ($i = 0; $i < count($data['option']); $i++) {
            $list[] = [
                'questionid' => $data['option'][$i]['id'],
                'qtype' => $data['qtype'],
                'answer' => $data['option'][$i]['answer'],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * ok
     * 阅读理解答案添加,编辑
     * @author 陈博文
     * @param  $data        前端总数据
     * @param  $all_hash_id 总hash=>id
     */
    public function addReadingcomprehensionAnswer($data, $all_hash_id) {
        $Answer = model('ExamAnswer');
        $check = [config('truefalse_con.0'), config('truefalse_con.1')];
        $list = [];
        for ($i = 0; $i < count($data['option']); $i++) {
            switch ($data['option'][$i]['qtype']) {
                case 'singlechoice':
                case 'multiplechoice':
                    for($j = 0; $j < count($data['option'][$i]['answer']); $j++){
                        $list[] = [
                                'questionid' => $data['option'][$i]['id'],
                                'qtype' => $data['option'][$i]['qtype'],
                                'answer' => $data['option'][$i]['answer'][$j]['name'],
                            //    'answerformat' => $data['answerformat'],
                                'createbyid' => $data['uid'],
                                'modifiedbyid' => $data['uid'],
                                'fraction' => $data['option'][$i]['answer'][$j]['fraction'],
                                'img' => $all_hash_id[$data['option'][$i]['answer'][$j]['files_hash']]
                            ];
                        }
                    break;
                case 'truefalse':
                    for ($k = 0; $k < 2; $k++) {
                        $list[] = [
                                'questionid' => $data['option'][$i]['id'],
                                'qtype' => $data['option'][$i]['qtype'],
                                'answer' => $check[$k],
                            //    'answerformat' => $data['answerformat'],
                                'createbyid' => $data['uid'],
                                'fraction' => $data['option'][$i]['answer']['fraction'] == $k ? 100 : null,
                                'modifiedbyid' => $data['uid']
                            ];
                        }
                    break;
                case 'shortanswer':
                    $list[] = [
                            'questionid' => $data['option'][$i]['id'],
                            'qtype' => $data['option'][$i]['qtype'],
                            'answer' => join('||',$data['option'][$i]['answer']),
                        //    'answerformat' => $data['answerformat'],
                            'createbyid' => $data['uid'],
                            'modifiedbyid' => $data['uid']
                        ];
                    break;
                case 'essay':
                    $list[] = [
                            'questionid' => $data['option'][$i]['id'],
                            'qtype' => $data['option'][$i]['qtype'],
                            'answer' => $data['option'][$i]['answer'],
                        //    'answerformat' => $data['answerformat'],
                            'createbyid' => $data['uid'],
                            'modifiedbyid' => $data['uid']
                        ];
                    break;
            }

        }
        $info = $Answer->isUpdate(false)->saveAll($list);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 添加试题公共部分代码，无答案hash
     * @author 陈博文
     * @param  $data 前端总数据
     */
    public function addCommon_NoAnHash($data) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $seq = $Question->where(['qtype' => $data['qtype'], 'status' => 0, 'pid' => ''])->max('seq');
        if (!isset($data['upload_files']) || $data['upload_files'] != null) {
            $attachments = new Attachments();
            //试题图片id
            $question_IMG = $attachments->where('file_hash', 'in', $data['files_hash'])->column('id');
            //组合格式-试题图片id字符串
            $question_id_string = join(',',$question_IMG);
        }
        $Question->data([
            'courseid'  =>  $data['course'],
            'sectionid' =>  $data['section'],
            'sectionsid' => $data['sections'],
            'difficulty' => $data['diff'],
            'qtype' => $data['qtype'],
            'seq' => $seq + 1,
            'name' => $name,
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid'],
            'usepermise' => $data['usepermise'],
            'questiontext' => $data['name'],
            'generalfeedback' => $data['generalfeedback'],
            'img' => isset($question_id_string) ? $question_id_string : null,
        ]);
        $info = $Question->save();
        $new_id = $Question->id;
        if (!$info) {
            return false;
        }
        return ['question_id_string' => isset($question_id_string) ? $question_id_string : null,
                'new_id' => $new_id
                ];
    }
    /**
     * 添加试题公共部分代码，有答案hash
     * @author 陈博文
     * @param $data 前端总数据
     */
    public function addCommon_AnHash($data) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'],0,18,'utf-8') . "...";//试题简介
        $seq = $Question->where(['qtype' => $data['qtype'], 'status' => 0, 'pid' => ''])->max('seq');
        if (isset($data['upload_files']) && $data['upload_files'] != null) {
            $attachments = new Attachments();
            foreach ($data['upload_files'] as $v) {
                $all_hash[] = $v['hash'];
            }
            $all_hash_id = $attachments->saveFileBatch($data['upload_files'], $all_hash);//格式：hash => id
            //试题图片id
            foreach ($data['files_hash'] as $value) {
                $question_IMG[] = $all_hash_id[$value];
            }
            //组合格式-试题图片id字符串
            $question_id_string = join(',',$question_IMG);
        }
        $Question->data([
            'courseid'  =>  $data['course'],
            'sectionid' =>  $data['section'],
            'sectionsid' => $data['sections'],
            'difficulty' => $data['diff'],
            'qtype' => $data['qtype'],
            'seq' => $seq + 1,
            'name' => $name,
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid'],
            'usepermise' => $data['usepermise'],
            'questiontext' => $data['name'],
            'generalfeedback' => $data['generalfeedback'],
            'img' => isset($question_id_string) ? $question_id_string : null,
        ]);
        $info = $Question->save();
        $new_id = $Question->id;
        if (!$info) {
            return false;
        }
        return ['question_id_string' => isset($question_id_string) ? $question_id_string : null,
                'new_id' => $new_id,
                'all_hash_id' => isset($all_hash_id) ? $all_hash_id : null,
                ];
    }
    /**
     * ok
     * 编辑试题公共部分代码，无答案hash
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upCommon_NoAnHash($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";//试题简介
        $seq = $Question->where(['qtype' => $data['qtype'], 'status' => 0, 'pid' => ''])->max('seq');
        if (!isset($data['upload_files']) || $data['upload_files'] != null) {
            $attachments = new Attachments();
            //试题图片id
            $question_IMG = $attachments->where('file_hash', 'in', $data['files_hash'])->column('id');
            //组合格式-试题图片id字符串
            $question_id_string = join(',',$question_IMG);
        }
        $Question->data([
            'courseid'  =>  $feild_arr['courseid'],
            'sectionid' =>  $feild_arr['sectionid'],
            'sectionsid' => $feild_arr['sectionsid'],
            'difficulty' => $data['diff'],
            'qtype' => $feild_arr['qtype'],
            'seq' => $feild_arr['seq'],
            'name' => $name,
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid'],
            'usepermise' => $data['usepermise'],
            'questiontext' => $data['name'],
            'generalfeedback' => $data['generalfeedback'],
            'img' => isset($question_id_string) ? $question_id_string : null,
        ]);
        $info = $Question->save();
        $new_id = $Question->id;
        if (!$info) {
            return false;
        }
        return ['question_id_string' => isset($question_id_string) ? $question_id_string : null,
                'new_id' => $new_id
                ];
    }
    /**
     * ok
     * 编辑试题公共部分代码，有答案hash
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upCommon_AnHash($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";//试题简介
        $seq = $Question->where(['qtype' => $data['qtype'], 'status' => 0, 'pid' => ''])->max('seq');
        if (!isset($data['upload_files']) || $data['upload_files'] != null) {
            $attachments = new Attachments();
            foreach ($data['upload_files'] as $v) {
                $all_hash[] = $v['hash'];
            }
            $all_hash_id = $attachments->saveFileBatch($data['upload_files'], $all_hash);//格式：hash => id
            //试题图片id
            foreach ($data['files_hash'] as $key => $value) {
                $question_IMG[] = $all_hash_id[$value];
            }
            //组合格式-试题图片id字符串
            $question_id_string = join(',',$question_IMG);
        }
        $Question->data([
            'courseid'  =>  $feild_arr['courseid'],
            'sectionid' =>  $feild_arr['sectionid'],
            'sectionsid' => $feild_arr['sectionsid'],
            'difficulty' => $data['diff'],
            'qtype' => $feild_arr['qtype'],
            'seq' => $feild_arr['seq'],
            'name' => $name,
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid'],
            'usepermise' => $data['usepermise'],
            'questiontext' => $data['name'],
            'generalfeedback' => $data['generalfeedback'],
            'img' => isset($question_id_string) ? $question_id_string : null,
        ]);
        $info = $Question->save();
        $new_id = $Question->id;
        if (!$info) {
            return false;
        }
        return ['question_id_string' => isset($question_id_string) ? $question_id_string : null,
                'new_id' => $new_id,
                'all_hash_id' => isset($all_hash_id) ? $all_hash_id : null,
                ];
    }
    /**
     * ok
     * 选择题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upChoice($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $return_data = $this->upCommon_AnHash($data, $feild_arr);
        if ($return_data == false) {
            return false;
        }
        $info = $this->upChoiceAnswer($data, $return_data['new_id'], $return_data['all_hash_id'], $feild_arr['qtype']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 填空题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upShort($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $return_data = $this->upCommon_NoAnHash($data, $feild_arr);
        if ($return_data == false) {
            return false;
        }
        $info = $this->upShortAnswer($data, $return_data['new_id'], $feild_arr['qtype']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 判断题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upTruefalse($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $return_data = $this->upCommon_NoAnHash($data, $feild_arr);//调用无答案hash的公共存储部分
        if ($return_data == false) {
            return false;
        }
        $info = $this->upTruefalseAnswer($data, $return_data['new_id'], $feild_arr['qtype']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 匹配题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upMatch($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->upCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        for ($i = 0; $i < count($data['option']); $i++) {
            $list = [
                    'pid' => $return_data['new_id'],
                    'parentstr' => '0,' . $return_data['new_id'] . ',',
                    'qtype' => $feild_arr['qtype'],
                    'seq' => $feild_arr['seq'],
                    'courseid' => $feild_arr['courseid'],
                    'sectionid' => $feild_arr['sectionid'],
                    'sectionsid' => $feild_arr['sectionsid'],
                    'name' => $name,
                    'questiontext' => $data['option'][$i]['name'],
                    'createbyid' => $data['uid'],
                    'modifiedbyid' => $data['uid'],
                    'usepermise' => $data['usepermise'],
                    'generalfeedback' => $data['generalfeedback'],
                    'img' => $return_data['question_id_string']
                ];
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$i]['id'] = $getInfo[0]['id'];
        }
        $Result = $this->upMatchAnswer($data, $feild_arr['qtype']);
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 简答题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upEssay($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $return_data = $this->upCommon_NoAnHash($data, $feild_arr);
        if ($return_data == false) {
            return false;
        }
        $Result = $this->upEssayAnswer($data, $return_data['new_id'], $feild_arr['qtype']);
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 综合题试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upComprehensive($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->upCommon_NoAnHash($data);
        if ($return_data == false) {
            return false;
        }
        for ($i = 0; $i < count($data['option']); $i++) {
            $list = [
                    'pid' => $return_data['new_id'],
                    'parentstr' => '0,' . $return_data['new_id'] . ',',
                    'qtype' => $feild_arr['qtype'],
                    'seq' => $feild_arr['seq'],
                    'courseid' => $feild_arr['courseid'],
                    'sectionid' => $feild_arr['sectionid'],
                    'sectionsid' => $feild_arr['sectionsid'],
                    'name' => $name,
                    'questiontext' => $data['option'][$i]['name'],
                    'createbyid' => $data['uid'],
                    'modifiedbyid' => $data['uid'],
                    'usepermise' => $data['usepermise'],
                    'generalfeedback' => $data['generalfeedback'],
                    'img' => $return_data['que_IMG']
                ];
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$i]['id'] = $getInfo[0]['id'];
        }
        $Result = $this->upMatchAnswer($data, $feild_arr['qtype']);
        if ($Result) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 阅读理解试题编辑
     * @author 陈博文
     * @param  $data       前端总数据
     * @param  $feild_arr  数据过滤后字段
     */
    public function upReadingcomprehension($data, $feild_arr) {
        $Question = model('ExamQuestion');
        $obj = $this->del_something($data['id']);
        if ($obj == false) {
            return false;
        }
        $name = mb_substr($data['name'], 0, 18, 'utf-8') . "...";
        $return_data = $this->upCommon_AnHash($data);
        if ($return_data == false) {
            return false;
        }
        for ($i = 0; $i < count($data['option']); $i++) {
            $list = $this->getListArr($data, $return_data['new_id'], $i, $return_data['question_id_string'], $name);
            $getInfo = collection($Question->isUpdate(false)->saveAll([$list]))->toArray();
            $data['option'][$i]['id'] = $getInfo[0]['id'];
        }
        $info = $this->addReadingcomprehensionAnswer($data, $return_data['all_hash_id']);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 选择题答案编辑
     * @author 陈博文
     * @param  $data            前端总数据
     * @param  $newID           试题id
     * @param  $all_hash_id     答案id数组
     * @param  $qtype           题目类型
     */
    public function upChoiceAnswer($data, $newID, $all_hash_id, $qtype) {
        $Answer = model('ExamAnswer');
        for ($i = 0; $i < count($data['answer']); $i++) {
            $list[] = [
                'questionid' => $newID,
                'qtype' => $qtype,
                'answer' => $data['answer'][$i]['name'],
            //    'answerformat' => $data['answerformat'],
                'fraction' => $data['answer'][$i]['fraction'],
                'tab' => $data['answer'][$i]['tab'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid'],
                'img' => $all_hash_id[$data['answer'][$i]['files_hash']]
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * ok
     * 填空题答案编辑,批量编辑
     * @author 陈博文
     * @param  $data    前端总数据
     * @param  $newID   试题id
     * @param  $qtype   题目类型
     */
    public function upShortAnswer($data, $newID, $qtype) {
        $Answer = model('ExamAnswer');
        $Answer->data([
            'questionid' => $newID,
            'qtype' => $qtype,
            'answer' => join('||',$data['answer']),
        //    'answerformat' => $data['answerformat'],
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid']
            ]);
        $info = $Answer->save();
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 判断题答案编辑
     * @author 陈博文
     * @param  $data    前端总数据
     * @param  $newID   试题id
     * @param  $qtype   题目类型
     */
    public function upTruefalseAnswer($data, $newID, $qtype) {
        $Answer = model('ExamAnswer');
        $answer = [config('truefalse_con.0'), config('truefalse_con.1')];
        for ($i = 0; $i < 2; $i++) {
            $list[] = [
                'questionid' => $newID,
                'qtype' => $qtype,
                'answer' => $answer[$i],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'fraction' => $data['answer']['fraction'] == $i ? 100 : null,
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 匹配题答案编辑，批量编辑
     * @author 陈博文
     * @param  $data    前端总数据
     * @param  $qtype   题目类型
     */
    public function upMatchAnswer($data, $qtype) {
        $Answer = model('ExamAnswer');
        for ($i = 0; $i < count($data['option']); $i++) {
            $list[] = [
                'questionid' => $data['option'][$i]['id'],
                'qtype' => $qtype,
                'answer' => $data['option'][$i]['answer'],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * ok
     * 简答题答案编辑
     * @author 陈博文
     * @param  $data    前端总数据
     * @param  $newID   试题id
     * @param  $qtype   题目类型
     */
    public function upEssayAnswer($data, $newID, $qtype) {
        $Answer = model('ExamAnswer');
        $Answer->data([
            'questionid' => $newID,
            'qtype' => $qtype,
            'answer' => $data['answer'],
        //    'answerformat' => $data['answerformat'],
            'createbyid' => $data['uid'],
            'modifiedbyid' => $data['uid']
            ]);
        $info = $Answer->save();
        if ($info) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * ok
     * 综合题答案编辑，批量编辑
     * @author 陈博文
     * @param  $data    前端总数据
     * @param  $qtype   题目类型
     */
    public function upComprehensiveAnswer($data, $qtype) {
        $Answer = model('ExamAnswer');
        for ($i = 0; $i < count($data['option']); $i++) {
            $list[] = [
                'questionid' => $data['option'][$i]['id'],
                'qtype' => $qtype,
                'answer' => $data['option'][$i]['answer'],
            //    'answerformat' => $data['answerformat'],
                'createbyid' => $data['uid'],
                'modifiedbyid' => $data['uid']
            ];
        }
        $info = $Answer->saveAll($list);
        if ($info) {
            return true;
         } else {
            return false;
        }
    }
    /**
     * 根据问题id获取问题的题目及答案
     * @author 宋玉琪
     * @2018.9.7
     */
    public function queQuestion()
    {
        $data = $this->data;
        $ques = model('ExamQuestion');
        $ans = model('ExamAnswer');
        $check = model('ExamQuestionCheck');
        $info = $ques->getQuesbyid($data['id']);
        $info['createtime'] = '添加时间:' . date('Y-m-d', $info['createtime']);
        $checkMsg = $check->showCheckMsg($data['id'])['text'];
        if($info['check'] == 2)
        {
            $info['check_msg'] = $checkMsg;
        }
        switch ($info['qtype'])
        {
            case 'choice'://单选&多选题
                $answer = array();
                $ansinfo = $ans->getAnswer($data['id'] ,'id');
                foreach($ansinfo as $item)
                {
                    $arr = [
                        'id' => $item['id'],
                        'name' => $item['answer'],
                        'fraction' => $item['fraction'],
                        'tab' => $item['tab'],
                        'imgs' => $item['img']
                    ];
                    $answer[] = $arr;
                }
                $info['answer'] = $answer;
                break;
            case 'shortanswer'://填空题
                $answer = array();
                $ansinfo = $ans->getAnswer($data['id'], 'id');
                foreach($ansinfo as $item)
                {
                    $answer[$item['id']] = $item['answer'];
                }
                $info['answer'] = $answer;
                break;
            case 'truefalse'://判断题
                $answer = array();
                $ansinfo = $ans->getAnswer($data['id'], 'id');
                foreach($ansinfo as $item)
                {
                    $answer[] = [
                        'id' => $item['id'],
                        'fraction' => $item['fraction']
                         ];
                }
                $info['answer'] = $answer;
                break;
            case 'essay'://简答题
                break;
            case 'match'://匹配题
            case 'comprehensive'://综合题
                $option = array();
                $childlist = $ques->getQuesbypid($data['id']);
                $childsId = [];
                foreach($childlist as $item)
                {
                    $childsId[] = $item['id'];
                }
                $childsanswers = $ans->getAnswer($childsId, 'id');
                foreach($childlist as $item)
                {
                    foreach($childsanswers as $citem)
                    {
                        if($item['id'] == $citem['questionid'])
                        {
                            $arr = [
                                'id' => $item['id'],
                                'name' => $item['name'],
                                'answer' => $citem['answer']
                                ];
                            $option[] = $arr;
                        }
                    }
                }
                $info['option'] = $option;
                break;
            case 'readingcomprehension'://阅读理解
                $option = array();
                $childlist = $ques->getQuesbypid($data['id']);
                $questidarr = array();
                foreach($childlist as $i)
                {
                    $questidarr[] = $i['id'];
                }
                $childanswerlist = $ans->getAnswer($questidarr, 'id');
                foreach($childlist as $i)
                {
                    switch($i['qtype'])
                    {
                        case 'choice':
                            $arr = [
                                'id' => $i['id'],
                                'qtype' => $i['qtype'],
                                'name' => $i['name'],
                                'imgs' => $i['img'],
                                'answer' => []
                            ];
                            foreach($childanswerlist as $j)
                            {
                                if($i['id'] == $j['questionid'])
                                {
                                    $a = [
                                        'id' => $j['id'],
                                        'name' => $j['answer'],
                                        'fraction' => $j['fraction'],
                                        'tab' => $j['tab'],
                                        'imgs' => $j['img']
                                    ];
                                    array_push($arr['answer'],$a);
                                }
                            }
                            array_push($option,$arr);
                            break;
                        case 'shortanswer':
                            $arr = [
                                'id' => $i['id'],
                                'qtype' => $i['qtype'],
                                'name' => $i['name'],
                                'imgs' => $i['img'],
                                'answer' => []
                            ];
                            foreach($childanswerlist as $j)
                            {
                                if($i['id'] == $j['questionid'])
                                {
                                    $arr['answer'][$j['id']] = $j['answer'];
                                }
                            }
                            array_push($option,$arr);
                            break;
                        case 'truefalse':
                            $arr = [
                                'id' => $i['id'],
                                'qtype' => $i['qtype'],
                                'name' => $i['name'],
                                'imgs' => $i['img'],
                                'answer' => []
                            ];
                            foreach($childanswerlist as $j)
                            {
                                if($i['id'] == $j['questionid'])
                                {
                                    $a = [
                                        'id' => $j['id'],
                                        'fraction' => $j['fraction']
                                    ];
                                    $arr['answer'][] = $a;
                                }
                            }
                            array_push($option,$arr);
                            break;
                        case 'essay':
                            $arr = [
                                'id' => $i['id'],
                                'qtype' => $i['qtype'],
                                'questiontext' => $i['questiontext'],
                                'imgs' => $i['img'],
                                'answer' => []
                            ];
                            foreach($childanswerlist as $j)
                            {
                                if($i['id'] == $j['questionid'])
                                {
                                    $a=[
                                        $j['id'] => $j['answer']
                                    ];
                                    array_push($arr['answer'],$a);
                                }
                            }
                            array_push($option,$arr);
                            break;
                    }
                }
                $info['option'] = $option;
                break;
        }
        if($info)
        return getBack(0,$info);
        return getBack(1,'获取题目详情失败');
    }
    /**
     * ok
     * 软删除试题和试题答案
     * @author  陈博文
     * @param   $data  前端总数据
     */
    public function del_something($id) {
        $Answer = model('ExamAnswer');
        $Question = model('ExamQuestion');
        //软删除试题
        $info = $Question::where('id', $id)->update(['status' => 1, 'timemodified' => time()]);
        //软删除试题答案
        $info = $Answer::where('questionid', $id)->update(['status' => 1, 'timemodified' => time()]);
        if ($info !== false) {
            return false;
        }
        return true;
    }
    /**
     * 根据问题id来删除问题
     * @author 宋玉琪
     */
    public function delQuestion()
    {
        $data = $this->data;
        if (@empty($data['id'])) {
            return getBack(1, '参数错误！');
        }
        $res = $this->del_something($data['id']);
        if($res)
        {
             return getBack(0, '删除试题成功');
        }
        return getBack(1, '删除试题失败');
    }
    /**
     * 根据问题id来改变check状态，问题审核
     * @author 宋玉琪
     */
    public function checkQuestion()
    {
        $data = $this->data;
        if (!isset($data['status'])) {
            return getBack(1, '参数错误');
        }
        $ques = model('ExamQuestion');
        $res = $ques->checkQuestion($data);
        if($data['status'] == 2)
        {
            $check = model('ExamQuestionCheck');
            $res = $check->addcheckmsg($data);
        }
        if($res)
        {
             return getBack(0, '操作成功！');
        }
        return getBack(1, '操作失败！');
    }
    /*题目搜索
     *@author 李桐
     *@param char(32)    course      试题所在课程id
     *@param char(32)    classroom     课堂id
     *@param char(32)    section     试题所在课程章id（可不传）
     *@param char(32)    sections    试题所在课程节id（可不传）
     *@param varchar(50) qtype         题类型（可不传）
     *@param text        kwords  题目关键字（可不传）
     *@param tinyint(2)  check         审核状态（可不传,默认为可用状态的试题）
     *@param tinyint(2)  diff          难度（可不传，默认为所有难度类型）
     *@param int         page          页数
     *@param int         rows          每页数据条数
     */
     public function questionList(){
        $data = $this->data;//前端传过来的所有参数
        $Question = model('ExamQuestion');
        if (isset($data['course'])){
            $where['courseid'] = $data['course'];
        }else{
            if (isset($data['classroom'])){
                $data['classroom'] = $data['classroom'];
                $classroom = new Classroom();
                $where['courseid'] = $classroom->getCourseid($data['classroom']);
                if (!$where['courseid']){
                    return getBack(0, '');
                }
            }else{        //courseid和classroomid都没有传，目前暂时不考虑此情况
                return getBack(1,'参数错误!');
            }
        }

        if (@!empty($data['section'])){
            $where['sectionid'] = $data['section'];
        }
        if (@!empty($data['sections'])){
            $where['sectionsid'] = $data['sections'];
        }
        if (@!empty($data['qtype'])) {
            $where['qtype'] = $data['qtype'];
        }
        if (@!empty($data['kwords'])){
            $where['questiontext'] = ['like', '%' . $data['kwords'] . '%'];
        }
        if (@!empty($data['check'])){
            $where['check'] = $data['check'];
        }
        if (@!empty($data['diff'])){
            $where['difficulty'] = $data['diff'];
        }
        if (@!empty($data['usepermise'])) {
            $where['question.usepermise'] = $data['usepermise'];
        }
        if (!isset($data['rows']) || $data['rows'] < 1){
            $data['rows'] = config('paginate.list_rows');
        }
        if (!isset($data['page']) || $data['page'] < 1){
            $data['page'] = 1;
        }
        //查询所有非子问题的问题,获取page信息
        $where['parentstr'] = '0,';
        $res_question = $Question->alias('question')
            ->join('User user', 'question.createbyid = user.id')
            ->where($where)
            ->where('question.status',0)
            ->field('question.id, question.pid, question.qtype, question.sectionid as section, question.sectionsid as sections, question.difficulty, question.usepermise, question.name, question.questiontext, question.generalfeedback, question.img as imgs, question.timecreated, question.check, user.username')
            ->order('question.timecreated', 'asc')->paginate(['list_rows' => $data['rows'], 'page' => $data['page']])->toArray();
        //查询所有非子问题的章、节名
        $sectionid = [];
        foreach ($res_question['data'] as $k => $v) {
            $res_question['data'][$k]['imgs'] = explode(',', $v['imgs']);
            $sectionid[] = $v['section'];
            $sectionid[] = $v['sections'];
        }

        $sectionObject = isset($data['course']) ? new CourseSections() : new ClassroomSections();
        $sectionName = $sectionObject->getName($sectionid);//获取章节名

        $no_ch_id = [];
        for ($i = 0; $i < count($res_question['data']); $i++){
            $status = 0;
            for ($j = 0; $j < count($sectionName); $j++){
                if ($res_question['data'][$i]['section'] == $sectionName[$j]['id']){
                    $res_question['data'][$i]['section'] = $sectionName[$j]['name'];
                    $status++;
                }
                if ($res_question['data'][$i]['sections'] == $sectionName[$j]['id']){
                    $res_question['data'][$i]['sections'] = $sectionName[$j]['name'];
                    $status++;
                }
                if ($status == 2){
                    break;
                }
            }
            if ($res_question['data'][$i]['qtype'] == 'match'|| $res_question['data'][$i]['qtype'] == 'comprehensive'|| $res_question['data'][$i]['qtype'] == 'readingcomprehension'){
                $id[] = $res_question['data'][$i]['id'];//$id=〉有子问题的问题id
            }else{
                $no_ch_id[] = $res_question['data'][$i]['id'];//$nu_ch_id->没有子问题id
            }
        }
        if (isset($id)){
            $ch_question = collection($Question->where('pid','in',$id)->where('status',0)->field('id, pid, qtype, difficulty, name, usepermise, questiontext, generalfeedback, timecreated, check, img as imgs')->order('timecreated', 'asc')->select())->toArray();//$ch_question——>匹配、综合、阅读理解的子问题
            foreach ($ch_question as $k => $value) {
                $no_ch_id[] = $value['id'];
                $ch_question[$k]['imgs'] = empty($value['imgs']) ? [] : explode(',', $value['imgs']);
            }
            //将子问题放在非子问题后面
            $res_question['data'] = array_merge($res_question['data'], $ch_question);
        }
        $answer = new ExamAnswer();
        $res_answer = $answer->getAnswer($no_ch_id,'tab asc,timecreated');//$res_answer->没有子题答案
        for ($i = 0; $i < count($res_question['data']); $i++){
            //$k = 0;
            for ($j = 0; $j < count($res_answer); $j++){
                if ($res_question['data'][$i]['id'] == $res_answer[$j]['questionid']){
                    switch ($res_question['data'][$i]['qtype']){
                        //单选题
                        case 'singlechoice':
                        //多选题
                        case 'multiplechoice':
                            $res_question['data'][$i]['answer'][] = [
                                'id'       => $res_answer[$j]['id'],
                                'name'     => $res_answer[$j]['answer'],
                                'fraction' => $res_answer[$j]['fraction'],
                                'tab'      => $res_answer[$j]['tab'],
                                'imgs'     => empty($res_answer[$j]['img']) ? [] : explode(',', $res_answer[$j]['img']) 
                            ];
                            break;
                        //填空题
                        case 'shortanswer':
                            $res_question['data'][$i]['answer'][][$res_answer[$j]['id']] = $res_answer[$j]['answer'];
                            break;
                        //判断题
                        case 'truefalse':
                            $res_question['data'][$i]['answer'][] = [
                                'id'       => $res_answer[$j]['id'],
                                'answer'   => $res_answer[$j]['answer'],
                                'fraction' => $res_answer[$j]['fraction']
                            ];
                            break;
                        //简答题
                        case 'essay':
                        //匹配题
                        case 'match':
                        //综合题
                        case 'comprehensive':
                            $res_question['data'][$i]['answer'] = $res_answer[$j]['answer'];
                            break;
                    }
                }
            }
        }
        $res_question['data'] = $this->tree($res_question['data']);
        $res_question = $this->dataPageDispose($res_question);
        return getBack(0, $res_question);
     }
     /**@author李桐
      *方法详情
      * 生成树
      */
     public function tree($data, $pid = ''){
        $temp = array();
        foreach ($data as $v) {
            if ($v['pid'] == $pid){
                $v['option'] = $this->tree($data, $v['id']);
                $temp[] = $v;
            }
        }
        return $temp;
     }

   /*
    *表question中check的状态的修改
    */
    public function questionError() {
        $data = $this->data;//前端传递过来的所有数据，以数组的形式存
        $Question = model('ExamQuestion');
        $result = $Question ->questionError($data['id']);
        return getBack(0, $result);
    }
        /*
    *获取某一课程下，各题型下各题的数量
    *李存亮
    */
    public function getQuestionNum(){
        $data = $this->data;
        if( !empty($data['id'])){
            $where = ['courseid' => $data['id']];
        }
        else{
            return getBack(1, '参数错误！');
            //课程参数必须传
            /*$examCourse = new Course();
            $res = $examCourse->where('uid', $data['uid'])->where('status', 1)->column('id');
            $where['courseid'] = ['in', $res];*/
        }
        $examQuestion = model('ExamQuestion');
        $info = collection($examQuestion
                   ->where($where)
                   ->where('status', 0)
                   ->field('qtype,difficulty,check')
                   ->select())->toArray();
        if($info){
            $team['number'] = count($info);
            foreach ($info as $k => $v) {
                isset($team[$v['check']][$v['qtype']][$v['difficulty']]) ? ($team[$v['check']][$v['qtype']][$v['difficulty']] += 1) : $team[$v['check']][$v['qtype']][$v['difficulty']] = 1;
                isset($team[$v['check']]['number']) ? ($team[$v['check']]['number'] += 1) : $team[$v['check']]['number'] = 1;
            }
            return getback(0,$team);
           }
        else{
            return getback(1,"该课程下没有题");
        }
    }
    /**    @author jyj
     *   查看试题列表（调用课堂的试题列表的方法）
     */
        public function classroomList(){
            $res = $this->questionList();
                return $res;
        }
        /**   试题详情
         * @author jyj
         *    完全调用课程下试题详情的方法
         */
        public function classroomDetail(){
                $res = $this->questionList();
                return $res;
        }
}
