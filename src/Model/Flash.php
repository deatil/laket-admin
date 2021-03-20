<?php

declare (strict_types = 1);

namespace Laket\Admin\Model;

use Composer\Semver\Semver;

use think\facade\Cache;

/*
 * 闪存
 *
 * @create 2021-3-19
 * @author deatil
 */
class Flash extends ModelBase
{
    // 设置当前模型对应的数据表名称
    protected $name = 'laket_flash';
    
    // 设置主键名
    protected $pk = 'id';
    
    // 时间字段取出后的默认时间格式
    protected $dateFormat = false;
    
    protected $append = [
        'keywordlist',
        'authorlist',
        'settinglist',
        'setting_datalist',
    ];
    
    public function getKeywordlistAttr() 
    {
        $value = $this->keywords;
        if (empty($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }
    
    public function getAuthorlistAttr() 
    {
        $value = $this->authors;
        if (empty($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }
    
    public function getSettinglistAttr() 
    {
        $value = $this->setting;
        if (empty($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }
    
    public function getSettingDatalistAttr() 
    {
        $value = $this->setting_data;
        if (empty($value)) {
            return [];
        }
        
        return json_decode($value, true);
    }
    
    public static function onBeforeInsert($model)
    {
        $id = md5(mt_rand(10000, 99999) . microtime());
        $model->setAttr('id', $id);
        
        $model->setAttr('install_time', time());
        
        $model->setAttr('add_time', time());
        $model->setAttr('add_ip', request()->ip());
    }
    
    public static function onBeforeUpdate($model)
    {
        $model->setAttr('update_time', time());
        $model->setAttr('update_ip', request()->ip());
    }
    
    /**
     * 版本检测
     *
     * @return void
     */
    public static function versionSatisfies(string $name, string $constraints = null)
    {
        $data = static::where('name', $name)
            ->find();
        $version = $data['version'];
        
        try {
            $versionCheck =  Semver::satisfies($version, $constraints);
        } catch(\Exception $e) {
            return false;
        }
        
        return $versionCheck;
    }
    
    /**
     * 缓存闪存
     *
     * @return void
     */
    public static function getFlashs()
    {
        $data = Cache::get(md5('larket.model.flashs'));
        if (! $data) {
            $installData = self::order('listorder', 'ASC')
                ->order('install_time', 'ASC')
                ->select()
                ->toArray();
            
            $data = [];
            foreach ($installData as $item) {
                $data[$item['name']] = $item;
            }
            
            Cache::set(md5('larket.model.flashs'), $data, 0);
        }
        
        return $data;
    }
    
    /**
     * 清空缓存
     *
     * @return void
     */
    public static function clearCahce()
    {
        Cache::delete(md5('larket.model.flashs'));
    }
    
    /**
     * 检测是否安装
     *
     * @return void
     */
    public static function isInstall(string $name)
    {
        return static::where('name', $name)
            ->find();
    }

    /**
     * 判断是否启用
     *
     * @return bool
     */
    public static function enabled($name)
    {
        return static::where('name', $name)
            ->where('status', 1)
            ->find();
    }

    /**
     * 判断是否禁用
     *
     * @return bool
     */
    public static function disabled($name)
    {
        return ! $this->enabled($name);
    }
    
}