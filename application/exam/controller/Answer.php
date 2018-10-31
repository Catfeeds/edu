<?php
namespace app\exam\controller;

use app\service\controller\Common;
use app\exam\model\ExamAnswer;
use app\exam\model\ExamQuestion;

class Answer extends Common
{
    public function __construct(){
        parent::__construct();
    }

    //查询试题详情数据
    public function getQuizQuestionList ($res) {

      $answerinfo = model('ExamAnswer')->getExamAnswer($res);

          if ($answerinfo) {
            return $answerinfo;
          } else {
            return false;
          }

    }


    //查询阅读理解试题详情数据
    public function getReadQuizQuestionList ($res) {

      $answerinfo = model('ExamAnswer')->getReadExamAnswer($res);

      if ($answerinfo) {
        return $answerinfo;
      } else {
        return false;
      }

    }


        //选择题    
        public function getChoice ($res) {

        $info = $this->getQuizQuestionList($res);

        foreach ($info as $k => $v) {
          $arr[$k]['qtype'] = $v['qtype'];
          $arr[$k]['answer'] = $v['answer'];
          $arr[$k]['fraction'] = $v['fraction'];
          $arr[$k]['tab'] = $v['tab'];
          $arr[$k]['img'] = $v['img'];
        }
          if ($arr) {
            return $arr;
          } else {
            return false;
          }

        }
         //填空题    
        public function getShortanswer ($res) {

        $info = $this->getQuizQuestionList($res);

        foreach ($info as $k => $v) {
          $arr[$k][$info[$k]['id']] = $v['answer'];
        }
          if ($arr) {
          return $arr;
          } else {
          return false;
          }
        }
        //判断题    
        public function getTruefalse ($res) {

        $info = $this->getQuizQuestionList($res);

        foreach ($info as $k => $v) {
            $arr[$k]['fraction'] = $v['fraction'];
        }
          if ($arr) {
            return $arr;
          } else {
            return false;
          }

        }
        //匹配题    
        public function getMatch ($res) {

          $ExamQuestion = new ExamQuestion();

          $field = "p.qtype,p.id";

          $infoma = collection($ExamQuestion->alias('p')
              ->join('__EXAM_ANSWER__ cq','cq.questionid = p.id')
              ->where('p.pid',$res)
              ->where('p.status',0)
              ->field($field)
              ->select())->toArray();

          foreach ($infoma as $k => $v) {
            $info = $this->getQuizQuestionList($v['id']);
            foreach ($info as $k1 => $v1) {
              $mes = $this->getExamQuestion($v1['questionid'],$v1['qtype']);
            foreach ($mes as $k2 => $v2) {
              $arr[$k2]['name'] = $v2['questiontext'];
            }
              $arr[$k1]['id'] = $v1['id'];
              $arr[$k1]['answer'] = $v1['answer'];
            }
          }
            if ($arr) {
              return $arr;
            } else {
              return false;
            }
        }
        //简答题   
        public function getEssay ($res) {

        $info = $this->getQuizQuestionList($res);

        foreach ($info as $k => $v) {
          $arr[$k]['answer'] = $v['answer'];
        }
         if ($arr) {
           return $arr;
         } else {
          return false;
         }
        }
        //综合题    
        public function getComprehensive ($res) {

          $ExamQuestion = new ExamQuestion();

          $field = "p.qtype,p.id";

          $infoma = collection($ExamQuestion->alias('p')
              ->join('__EXAM_ANSWER__ cq','cq.questionid = p.id')
              ->where('p.pid',$res)
              ->where('p.status',0)
              ->field($field)
              ->select())->toArray();

          foreach ($infoma as $k => $v) {
            $info = $this->getQuizQuestionList($v['id']);
            foreach ($info as $k1 => $v1) {
              $mes = $this->getExamQuestion($v1['questionid'],$v1['qtype']);
            foreach ($mes as $k2 => $v2) {
              $arr[$k2]['name'] = $v2['questiontext'];
            }
              $arr[$k1]['id'] = $v1['id'];
              $arr[$k1]['answer'] = $v1['answer'];
            }
          }
             if ($arr) {
               return $arr;
             } else {
              return false;
             }
        }

        //阅读理解的选择题  
        public function getReadChoice ($infoma) {

        $info = $this->getReadQuizQuestionList($infoma);

        foreach ($info as $k => $v) {
            $arr[$k]['id'] = $v['id'];
            $arr[$k]['name'] = $v['answer'];
            $arr[$k]['fraction'] = $v['fraction'];
            $arr[$k]['tab'] = $v['tab'];
            $arr[$k]['imgs'] = $v['img'];
          }
              if ($arr) {
               return $arr;
             } else {
              return false;
             }
        }

        //阅读理解的判断题  
        public function getReadTruefalse ($infoma) {

        $info = $this->getReadQuizQuestionList($infoma);

        foreach ($info as $k => $v) {
            $arr[$k]['id'] = $v['id'];
            $arr[$k]['fraction'] = $v['fraction'];
          }
              if ($arr) {
               return $arr;
             } else {
              return false;
             }
        }

