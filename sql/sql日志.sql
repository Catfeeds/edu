-- ----------------------------
-- 保留表数据 edu_access，edu_apps，edu_area，edu_node，edu_role，edu_role_user，
-- ----------------------------


-- ----------------------------
-- 表修改 2018/07/10
-- ----------------------------
ALTER TABLE `edu_exam_classroom_project`
ADD COLUMN `classroom`  char(32) NOT NULL COMMENT '课堂id' AFTER `quiz`,
ADD COLUMN `sections`  text NULL COMMENT '章或节id，逗号分隔' AFTER `classroom`;

-- ----------------------------
-- 表修改 2018/07/11
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step_data`
ADD INDEX `attemptstep_index` (`attemptstepid`) USING BTREE ;


-- ----------------------------
-- 表修改 2018/07/16
-- ----------------------------
ALTER TABLE `edu_exam_answer`
ADD INDEX `questionid_index` (`questionid`) USING BTREE ;

ALTER TABLE `edu_exam_classroom_question_attempt_step_data`
ADD COLUMN `seq`  int(3) NOT NULL DEFAULT 1 COMMENT '作答次数' AFTER `remarks`;

-- ----------------------------
-- 表修改 2018/07/20
-- ----------------------------
ALTER TABLE `edu_exam_classroom_quiz`
ADD COLUMN `pid`  char(32) NULL DEFAULT '0' COMMENT '试卷父id' AFTER `id`;
-- ----------------------------
-- 表修改 2018/07/23
-- ----------------------------

ALTER TABLE `edu_exam_question`
MODIFY COLUMN `seq`  bigint(10) NULL DEFAULT 0 COMMENT '添加题的顺序' AFTER `qtype`;

ALTER TABLE `edu_exam_classroom_quiz_attempts`
CHANGE COLUMN `quiz` `project`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '场次id' AFTER `id`;


-- ----------------------------
-- 表修改 2018/07/27
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step`
ADD COLUMN `answer`  text NULL COMMENT '答案' AFTER `sequencenumer`;
ALTER TABLE `edu_exam_classroom_question_attempt_step_data`
MODIFY COLUMN `answer`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '选项id' AFTER `fraction`;
ALTER TABLE `edu_exam_classroom_question_attempt_step`
ADD COLUMN `stu_answer`  text NULL COMMENT '学生答案' AFTER `answer`;
ALTER TABLE `edu_exam_classroom_question_attempt_step`
ADD COLUMN `grade`  decimal(10,5) NULL COMMENT '该题得分' AFTER `score`;


