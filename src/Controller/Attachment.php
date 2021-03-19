<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Model\Attachment as AttachmentModel;

/**
 * 附件管理
 *
 * @create 2021-3-18
 * @author deatil
 */
class Attachment extends Base
{
    /**
     * 附件列表页
     */
    public function index()
    {
        if ($this->request->isPost()) {
            $limit = $this->request->param('limit/d', 10);
            $page = $this->request->param('page/d', 1);
            $map = $this->buildparams();
            
            $list = AttachmentModel::where($map)
                ->page($page, $limit)
                ->order('create_time desc')
                ->select()
                ->toArray();
            if (! empty($list)) {
                foreach ($list as $k => &$v) {
                    $v['path'] = AttachmentModel::objectUrl($v['path']);
                }
                unset($v);
            }
            
            $total = AttachmentModel::where($map)
                ->order('create_time desc')
                ->count();
            $result = [
                "code" => 0, 
                "count" => $total, 
                "data" => $list,
            ];
            
            return $this->json($result);
        } else {
            return $this->fetch('laket-admin::attachment.index');
        }
    }
    
    /**
     * 附件详情
     */
    public function view()
    {
        $id = $this->request->param('id/s', null);
        if (empty($id)) {
            $this->error('请选择需要查看的附件！');
        }
        
        $data = AttachmentModel::where([
            'id' => $id,
        ])->find();
        if (empty($data)) {
            $this->error('附件不存在！');
        }
    
        $data['path'] = AttachmentModel::objectUrl($data['path']);
        
        $this->assign('data', $data);
        
        return $this->fetch('laket-admin::attachment.view');
    }
    
    /**
     * 附件删除
     */
    public function delete()
    {
        $ids = $this->request->param('ids/a', null);
        if (empty($ids)) {
            $this->error('请选择需要删除的附件！');
        }
        
        if (! is_array($ids)) {
            $ids = [0 => $ids];
        }
        
        $data = AttachmentModel::where([
                ['id', 'in', $ids],
            ])
            ->select();
        foreach ($data as $attachment) {
            AttachmentModel::where([
                'id' => $attachment['id'],
            ])->delete();
            
            // 删除实际文件
            AttachmentModel::deleteFile($attachment['path'], $attachment['driver']);
        }
        
        $this->success('文件删除成功！');
    }
    
    /**
     * 附件上传
     */
    public function upload()
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
                'info' => '禁止上传非法文件！'
            ]);
        }
        
        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'webp'])) {
            $uploadPath = 'images';
        } else {
            $uploadPath = 'files';
        }
        
        $savename = AttachmentModel::filesystem()
            ->putFile($uploadPath, $file);
        
        // 获取附件信息
        $fileInfo = [
            'type' => 'admin',
            'type_id' => env('admin_id'),
            'name' => $file->getOriginalName(),
            'mime' => $file->getOriginalMime(),
            'path' => $savename,
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
        
        return json([
            'code' => 0,
            'info' => $fileInfo['name'] . '上传成功',
            'id' => $fileAdd['id'],
            'path' => $fileInfo['path'],
        ]);

    }

}
