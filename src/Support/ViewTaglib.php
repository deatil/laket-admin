<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

/**
 * 视图标签
 *
 * @create 2021-4-18
 * @author deatil
 */
class ViewTaglib
{
    /**
     * 注册的视图标签列表
     *
     * @var string[]
     */
    protected $taglibs = [];

    /**
     * 注册视图标签
     *
     * @param  string  $taglib
     * @return void
     */
    public function addTaglib($taglib)
    {
        $this->forgetTaglib($taglib);

        $this->taglibs[] = $taglib;
        
        return $this;
    }
    
    /**
     * 注册视图标签到最前面
     *
     * @param  string  $location
     * @return void
     */
    public function prependTaglib($taglib)
    {
        $this->forgetTaglib($taglib);

        array_unshift($this->taglibs, $taglib);
        
        return $this;
    }
    
    /**
     * 移除视图标签
     *
     * @param  string  $taglib
     * @return void
     */
    public function forgetTaglib($taglib)
    {
        if (($index = array_search($taglib, $this->taglibs)) !== false) {
            unset($this->taglibs[$index]);
        }
        
        return $this;
    }

    /**
     * 获取全部视图标签
     *
     * @return array
     */
    public function getTaglibs()
    {
        return $this->taglibs;
    }

    /**
     * 清空
     *
     * @return void
     */
    public function flush()
    {
        $this->taglibs = [];
        
        return $this;
    }

}
