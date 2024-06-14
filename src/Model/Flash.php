<?php

declare (strict_types = 1);

namespace Laket\Admin\Model;

use think\facade\Cache;
use Composer\Semver\Semver;

use Laket\Admin\Event;

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
        'requirelist',
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
    
    public function getRequirelistAttr() 
    {
        $value = $this->require;
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
        $id = md5(mt_rand(100000, 999999).microtime().uniqid());
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
     * 获取配置
     *
     * @return array
     */
    public static function getConfigs($name)
    {
        $info = Flash::where([
                "name" => $name,
            ])->find();
        if (empty($info)) {
            return [];
        }
        
        $settinglist = $info['settinglist'];
        $settingDatalist = $info['setting_datalist'];
        
        foreach ($settinglist as $value) {
            if (isset($settingDatalist[$value['name']])) {
                $value['value'] = $settingDatalist[$value['name']];
                
                switch ($value['type']) {
                    case 'array':
                        $settingDatalist[$value['name']] = json_decode($value['value'], true);
                        break;
                    case 'radio':
                        $settingDatalist[$value['name']] = isset($value['options'][$value['value']]) ? $value['value'] : '';
                        break;
                    case 'select':
                        $settingDatalist[$value['name']] = isset($value['options'][$value['value']]) ? $value['value'] : '';
                        break;
                    case 'checkbox':
                        if (empty($value['value'])) {
                            $settingDatalist[$value['name']] = [];
                        } else {
                            $valueArr = explode(',', $value['value']);
                            foreach ($valueArr as $v) {
                                if (isset($value['options'][$v])) {
                                    $settingDatalist[$value['name']][$v] = $value['options'][$v];
                                } elseif ($v) {
                                    $settingDatalist[$value['name']][$v] = $v;
                                }
                            }
                        }
                        break;
                    case 'image':
                        $settingDatalist[$value['name']] = !empty($value['value']) ? Attachment::getAttachmentUrl($value['value']) : '';
                        break;
                    case 'images':
                        if (!empty($value['value'])) {
                            $images_values = explode(',', $value['value']);
                            foreach ($value['value'] as $val) {
                                $settingDatalist[$value['name']][] = Attachment::getAttachmentUrl($val);
                            }
                        } else {
                            $settingDatalist[$value['name']] = [];
                        }
                        break;
                    default:
                        $settingDatalist[$value['name']] = $value['value'];
                        break;
                }
            }
        }
        
        // 事件
        $settingDatalist = apply_filters('FlashModelGetConfigs', $settingDatalist, $settinglist);
        
        return $settingDatalist;
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
            $versionCheck = Semver::satisfies($version, $constraints);
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
        $data = Cache::remember(md5('laket.model.flashs'), function() {
            $installData = static::order('listorder', 'ASC')
                ->order('install_time', 'ASC')
                ->select()
                ->toArray();
            
            $data = [];
            foreach ($installData as $item) {
                $data[$item['name']] = $item;
            }
            
            return $data;
        }, 0);
        
        return $data;
    }
    
    /**
     * 清空缓存
     *
     * @return void
     */
    public static function clearCahce()
    {
        Cache::delete(md5('laket.model.flashs'));
    }
    
    /**
     * 检测是否安装
     *
     * @return void
     */
    public static function isInstall(string $name)
    {
        return static::where('name', $name)->find();
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
    
    /**
     * 检测扩展依赖
     * 
     * @param array $requireExtensions
     * @return array
     */
    public static function checkRequireExtension(array $requireExtensions = [])
    {
        if (empty($requireExtensions)) {
            return [];
        }
        
        $requireExtensionNames = collect($requireExtensions)
            ->filter(function($data) {
                return !empty($data);
            })
            ->each(function($data, $key) {
                return $key;
            })
            ->map(function($data) {
                return $data;
            })
            ->toArray();
        $requireExtensionNames = array_values($requireExtensionNames);
        
        $extensions = Flash::whereIn('name', $requireExtensionNames)
            ->field('name,version')
            ->select()
            ->toArray();
        
        $installExtensions = [];
        foreach ($extensions as $k => $v) {
            $installExtensions[$v['name']] = $v['version'];
        }
        
        $data = [];
        foreach ($requireExtensions as $name => $version) {
            if (isset($installExtensions[$name])) {
                try {
                    $versionCheck = Semver::satisfies($installExtensions[$name], $version);
                } catch(\Exception $e) {
                    $versionCheck = false;
                }
                
                if ($versionCheck) {
                    $requireExtensionData = [
                        'name' => $name,
                        'version' => $version,
                        'install_version' => $installExtensions[$name],
                        'match' => true,
                    ];
                } else {
                    $requireExtensionData = [
                        'name' => $name,
                        'version' => $version,
                        'install_version' => $installExtensions[$name],
                        'match' => false,
                    ];
                }
            } else {
                $requireExtensionData = [
                    'name' => $name,
                    'version' => $version,
                    'install_version' => '',
                    'match' => false,
                ];
            }
            
            $data[] = $requireExtensionData;
        }
        
        return $data;
    }

}