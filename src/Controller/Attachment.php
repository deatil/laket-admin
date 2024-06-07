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
        return $this->fetch('laket-admin::attachment.index');
    }
    
    /**
     * 附件列表页
     */
    public function indexData()
    {
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
            
        return $this->success('获取成功', '', [
            "count" => $total, 
            "list"  => $list,
        ]);
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

}
