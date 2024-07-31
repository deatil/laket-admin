<?php

declare (strict_types = 1);

namespace Laket\Admin\Support;

/**
 * 数组
 *
 * @create 2021-4-12
 * @author deatil
 */
class Arr
{
    /**
     * 数组深度合并
     *
     * @param array $arr1 数组
     * @param array $arr2 数组
     * @return array
     */
    public static function merge(array $arr1 = [], array $arr2 = [])
    {
        $merged = $arr1;
        
        if (empty($arr1)) {
            return $arr2;
        }
        
        if (empty($arr2)) {
            return $arr1;
        }
        
        foreach ($arr2 as $key => $value) {
            if (is_array($value) 
                && isset($merged[$key]) 
                && is_array($merged[$key])
            ) {
                $merged[$key] = self::merge($merged[$key], $value);
            } elseif (is_numeric($key)) {
                if (! in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }
        
        return $merged;
    }
    
    /**
     * 返回数组结构
     *
     * @param array $arr 输出的数据
     * @param string $blankspace 空格
     * @return string
     */
    public static function varExport($arr = [], $blankspace = '')
    {
        $filter = function($str) {
            return str_replace("'", "\'", $str);
        };
        
        $blank = '    ';
        $ret = "[\n";
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                $ret .= $blankspace . $blank;
                $ret .= (is_numeric($k) ? '' : "'".$filter($k)."' => ");
                $_type = strtolower(gettype($v));
                switch($_type){
                    case 'integer':
                        $ret .= $v.",";
                        break;
                    case 'array':
                        $ret .= self::varExport($v, $blankspace . $blank).",";
                        break;
                    case 'null':
                        $ret .= "NULL,";
                        break;
                    default:
                        $ret  .= "'".$filter($v)."',";
                        break;
                }
                $ret .= "\n";
            }
        }
        
        $ret .= $blankspace . "]";
        return $ret;
    }
    
    /**
     * 打印输出数据到文件
     *
     * @param mixed $data 输出的数据
     * @param boolean $force 强制替换
     * @param string|null $file 文件名称
     * @return boolean
     */
    public static function printr($data, $force = false, $file = null)
    {
        if (is_null($file)) {
            return false;
        }
        
        if (is_string($data)) {
            $str = $data;
        } else {
            if (is_array($data) || is_object($data)) {
                $str = print_r($data, true);
            } else {
                $str = var_export($data, true);
            }
        }
        
        $str = $str . PHP_EOL;
        
        if ($force) {
            file_put_contents($file, $str);
        } else {
            file_put_contents($file, $str, FILE_APPEND);
        }
        
        return true;
    }
}
