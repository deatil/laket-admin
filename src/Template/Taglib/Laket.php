<?php

declare (strict_types = 1);

namespace Laket\Admin\Template\Taglib;

use Laket\Admin\View\Laket\TagLib;

/**
 * 模版标签
 *
 * @create 2021-3-23
 * @author deatil
 */
class Laket extends Taglib
{
    // 标签定义
    protected $tags = [
        /**
         * 标签定义： 
         * attr 属性列表 
         * close 是否闭合（0 或者 1，默认 1） 
         * alias 标签别名 
         * level 嵌套层次
         */
        'template' => [
            'attr' => 'file', 
            'close' => 0,
        ],
        
        'execute' => [
            'attr' => 'sql,return', 
            'close' => 0,
        ],
        
        'hook' => [
            'attr' => 'name', 
            'close' => 1,
        ],
    ];
    
    /**
     * 加载模版
     */
    public function tagTemplate($tag, $content)
    {
        $templateFile = $tag['file'];
        
        if (0 === strpos($templateFile, '$')) {
            // 支持加载变量文件名
            $templateFile = $this->tpl->get(substr($templateFile, 1));
        }
        
        if (! file_exists($templateFile)) {
            $templateFile = app('laket-admin.view-finder')->find($templateFile);
        }
        
        // 读取内容
        $tmplContent = '';
        if ($templateFile) {
            $tmplContent .= file_get_contents($templateFile);
        }
        
        // 解析模板
        $this->tpl->parse($tmplContent);
        
        return $tmplContent;
    }
    
    /**
     * 执行 sql
     */
    public function tagExecute($tag, $content)
    {
        // 执行语句
        $sql = isset($tag['sql']) ? $tag['sql'] : '';
        
        // 返回数据
        $return = isset($tag['return']) ? $tag['return'] : 'sqldata';
        
        // 格式化
        $sql = addslashes($sql);
        
        $parse = '{php}';
        $parse .= '$' . $return . ' = \think\facade\Db::execute(\'' . $sql . '\');';
        $parse .= '{/php}';
        
        $this->tpl->parse($parse);
        
        return $parse;
    }
    
    /**
     * 使用过滤事件
     * 
     * 使用：
     * {hook name="hook_name"}...{/hook}
     */
    public function tagHook($tag, $content)
    {
        $name = $tag['name'];

        $parseStr = '<?php ob_start(); ?>';
        $parseStr .= $content;
        $parseStr .= '<?php 
                    $__hook_content = ob_get_clean();
                    echo apply_filters("'.$name.'", $__hook_content);
                    unset($__hook_content);
                    ?>';

        return $parseStr;
    }
    
}
