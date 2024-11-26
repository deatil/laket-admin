<?php

declare (strict_types = 1);

namespace Laket\Admin\Flash;

use think\File;

class Asset
{
    /**
     * @var array
     */
    protected array $namespaces = [];

    /**
     * @var string
     */
    protected string $mimeType;

    /**
     * 设置文件类型
     * 
     * @param  string $filename 文件名
     * @return $this
     */
    public function withMimeType(string $mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }
    
    /**
     * 添加命名空间
     *
     * @param $namespace
     * @param $path
     * @return $this
     */
    public function addNamespace(string $namespace, string $path): self
    {
        $this->namespaces[$namespace] = rtrim($path, '/\\');
        
        return $this;
    }

    /**
     * 获取文件类型
     *
     * @param $flashName 
     * @param $file
     * @return array
     */
    public function getContent(string $flashName, string $file)
    {
        if (! isset($this->namespaces[$flashName])) {
            return [];
        }
        
        $prefix = $this->namespaces[$flashName];
        
        $filePath = realpath($prefix . '/' . ltrim($file, '/\\')) ?: '';
        if (! str_starts_with($filePath, realpath($prefix))) {
            return [];
        }
        
        if (is_file($filePath)) {
            $extension = (new File($filePath))->extension();

            return [
                'type'    => $this->getMimeType($filePath),
                'content' => file_get_contents($filePath),
            ];
        }

        return [];
    }

    /**
     * 获取路径
     *
     * @param $flashName 
     * @param $file
     * @return string
     */
    public function getPath(string $flashName, string $file)
    {
        if (! isset($this->namespaces[$flashName])) {
            return '';
        }
        
        $prefix = $this->namespaces[$flashName];
        
        $filePath = realpath($prefix . '/' . ltrim($file, '/\\')) ?: '';
        if (! str_starts_with($filePath, realpath($prefix))) {
            return '';
        }
        
        if (is_file($filePath)) {
            return $filePath;
        }

        return '';
    }
    
    /**
     * 获取文件类型信息
     * 
     * @param  string $filename
     * @return string
     */
    protected function getMimeType(string $filename): string
    {
        if (!empty($this->mimeType)) {
            return $this->mimeType;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        return finfo_file($finfo, $filename);
    }
}
