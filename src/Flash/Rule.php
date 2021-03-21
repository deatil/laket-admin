<?php

declare (strict_types = 1);

namespace Laket\Admin\Flash;

use think\helper\Arr;

use Laket\Admin\Support\Tree;
use Laket\Admin\Model\AuthRule as AuthRuleModel;

/*
 * 规则
 *
 * @create 2021-3-21
 * @author deatil
 */
class Rule
{
    /**
     * 创建
     *
     * @return array $data 
     * @return int|string $parentId 
     *
     * @return array
     */
    public static function create($data = [], $parentId = 0) 
    {
        if (empty($data)) {
            return false;
        }
        
        $lastOrder = AuthRuleModel::max('listorder');
        
        $rule = AuthRuleModel::create([
            'parentid' => $parentId,
            'listorder' => $lastOrder + 1,
            'title' => Arr::get($data, 'title'),
            'url' => Arr::get($data, 'url'),
            'method' => Arr::get($data, 'method'),
            'slug' => Arr::get($data, 'slug'),
            'remark' => Arr::get($data, 'remark', ''),
        ]);
        
        $children = Arr::get($data, 'children', []);
        foreach ($children as $child) {
            static::create($child, $rule->id);
        }

        return $rule;
    }

    /**
     * 删除
     *
     * @param string $slug 规则slug
     *
     * @return boolean
     */
    public static function delete($slug)
    {
        $ids = self::getAuthRuleIdsBySlug($slug);
        if (! $ids) {
            return false;
        }
        
        collect($ids)->each(function($id) {
            AuthRuleModel::where([
                'id' => $id,
            ])->delete();
        });
        
        return true;
    }

    /**
     * 启用
     *
     * @param string $slug
     *
     * @return boolean
     */
    public static function enable($slug)
    {
        $ids = self::getAuthRuleIdsBySlug($slug);
        if (! $ids) {
            return false;
        }
        
        collect($ids)->each(function($id) {
            AuthRuleModel::where([
                    'id' => $id,
                ])
                ->update([
                    'status' => 1,
                ]);
        });
        
        return true;
    }

    /**
     * 禁用
     *
     * @param string $slug
     *
     * @return boolean
     */
    public static function disable($slug)
    {
        $ids = self::getAuthRuleIdsBySlug($slug);
        if (!$ids) {
            return false;
        }
        
        collect($ids)->each(function($id) {
            AuthRuleModel::where([
                    'id' => $id,
                ])
                ->update([
                    'status' => 0,
                ]);
        });
        
        return true;
    }

    /**
     * 导出指定slug的规则
     *
     * @param string $slug
     *
     * @return array
     */
    public static function export($slug)
    {
        $ids = self::getAuthRuleIdsBySlug($slug);
        if (!$ids) {
            return [];
        }
        
        $ruleList = [];
        $rule = AuthRuleModel::where([
                ['slug', '=', $slug]
            ])
            ->find();

        if ($rule) {
            $ruleList = AuthRuleModel::order('listorder', 'ASC')
                ->where([
                    ['id', 'in', $ids]
                ])
                ->select()
                ->toArray();
                
            $ruleList = Tree::create()
                ->withConfig('buildChildKey', 'children')
                ->withData($ruleList)
                ->build($rule['id']);
        }
        
        return $ruleList;
    }

    /**
     * 根据slug获取规则IDS
     *
     * @param string $slug
     *
     * @return array
     */
    public static function getAuthRuleIdsBySlug($slug)
    {
        $ids = [];
        $rules = AuthRuleModel::where([
                ['slug', '=', $slug]
            ])
            ->get()
            ->toArray();
        
       $ids = [];
       foreach ($rules as $rule) {
            if ($rule) {
                $ruleList = AuthRuleModel::order('listorder', 'ASC')
                    ->select(['id', 'parentid', 'slug'])
                    ->toArray();
                $ids = Tree::create()
                    ->getListChildrenId($ruleList, $rule['id']);
                $ids[] = $rule['id'];
            }
        });
        $ids = array_unique($ids);
        
        return $ids;
    }

}
