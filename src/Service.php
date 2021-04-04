<?php

declare (strict_types = 1);

namespace Laket\Admin;

use Laket\Admin\Flash\Manager;
use Laket\Admin\Support\Form;
use Laket\Admin\Support\Loader;
use Laket\Admin\Support\Password;
use Laket\Admin\Support\ViewFinder;
use Laket\Admin\Support\Publish;
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
        Command\Publish::class,
        Command\Install::class,
        Command\ResetPassword::class,
    ];

    /**
     * 路由中间件
     *
     * @var array
     */
    protected $routeMiddleware = [
        'laket-admin.auth' => Middleware\Auth::class,
        'laket-admin.permission' => Middleware\Permission::class,
        'laket-admin.screen-lock' => Middleware\ScreenLockCheck::class,
    ];

    /**
     * 路由中间件分组别名
     *
     * @var array
     */
    protected $middlewareGroups = [
        'laket-admin' => [
            'laket-admin.auth',
            'laket-admin.permission',
            'laket-admin.screen-lock',
        ]
    ];
    
    /**
     * 注册
     */
    public function register()
    {
        $this->registerConfig();
        
        $this->registerAlias();
        
        $this->registerBind();
        
        $this->registerMiddleware();
        
        $this->registerCommand();
        
        $this->registerPublishes();
    }
    
    /**
     * boot
     */
    public function boot()
    {
        $this->bootView();
        
        $this->bootRouter();
        
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
    protected function registerConfig() 
    {
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
            
            // 加载配置的视图路径
            $config = config('laket.view');
            if (isset($config['paths']) 
                && is_array($config['paths'])
            ) {
                foreach ($config['paths'] as $viewPath) {
                    $viewFinder->addLocation($viewPath);
                }
            }
            
            return $viewFinder;
        });
        
        // 导入器
        $this->app->bind('laket-admin.loader', Loader::class);
        
        // 推送
        $this->app->bind('laket-admin.publish', Publish::class);
        
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
    public function registerCommand()
    {
        $this->commands($this->commands);
    }
    
    /**
     * 推送
     *
     * @return void
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            // 静态文件 
            // php think laket-admin:publish --tag=laket-admin-assets
            $this->publishes([
                __DIR__ . '/../resources/assets/' => public_path() . 'static/admin/',
            ], 'laket-admin-assets');
            
            // 配置文件 
            // php think laket-admin:publish --tag=laket-admin-config
            $this->publishes([
                __DIR__ . '/../resources/config/laket.php' => config_path() . 'laket.php',
            ], 'laket-admin-config');
            
            // 视图文件，需要修改系统页面时候可以执行命令
            // php think laket-admin:publish --tag=laket-admin-views
            $this->publishes([
                __DIR__ . '/../resources/views/' => app_path() . 'view/vendor/laket-admin/',
            ], 'laket-admin-views');
        }
    }

    /**
     * 中间件
     *
     * @return void
     */
    public function registerMiddleware()
    {
        $this->app->middleware->add(Middleware\ExceptionHandler::class);
        
        // 中间件配置
        $middleware = config('middleware', []);
        
        // 路由中间件别名
        foreach ($this->routeMiddleware as $key => $routeMiddleware) {
            if (isset($middleware['alias'][$key])) {
                unset($middleware['alias'][$key]);
            }
            
            $middleware['alias'][$key] = $routeMiddleware;
        }
        
        // 路由中间件分组
        foreach ($this->middlewareGroups as $group => $middlewareGroup) {
            if (isset($middleware['alias'][$group])) {
                unset($middleware['alias'][$group]);
            }
            
            $middleware['alias'][$group] = $middlewareGroup;
        }
        
        config($middleware, 'middleware');
    }
    
    /**
     * 视图
     *
     * @return void
     */
    public function bootView()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laket-admin');
        
        // 设置公用参数
        $this->app->view->assign([
            'laket_admin_layout' => app('laket-admin.view-finder')->find('laket-admin::layout'),
        ]);
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
     * 闪存插件
     *
     * @return void
     */
    protected function bootFlash()
    {
        app('laket-admin.flash')->bootFlash();
    }

}