        //阅读理解的填空题  
        public function getReadShortanswer ($infoma) {

        $info = $this->getReadQuizQuestionList($infoma);

        foreach ($info as $k => $v) {
            $arr[$info['id']] = $info['answer'];
          }
              if ($arr) {
               return $arr;
             } else {
              return false;
             }
        }

        //阅读理解的简答题  
        public function getReadEssay ($infoma) {

        $info = $this->getReadQuizQuestionList($infoma);

        foreach ($info as $k => $v) {
            $arr[$info['id']] = $info['answer'];
          }
              if ($arr) {
               return $arr;
             } else {
              return false;
             }
        }

        //查询字问题的数据
        public function getExamQuestion ($res,$infoma) {

          $ExamQuestion = new ExamQuestion();
          $mes = collection($ExamQuestion->where('pid',$res)->where('qtype',$infoma)->field('pid,questiontext,qtype,img')->select())->toArray();

              if ($mes) {
               return $mes;
             } else {
              return false;
             }
        }

        //阅读理解
        public function getReadingcomprehension ($res) {
        
        $ExamQuestion = new ExamQuestion();

        $field = "p.qtype,p.id";

        $infomaex = collection($ExamQuestion->alias('p')
            ->join('__EXAM_ANSWER__ cq','cq.questionid = p.id')
            ->where('p.pid',$res)
            ->where('p.status',0)
            ->field($field)
            ->select())->toArray();

        foreach ($infomaex as $key => $value) {

          $infoma = $this->getQuizQuestionList($value['id']);

          $info1 = array();
          $info2 = array();
          $info3 = array();
          $info4 = array();
          $info5 = array();

          foreach ($infoma as $k => $v) {
            switch ($infoma[$k]['qtype']) {

              case 'singlechoice':
                  $answer = $this->getReadChoice($infoma[$k]['id']);
                  foreach ($answer as $k1 => $v1) {
                    $arr1[] = $v1;
                  }

                  $mes = $this->getExamQuestion($res,$infoma[$k]['qtype']);
                  foreach ($mes as $k2 => $v2) {
                        $info1 = [
                            'id' =>$v2['pid'],
                            'name' =>$v2['questiontext'],
                            'qtype' =>$v2['qtype'],
                            'imgs' =>$v2['img']
                        ];
                  } 
                  $info1[$k]['answer'] = $arr1;
                  //单选择题    
                  break;

              case 'multiplechoice':               
                  $answer = $this->getReadChoice($infoma[$k]['id']);
                  foreach ($answer as $k3 => $v3) {
                    $arr2[] = $v3;
                  }
                  $mes = $this->getExamQuestion($res,$infoma[$k]['qtype']);
                  foreach ($mes as $k4 => $v4) {
                    $info2 = [
                            'id' =>$v4['pid'],
                            'name' =>$v4['questiontext'],
                            'qtype' =>$v4['qtype'],
                            'imgs' =>$v4['img']
                        ];
                  }
                  $info2[$k]['answer'] = $arr2;
                  //多选择题    
                  break;

                  case 'truefalse':
                  $answer = $this->getReadTruefalse($infoma[$k]['id']);
                  foreach ($answer as $k5 => $v5) {
                    $arr3[] = $v5;
                  }
                  $mes = $this->getExamQuestion($res,$infoma[$k]['qtype']);
                  foreach ($mes as $k6 => $v6) {
                    $info3 = [
                            'id' =>$v6['pid'],
                            'name' =>$v6['questiontext'],
                            'qtype' =>$v6['qtype'],
                        ];
                  }
                  $info3[$k]['answer'] = $arr3;

                  //判断题    
                  break;

                  case 'shortanswer':
                  $answer = $this->getReadShortanswer($infoma[$k]['id']);
                  foreach ($answer as $k7 => $v7) {
                    $arr4[] = $v7;
                  }
                  $mes = $this->getExamQuestion($res,$infoma[$k]['qtype']);
                  foreach ($mes as $k8 => $v8) {
                    $info4 = [
                            'id' =>$v8['pid'],
                            'name' =>$v8['questiontext'],
                            'qtype' =>$v8['qtype'],
                        ];
                  }
                  $info4[$k]['answer'] = $arr4;
                  //填空题    
                  break;

                  case 'essay':
                  $answer = $this->getReadEssay($infoma[$k]['id']);
                  foreach ($answer as $k9 => $v9) {
                    $arr5[] = $v9;
                  }
                  $mes = $this->getExamQuestion($res,$infoma[$k]['qtype']);
                  foreach ($mes as $k10 => $v10) {
                    $info5 = [
                            'id' =>$v10['pid'],
                            'name' =>$v10['questiontext'],
                            'qtype' =>$v10['qtype'],
                        ];
                  }
                  $info5[$k]['answer'] = $arr5;
                  //简答题    
                  break;
            }
          }
        }
            $arr['answer'] = [$info1,$info2,$info3,$info4,$info5];
            if ($arr) {
              return $arr;
            } else {
              return false;
            }
    }

}