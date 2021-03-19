<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Support\Tree;
use Laket\Admin\Model\AuthRule as AuthRuleModel;

/**
 * 后台菜单管理
 *
 * @create 2021-3-18
 * @author deatil
 */
class AuthRule extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        if ($this->request->isPost()) {

            $result = AuthRuleModel::order([
                    'listorder' => 'ASC', 
                    'id' => 'ASC',
                ])
                ->select()
                ->toArray();

            $Tree = new Tree();
            $menuTree = $Tree->withData($result)->buildArray(0);
            $menus = $Tree->buildFormatList($menuTree, 'title');
            $total = count($menus);
            
            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $menus
            ];
            return $this->json($result);
        } else {
            return $this->fetch('laket-admin::auth-rule.index');
        }
    }

    /**
     * 全部
     */
    public function all()
    {
        if ($this->request->isAjax()) {
            $limit = $this->request->param('limit/d', 20);
            $page = $this->request->param('page/d', 1);
            
            $searchField = $this->request->param('search_field/s', '', 'trim');
            $keyword = $this->request->param('keyword/s', '', 'trim');
            
            $map = [];
            if (!empty($searchField) && !empty($keyword)) {
                $map[] = [$searchField, 'like', "%$keyword%"];
            }
            
            $data = AuthRuleModel::where($map)
                ->page($page, $limit)
                ->order('url ASC, title ASC, id ASC')
                ->select()
                ->toArray();
            $total = AuthRuleModel::where($map)->count();
            
            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $data,
            ];
            return $this->json($result);
        } else {
            return $this->fetch('laket-admin::auth-rule.all');
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            
            if (!isset($data['menu_show'])) {
                $data['menu_show'] = 0;
            } else {
                $data['menu_show'] = 1;
            }
            
            if (!isset($data['status'])) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }

            $result = $this->validate($data, 'Laket\\Admin\\Validate\\AuthRule.insert');
            if (true !== $result) {
                return $this->error($result);
            }
            
            $res = AuthRuleModel::create($data);
            
            if ($res === false) {
                $this->error('添加失败！');
            }
            
            $this->success("添加成功！");
        } else {
            $parentid = $this->request->param('parentid/s', '');
            
            $menus = AuthRuleModel::order([
                'listorder', 
                'id' => 'DESC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            $menuTree = $Tree->withData($menus)->buildArray(0);
            $menus = $Tree->buildFormatList($menuTree, 'title');
            
            $this->assign("parentid", $parentid);
            $this->assign("menus", $menus);
            
            return $this->fetch('laket-admin::auth-rule.add');
        }
    }

    /**
     * 编辑后台菜单
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            
            $rs = AuthRuleModel::where([
                "id" => $data['id'],
            ])->find();
            if (empty($rs)) {
                $this->error('权限菜单不存在！');
            }
            
            if (!isset($data['menu_show'])) {
                $data['menu_show'] = 0;
            } else {
                $data['menu_show'] = 1;
            }
            
            if (!isset($data['status'])) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }
            
            $result = $this->validate($data, 'Laket\\Admin\\Validate\\AuthRule.update');
            if (true !== $result) {
                return $this->error($result);
            }
            
            $res = AuthRuleModel::where(['id' => $data['id']])->update($data);
            if ($res === false) {
                $this->error('编辑失败！');
            }
            
            $this->success("编辑成功！");
        } else {
            $id = $this->request->param('id/s', '');
            
            $data = AuthRuleModel::where(["id" => $id])->find();
            if (empty($data)) {
                $this->error('菜单不存在！');
            }
            
            $ruleList = AuthRuleModel::order([
                'listorder' => 'ASC', 
                'id' => 'DESC',
            ])->select()->toArray();
            
            $Tree = new Tree();
            $childsId = $Tree->getListChildsId($ruleList, $data['id']);
            $childsId[] = $data['id'];
            
            $ruleParentList = [];
            foreach ($ruleList as $r) {
                if (in_array($r['id'], $childsId)) {
                    continue;
                }
                
                $ruleParentList[] = $r;
            }
            
            $this->assign("data", $data);
            
            $menuTree = $Tree->withData($ruleParentList)->buildArray(0);
            $menus = $Tree->buildFormatList($menuTree, 'title');
            
            $this->assign("parentid", $data['parentid']);
            $this->assign("menus", $menus);
            
            return $this->fetch('laket-admin::auth-rule.edit');
        }

    }

    /**
     * 菜单删除
     */
    public function delete()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            $this->error('ID错误！');
        }
        
        $rs = AuthRuleModel::where(["id" => $id])->find();
        if (empty($rs)) {
            $this->error('权限菜单不存在！');
        }
        
        $result = AuthRuleModel::where(["parentid" => $id])->find();
        if (!empty($result)) {
            $this->error("含有子菜单，无法删除！");
        }
        
        $res = AuthRuleModel::where(["id" => $id])->delete();
        if ($res === false) {
            $this->error("删除失败！");
        }
        
        $this->success("删除成功！");
    }

    /**
     * 菜单排序
     */
    public function listorder()
    {
        $id = $this->request->param('id/s', 0);
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        
        $listorder = $this->request->param('value/d', 100);
        
        $rs = AuthRuleModel::where([
            'id' => $id,
        ])->update([
            'listorder' => $listorder,
        ]);
        if ($rs === false) {
            $this->error("菜单排序失败！");
        }
        
        $this->success("菜单排序成功！");
    }

    /**
     * 菜单显示状态
     */
    public function setmenu()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        
        $status = $this->request->param('status/d', 0);
        
        $rs = AuthRuleModel::where([
            'id' => $id,
        ])->update([
            'menu_show' => $status,
        ]);
        if ($rs === false) {
            $this->error('操作失败！');
        }
        
        $this->success('操作成功！');
    }

    /**
     * 菜单状态
     */
    public function setstate()
    {
        $id = $this->request->param('id/s');
        if (empty($id)) {
            $this->error('参数不能为空！');
        }
        
        $status = $this->request->param('status/d', 0);
        
        $rs = AuthRuleModel::where([
            'id' => $id,
        ])->update([
            'status' => $status,
        ]);
        if ($rs === false) {
            $this->error('操作失败！');
        }
        
        $this->success('操作成功！');
    }

}
