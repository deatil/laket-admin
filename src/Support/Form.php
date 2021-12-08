<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

/**
 * 表单构建器
 *
 * @create 2021-3-18
 * @author deatil
 */
class Form
{
    /**
     * 生成Token
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public static function token($name = '__token__', $type = 'md5')
    {
        if (function_exists('token')) {
            return token($name, $type);
        }
        
        return '';
    }
    
    /**
     * 下拉选择框
     *
     * @param type $array 数据
     * @param type $id 默认选择
     * @param type $str 属性
     * @param type $default_option 默认选项
     * @return boolean|string
     */
    public static function select($array = array(), $id = 0, $str = '', $default_option = '')
    {
        $string = '<select ' . $str . '>';
        $default_selected = (empty($id) && $default_option) ? 'selected' : '';
        if ($default_option) {
            $string .= "<option value='' $default_selected>$default_option</option>";
        }

        if (!is_array($array) || count($array) == 0) {
            return false;
        }

        $ids = array();
        if (isset($id)) {
            $ids = explode(',', $id);
        }

        foreach ($array as $key => $value) {
            $selected = in_array($key, $ids) ? 'selected' : '';
            $string .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $string .= '</select>';
        return $string;
    }

    /**
     * 复选框
     *
     * @param $array 选项 二维数组
     * @param $id 默认选中值，多个用 '逗号'分割
     * @param $str 属性
     * @param $defaultvalue 是否增加默认值 默认值为 -99
     */
    public static function checkbox($array = array(), $id = '', $str = '', $defaultvalue = '', $field = '')
    {
        $string = '';
        
        if (!is_array($id)) {
            $id = trim($id);
            if ($id != '') {
                $id = strpos($id, ',') ? explode(',', $id) : array($id);
            }
        }
        
        if ($defaultvalue) {
            $string .= '<input type="hidden" ' . $str . ' value="-99">';
        }
        $i = 1;
        foreach ($array as $key => $value) {
            $key = trim($key);
            $checked = ($id && in_array($key, $id)) ? 'checked' : '';
            $string .= '<input type="checkbox" lay-skin="primary" ' . $str . ' id="' . $field . '_' . $i . '" ' . $checked . ' value="' . htmlspecialchars($key) . '" title="' . htmlspecialchars($value) . '"> ';

            $i++;
        }
        return $string;
    }

    /**
     * 图片上传
     *
     * @param string $name 表单名称
     * @param int $id 表单id
     * @param string $value 表单默认值
     * @param string $multiple 是否多图片
     * @param string $alowexts 允许图片格式
     * @param int $size 图片大小限制
     */
    public static function images(
        $name, 
        $id = '', 
        $value = '', 
        $multiple = 'false', 
        $size = 0
    ) {
        if (!$id) {
            $id = $name;
        }
        
        $string = "<div id='file_list_{$name}' class='uploader-list'>";
        if (!empty($value)) {
            $path = laket_attachment_url($value) ? laket_attachment_url($value) : "admin/admin/img/none.png";
            $string .= "<div class='file-item thumbnail'><img data-original='{$path}' src='{$path}' width='100' style='max-height: 100px;'><i class='iconfont icon-delete_fill remove-picture' data-id='{$value}'></i></div>";
        }
        
        $string .= "</div><input type='hidden' name='{$name}' data-multiple='{$multiple}' data-thumb='' data-size='{$size}' id='{$id}' value='{$value}'><div class='layui-clear'></div><div id='picker_{$name}'>上传单张图片</div>";
        return $string;
    }

}