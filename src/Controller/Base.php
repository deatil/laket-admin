<?php

declare (strict_types = 1);

namespace Laket\Admin\Controller;

use Laket\Admin\Http\Traits\Jump as JumpTrait;
use Laket\Admin\Http\Traits\View as ViewTrait;
use Laket\Admin\Http\BaseController;

/**
 * Base
 *
 * @create 2021-3-18
 * @author deatil
 */
abstract class Base extends BaseController
{
    use JumpTrait;
    use ViewTrait;

    /**
     * 控制器中间件
     *
     * @var array
     */
    protected $middleware = [];
    
    /**
     * 生成查询所需要的条件,排序方式
     */
    protected function buildparams()
    {
        $search_field = $this->request->param('search_field/s', '', 'trim');
        $keyword = $this->request->param('keyword/s', '', 'trim');
       
        $this->assign("search_field", $search_field);
        $this->assign("keyword", $keyword);

        $filter_time = $this->request->param('filter_time/s', '', 'trim');
        $filter_time_range = $this->request->param('filter_time_range/s', '', 'trim');
       
        $this->assign("filter_time", $filter_time);
        $this->assign("filter_time_range", $filter_time_range);

        $map = [];
        // 关键词搜索
        if ($search_field != '' && $keyword !== '') {
            $map[] = [$search_field, 'like', "%$keyword%"];
        }

        // 时间范围搜索
        if ($filter_time && $filter_time_range) {
            $filter_time_range = str_replace(' - ', ',', $filter_time_range);
            $arr = explode(',', $filter_time_range);
            !empty($arr[0]) ? $arr[0] : date("Y-m-d", strtotime("-1 day"));
            !empty($arr[1]) ? $arr[1] : date('Y-m-d', time());
            $map[] = [$filter_time, 'between time', [$arr[0] . ' 00:00:00', $arr[1] . ' 23:59:59']];
        }

        $map = apply_filters('admin_base_buildparams', $map);
        
        return $map;
    }

}
