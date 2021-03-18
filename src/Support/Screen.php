<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

/**
 * 屏幕锁屏
 *
 * @create 2021-3-18
 * @author deatil
 */
class Screen
{
    protected $key = 'laket_admin_screen';
    
    /**
     * 锁定
     */
    public function lock($value = '')
    {
        session($this->key, $value);
        
        return true;
    }
    
    /**
     * 解除锁定
     */
    public function unlock()
    {
        session($this->key, null);
        
        return true;
    }
    
    /**
     * 检测
     */
    public function check()
    {
        $data = session($this->key);
        if (empty($data)) {
            return false;
        }
        
        return $data;
    }
    
}
