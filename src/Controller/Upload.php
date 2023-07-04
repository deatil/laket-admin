<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Model\Attachment as AttachmentModel;

/**
 * 上传
 *
 * @create 2021-4-18
 * @author deatil
 */
class Upload extends Base
{
    /**
     * 文件上传
     */
    public function file()
    {
        $file = $this->request->file('file');
        
        $fileExists = AttachmentModel::where([
            'md5' => $file->hash('md5'),
        ])->find();
        
        if ($fileExists) {
            $filePath = AttachmentModel::objectUrl($fileExists['path']);
            
            AttachmentModel::where([
                'md5' => $file->hash('md5'),
            ])->data([
                'update_time' => time(),
            ])->update();
            
            return json([
                'code' => 0,
                'info' => $fileExists['name'] . '上传成功',
                'class' => 'success',
                'id' => $fileExists['id'],
                'path' => $filePath,
            ]);
        }
        
        // 判断附件格式是否符合
        $fileName = $file->getOriginalName();
        $fileExt = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
        
        try {
            $fileMine = $file->getMime();
        } catch (\Exception $ex) {
            return json([
                'code' => -1,
                'info' => '上传失败',
            ]);
        }
        
        if ($fileMine == 'text/x-php' || $fileMine == 'text/html') {
            return json([
                'code' => 0, 
                'info' => '禁止上传非法文件'
            ]);
        }
        
        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'])) {
            $uploadPath = 'images';
        } else {
            $uploadPath = 'files';
        }
        
        $savename = AttachmentModel::filesystem()
            ->putFile($uploadPath, $file, '');
        
        // 上传文件原始名称
        $name = $file->getOriginalName();
        
        // 获取附件信息
        $fileInfo = [
            'type' => 'admin',
            'type_id' => env('admin_id'),
            'name' => $name,
            'mime' => $file->getOriginalMime(),
            'path' => str_replace("\\", "/", $savename),
            'ext' => $file->getOriginalExtension(),
            'size' => $file->getSize(),
            'md5' => $file->hash('md5'),
            'sha1' => $file->hash('sha1'),
            'driver' => AttachmentModel::getFilesystemDefaultDisk(),
            'status' => 1,
        ];
        
        $fileAdd = AttachmentModel::create($fileInfo);
        if (! $fileAdd) {
            return json([
                'code' => 0, 
                'info' => '上传成功,写入数据库失败'
            ]);
        }
        
        $filePath = AttachmentModel::objectUrl($fileInfo['path']);
        
        return json([
            'code' => 0,
            'info' => $fileInfo['name'] . '上传成功',
            'class' => 'success',
            'id' => $fileAdd['id'],
            'path' => $filePath,
        ]);

    }

}
