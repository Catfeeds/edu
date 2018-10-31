<?php
namespace app\admin\model;
use think\Model;
class Node extends Model {
    /**通过id查询node表数据
     * @param $id  nodeid
     * @return array|false|\PDOStatement|string|Model  查询出的节点
     */
    public function search($id) {
      $node = new Node;
      $data = $node->where('id', $id)->find();
      return $data;
    }
    /**
     * 添加节点
     * @param $data 需要添加的节点数组
     * @return int  1 添加成功 2 添加失败
     */
    public function addNod($data) {
       $node = new Node;
        $data['id'] = createGuid();
        $res = $node->save($data);
        if( $res != false ) {
            return 1;
        } else {
            return 2;
        }
    }
    /**
     * 查询node表获取所有节点，调用getChildrenid 方法，通过节点id查询子类节点
     * @param $nodeid   要查询子类节点的父级节点id
     * @return array 节点的子类id数组
     */
    public function getChildren($nodeid) {
        $node = new Node;
        $noderes = $node->select();
        return $this->getChildrenid($noderes, $nodeid);
    }
    /**
     * 递归查询子类id，保存在数组$arr中
     * @param $noderes  所有节点数组
     * @param $nodeid   父级id
     * @return array    父级节点的子类id数组
     */
    public function  getChildrenid($noderes, $nodeid) {
        static $arr = array();
        foreach ($noderes as $k=>$v) {
            if ( $nodeid == $v['pid'] ) {
                $arr[] = $v['id'];
                $this->getChildrenid($noderes, $v['id']);
            }
        }
        return $arr;
    }

    /**
     * 查询所有节点数据，调用topNode方法获取所有的最高级节点，并添加folg字段给每个节点编号
     * @return array  所有的最高级节点数组
     */
    public function nodeTree() {
        $node = new Node;
        $noderes = $node->select();
        return $node->topNode($noderes);
    }

    /**
     * 循环获取最高节点数组
     * @param $data  所有的节点数组
     * @param string $pid 节点pid
     * @param int $flog  最高级节点编号
     * @return array  所有的最高级节点数组
     */
    public function topNode($data, $pid = '0', $flog = 0) {
        static $trr = array();
        foreach ($data as $k => $v) {
            if ( $pid == $v['pid'] ) {
                $trr[] = array("title"=>$v['title'], "flog"=>$flog);
                $flog = $flog+1;
            }
        }
        return $trr;
    }
    /**
     * 查询所有节点数据，调用getChild方法构造节点树形数组
     * @return array  节点树形数组
     */
    public function getMenu() {
        $node = new Node;
        $data = $node->select();
        $arr = $this::getChild($data);
         return $arr;
    }
    /**
     * 判断所有节点中是否包含用户已经拥有的节点，如果拥有就在所有节点数组中添加flog=1来标志，否则flog为0
     * 用于自动勾选出当前角色已经有的节点
     * @param $nodes  所有的节点
     * @param $rolenoderes  角色拥有的节点
     * @return array  添加folg标志的所有节点树形数组
     */
    public function  roleFlog($nodes, $rolenoderes) {
        foreach ($nodes as $k=>$v){
            $v['flog'] = 0;
            foreach ($rolenoderes as $key=>$vo) {
                if ( $v['id'] == $vo['node_id'] ) {
                    $v['flog'] = 1;
                    break ;
                } else {
                    $v['flog'] = 0;
                }
            }
        }
        $arr = $this::getChild($nodes);
        return $arr;
    }
    /**
     * 递归构造树形数组
     * @param $data  所有节点
     * @param string $pid  节点pid
     * @return array  树形数组
     */
    static protected function getChild($data, $pid = '0') {
        $array = array();
        foreach ($data as $k => $v) {
            if ( $v['pid'] == $pid ) {
                $v['child'] = self::getChild($data, $v['id']);
                $array[] = $v;
            }
        }
        return $array;
    }

    /**
     * 删除子节点
     */
    public function delNodeAccess() {
        $nodeid = input('id');
        $sonids = $this->getChildren($nodeid);
        if ($sonids) {
           $this->where('id','in', $sonids)->delete();
            Access::where('node_id','in', $sonids)->delete();
        }
    }
}