-- ----------------------------
-- 表修改 2018/08/1
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step`
MODIFY COLUMN `pid`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '父id' AFTER `questionid`;
-- ----------------------------
-- 表修改 2018/08/10
-- ----------------------------
ALTER TABLE `edu_exam_classroom_quiz_attempts`
MODIFY COLUMN `project`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '试卷模型id，可场次，可试卷' AFTER `id`;

-- ----------------------------
-- 表修改 2018/08/20
-- ----------------------------
ALTER TABLE `edu_user`
DROP INDEX `phone_index` ,
ADD INDEX `phone_index` (`phone`) USING BTREE ;

-- ----------------------------
-- 表修改 2018/08/27
-- ----------------------------
ALTER TABLE `edu_exam_answer`
MODIFY COLUMN `answerformat`  tinyint(2) NULL AFTER `answer`;
-- ----------------------------
-- 表修改 2018/08/29
-- ----------------------------
ALTER TABLE `edu_exam_answer`
MODIFY COLUMN `fraction`  decimal(12,0) NULL DEFAULT 0.0000000 COMMENT '正确答案标记' AFTER `answerformat`;

-- ----------------------------
-- 表修改 2018/09/05
-- ----------------------------
ALTER TABLE `edu_class`
MODIFY COLUMN `code`  varchar(10) NOT NULL COMMENT '班级号' AFTER `number`;

-- ----------------------------
-- 表修改 2018/09/19
-- ----------------------------
ALTER TABLE `edu_courseware`
MODIFY COLUMN `flag`  int(1) NOT NULL DEFAULT 0 COMMENT '0:课程的课件|1:章节的课件' AFTER `status`;

ALTER TABLE `edu_classroom_courseware`
ADD COLUMN `flag`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:课堂的课件|1:章节的课件' AFTER `timemodified`;

-- ----------------------------
-- 表修改 2018/09/23
-- ----------------------------
ALTER TABLE `edu_exam_classroom_project`
MODIFY COLUMN `check`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '1：考试未开始  2：考试中  3：考试结束  4：待审核  5：审核未通过' AFTER `attempts`;

ALTER TABLE `edu_exam_classroom_project`
ADD COLUMN `msg`  text NULL COMMENT '场次未通过原因' AFTER `attempts`;

-- ----------------------------
-- 表修改 2018/09/25
-- ----------------------------
ALTER TABLE `edu_classroom_sections`
MODIFY COLUMN `pid`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' COMMENT '父id' AFTER `summary`;

-- ----------------------------
-- 表修改 2018/09/29
-- ----------------------------
ALTER TABLE `edu_role`
MODIFY COLUMN `level`  tinyint(1) NULL DEFAULT 9 COMMENT '基本 1最高 9最低   1-3:管理员  4-6：教师  7-9：学生' AFTER `remark`;

-- ----------------------------
-- 表修改 2018/10/15
-- ----------------------------
ALTER TABLE `edu_role`
MODIFY COLUMN `level`  tinyint(1) NULL DEFAULT 9 COMMENT '基本 1最高 9最低 1-3:管理员 4-6：教师 7-9：学生' AFTER `remark`;

-- ----------------------------
-- 表修改 2018/10/17
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step`
MODIFY COLUMN `answer`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '正确答案id（answer表）' AFTER `sequencenumer`,
MODIFY COLUMN `stu_answer`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '学生答案（answer表id，或者学生输入）' AFTER `answer`;

ALTER TABLE `edu_exam_classroom_question_attempt_step_data`
DROP COLUMN `fraction`,
DROP COLUMN `score`,
DROP COLUMN `remarks`,
DROP COLUMN `seq`;

ALTER TABLE `edu_exam_question`
MODIFY COLUMN `pid`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '题父id' AFTER `id`;


-- ----------------------------
-- 表修改 2018/10/19
-- ----------------------------
ALTER TABLE `edu_assignment`
ADD COLUMN `pid`  char(32) NOT NULL DEFAULT '0' COMMENT '父id' AFTER `id`,
ADD COLUMN `parentstr`  text NOT NULL AFTER `pid`;

ALTER TABLE `edu_assignment`
MODIFY COLUMN `sectionid`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '作业所在章或节id  使用逗号连接多个id  例：×××,×××,  ' AFTER `courseid`,
DROP INDEX `sectionid_index`,
ADD INDEX `pid_index` (`pid`) USING BTREE ;

-- ----------------------------
-- 表修改 2018/10/22
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step`
ADD INDEX `attempt_index` (`attempts`) USING BTREE ;

ALTER TABLE `edu_exam_question`
ADD INDEX `pid_index` (`pid`) USING BTREE ,
ADD INDEX `courseid_index` (`courseid`) USING BTREE ,
ADD INDEX `questiontext_index` (`questiontext`(12)) USING BTREE ,
ADD INDEX `check_index` (`check`) USING BTREE ;

-- ----------------------------
-- 表修改 2018/10/24
-- ----------------------------
ALTER TABLE `edu_exam_answer`
MODIFY COLUMN `fraction`  tinyint(3) NULL DEFAULT 0 COMMENT '正确答案标记' AFTER `answerformat`;

ALTER TABLE `edu_exam_classroom_question_attempt_step`
MODIFY COLUMN `pid`  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '父id(本表id)' AFTER `questionid`;

-- ----------------------------
-- 表修改 2018/10/24
-- ----------------------------
ALTER TABLE `edu_exam_classroom_question_attempt_step_data`
MODIFY COLUMN `number`  int(5) NOT NULL DEFAULT 1 COMMENT '答案序号' AFTER `qtype`;


-- ----------------------------
-- 表修改 2018/10/24
-- ----------------------------
ALTER TABLE `edu_exam_question`
ADD COLUMN `sonnum`  tinyint(2) NULL COMMENT '子题数量' AFTER `modifiedbyid`;





