<?php
namespace app\service\model;
use think\Model;

class CourseCategories extends Model
{
    protected $pk = 'id';
    protected $auto = ['id'];

    protected function setIdAttr($val) {
        return $val ? $val : createGuid();
    }

    /*
     *根据机构id得到该机构及其所有子机构的课程类型信息
     *@param string $str 请求字段 
     *@param string $org_id 请求的机构id
     */
    public function queAll ($org_id, $str = '*') {
   //     return collection($this->where(['visible' => 0])->field($str)->select())->toArray();
        $org = new Organization();
        //得到机构下的$parentstr. $org_id，查询所有的子机构
        $parentstr = $org->where(['status' => 0, 'id' => $org_id])->value('parentstr');
        $son_str = $parentstr .  $org_id;
        $all_org_id = $org->where('status', 0)->where('parentstr', 'like', $son_str . '%')->field('id')->select();
        $org_ids = $org_id;
        foreach ($all_org_id as $val) {
            $org_ids .= ',' . $val['id'];
        }
        $all_cate = $this->where('visible', 0)->where('department', 'in', $org_ids)->field($str)->select();
        return $all_cate;
    }
    /*
     * 根据机构id得到课程类型信息（获取机构id到登陆用户所在机构id之间的所有课程类型）
     * @param string $id 机构id
     * @param string $u_org_id 登陆用户所在的机构id
     */
    public function queInfo($id, $u_org_id) {
        $org = new Organization();
        $parentarray = $org->getProtionOrg($u_org_id, $id, 'id');
        $parentstr = [];
        foreach ($parentarray as $val) {
            $parentstr[] = $val['id'];
        }
        $cate_info = $this->where('visible', 0)->where('department', 'in', $parentstr)->field('id, name')->select();
        if ($cate_info) {
            return $cate_info;
        } else {
            return false;
        }
    }
    
    /*
     * 根据条件获取类型
     */
    public function getInfo($where, $str = '*') {
        $where['visible'] = 0;
        $info = $this->where($where)->field($str)->select();
        if ($info) {
            return $info;
        } else {
            echo getBack(1, '获取类型失败！');
            exit;
        }
    }
    /*
     *根据用户请求参数处理数据
     */
    public function getInfoParam($str, $data) {
        switch ($str) {
            case 'queInfo':
                $user = new User();
                $u_org_id = $user->getOrgInfo($data['uid']);
                if (@empty($data['organization'])) {
                   $id = $u_org_id;
                } else {
                    $id = $data['organization'];
                }
                $re_data = $this->queInfo($id, $u_org_id);
                return $re_data ? getBack(0, $re_data) : getBack(0, '该机构下无类型！');
                break;
            case 'queAll':
                if (@empty($data['organization'])) {
                    $user = new User();
                    $org_id = $user->getOrgInfo($data['uid']);
                } else {
                    $org_id = $data['organization'];
                }
                $re_data = $this->queAll($org_id, 'id, name');
                return $re_data ? getBack(0, $re_data) : getBack(0, '该机构下无类型！');
                break;
            default:
                echo getBack(1, '请求错误！');
                exit;
                break;
        }
    }

}