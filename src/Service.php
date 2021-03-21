<?php

declare (strict_types = 1);

namespace Laket\Admin;

use think\facade\Event;

use Laket\Admin\Flash\Manager;
use Laket\Admin\Support\Form;
use Laket\Admin\Support\Loader;
use Laket\Admin\Support\Password;
use Laket\Admin\Support\ViewFinder;
use Laket\Admin\Support\Service as BaseService;
use Laket\Admin\Http\JsonResponse as HttpJsonResponse;
use Laket\Admin\Auth\Admin as AuthAdmin;
use Laket\Admin\Auth\Permission as AuthPermission;

// 引用文件夹
use Laket\Admin\Command;
use Laket\Admin\Middleware;
use Laket\Admin\Event as AdminEvent;
use Laket\Admin\Listener as AdminListener;

/**
 * 服务
 *
 * @create 2021-3-17
 * @author deatil
 */
class Service extends BaseService
{
    /**
     * 别名
     */
    protected $alias = [
        'HtmlForm' => Form::class,
    ];
    
    /**
     * 脚本
     *
     * @var array
     */
    protected $commands = [
        Command\Install::class,
        Command\ResetPassword::class,
    ];

    /**
     * 路由中间件
     *
     * @var array
     */
    protected $routeMiddleware = [
        'laket-admin' => [
            'laket-admin.auth' => Middleware\Auth::class,
            'laket-admin.permission' => Middleware\Permission::class,
            'laket-admin.screen-lock' => Middleware\ScreenLockCheck::class,
        ]
    ];
    
    /**
     * 注册
     */
    public function register()
    {
        $this->registerGlobalConfig();
        
        $this->registerAlias();
        
        $this->registerBind();
    }
    
    /**
     * boot
     */
    public function boot()
    {
        $this->bootCommand();
        
        $this->bootView();
        
        $this->bootRouter();
        
        $this->bootMiddleware();
        
        $this->bootEvent();
        
        $this->bootFlash();
    }
    
    /**
     * 别名
     */
    protected function registerAlias() 
    {
        foreach ($this->alias as $alias => $class) {
            class_alias($class, $alias);
        }
    }
    
    /**
     * 全局配置
     */
    protected function registerGlobalConfig() 
    {
        $viewPath = __DIR__ . '/../resources/view';
        
        $layout = $viewPath . DIRECTORY_SEPARATOR . 'layout.html';
        $inputItem = $viewPath . DIRECTORY_SEPARATOR . 'inputItem.html';
        
        // 设置环境变量
        $this->app->env->set([
            // 页面变量
            'laket_admin_layout' => $layout,
            'laket_admin_input_item' => $inputItem,
        ]);
        
        // 设置公用参数
        $this->app->view->assign([
            'laket_admin_layout' => $layout,
            'laket_admin_input_item' => $inputItem,
        ]);
        
        // 配置
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/laket.php', 'laket');
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/laket_exception.php', 'laket_exception');
        
        // 验证码配置
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/captcha.php', 'captcha');
    }
    
    /**
     * 注册绑定
     *
     * @return void
     */
    protected function registerBind()
    {
        // 视图
        $this->app->bind('laket-admin.view-finder', function() {
            $viewFinder = new ViewFinder();
            $viewFinder->addLocation($this->app->getAppPath() . '/view');
            
            return $viewFinder;
        });
        
        // 导入器
        $this->app->bind('laket-admin.loader', Loader::class);
        
        // json响应
        $this->app->bind('laket-admin.response', function() {
            $httpJsonResponse = new HttpJsonResponse();
            
            $config = config('laket.response.json');
            $httpJsonResponse
                ->withIsAllowOrigin($config['is_allow_origin'])
                ->withAllowOrigin($config['allow_origin'])
                ->withAllowCredentials($config['allow_credentials'])
                ->withMaxAge($config['max_age'])
                ->withAllowMethods($config['allow_methods'])
                ->withAllowHeaders($config['allow_headers'])
                ->withExposeHeaders($config['expose_headers']);
            
            return $httpJsonResponse;
        });

        // 密码
        $this->app->bind('laket-admin.password', Password::class);
        
        // 权限
        $this->app->bind('laket-admin.auth-permission', function() {
            $authPermission = new AuthPermission();
            
            return $authPermission;
        });
        
        // 登陆信息
        $this->app->bind('laket-admin.auth-admin', function() {
            $authAdmin = new AuthAdmin();
            
            return $authAdmin;
        });
        
        // 闪存
        $this->app->bind('laket-admin.flash', Manager::class);
    }
    
    /**
     * 脚本
     */
    public function bootCommand()
    {
        $this->commands($this->commands);
    }

    /**
     * 视图
     *
     * @return void
     */
    public function bootView()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/view', 'laket-admin');
    }
    
    /**
     * 路由
     *
     * @return void
     */
    protected function bootRouter()
    {
        // 路由
        $this->loadRoutesFrom(__DIR__ . '/../resources/routes/admin.php');
    }
    
    /**
     * 中间件
     *
     * @return void
     */
    public function bootMiddleware()
    {
        $this->app->middleware->add(Middleware\ExceptionHandler::class);
    
        // 配置路由中间件
        $middleware = config('laket.middleware', []);
        foreach ($this->routeMiddleware as $key => $routeMiddleware) {
            $middleware['alias'][$key] = $routeMiddleware;
        }
        
        config([
            'middleware' => $middleware,
        ], 'laket');
    }
    
    /**
     * 事件
     *
     * @return void
     */
    public function bootEvent()
    {
        Event::listen(
            AdminEvent\MainUrl::class, 
            AdminListener\MainUrl::class
        );
    }
    
    /**
     * 闪存插件
     *
     * @return void
     */
    protected function bootFlash()
    {
        app('laket-admin.flash')->bootFlash();
    }

}
