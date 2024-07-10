<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use think\response\File;

/**
 * 代理静态文件
 *
 * @create 2024-7-10
 * @author deatil
 */
class Assets
{
    /**
     * 代理静态文件
     */
    public function show($flash, $path)
    {
        $path = $path . '.' . request()->ext();
        $contents = app('laket-admin.flash-asset')->getContent($flash, $path);

        $content = $contents['content'] ?? '';
        $type    = $contents['type']    ?? '';

        if ($content && $type) {
            $response = response($content, 200, [
                'Content-Type' => $type,
            ]);

            return $response;
        }

        return '';
    }
}
