# SwooleMvc 

* 为兴趣而生 

## 介绍 
    
    本项目是一个运行在 php7 + swoole 环境中的 纯异步 php 框架 ， 已经实现 DI/MVC 分层/自定义路由/热启动等功能
    即将实现的功能在底部features
# 基本架构图

![image](https://github.com/rookiejin/swoole/raw/master/images/app.png)
## 项目结构: 
    ├── app 主目录 
    │   ├── Controller 控制器目录 可以自定义
    │   │   └── Home.php  控制器
    │   ├── helper.php 自定义函数
    │   └── Model 模型 
    ├── composer.json 
    ├── composer.lock
    ├── config 配置目录
    │   ├── app.php  应用 配置文件
    │   ├── router.php  路由配置文件
    │   └── server.php server配置文件
    ├── index.php  服务端入口 
    ├── reload.php 开发工具
    └── vendor  composer 仓库
## 示例： 

* 安装 
```php 
    composer require rookiejin/swoole
```
-  index.php 
```php
    <?php 
        require_once __DIR__ . '/vendor/autoload.php';
        
        $app = new \Rookiejin\Swoole\Application(__DIR__ . '/app') ;
        $app->bootstrap();
        $app->run();
```

- config/route.php 
```php 
    <?php 
    
        return [
            'home/index' => [
                'get,post',  // 请求方法
                'Home@index', // 控制器@执行方法  以\\ 开头为自定义命名空间 
            ],
            'home/get' => [
                ['get','put','post'], // 可以是逗号分隔的字符串也可以是数组
                'Home@get',
                ]
            ]
        ];

```
- app/Controller/Home.php 
```php
    <?php 
        
      /**
       *   不需要继承任何控制器 , 以返回字符串的方式进行页面返回 。 
       */
      class Home
      {
          public function index()
          {
              return "index";
          }
      }
```

### 开始：

    php index.php  & 
    
    访问: http:localhost:8888/home/index    
    
    输出 : index 
    
    访问： http:localhost:8888/home/
    输出： 404notfound 
   
### features

* ObjectPool 对象池 单独开辟出一个进程 管理对象池 业务程序将对象托管到对象池，极大的提升了 在请求初期所需要
创建一次所需要的对象的性能 。

* MysqlPool mysql连接池 服务器初始化的时候创建自定义个数的mysql长连接，提供业务程序获取 

* MysqlOrm CURD 必备 包括表关联 等api封装。 

* RouteCache 路由缓存，客户端路由请求尝试从缓存中获取，提高解析路由花费的时间[目前路由本身就是保存在内存中，
该功能待考虑]

* Template 自定义视图 

* 上传/验证码/csrf/session/cookie/加密/auth ... 等各种类库拓展

* Hook 系统钩子 ，也叫中间件 

* Filter 系统验证/过滤 

* ResetFulApi 纯服务端RESETFUL API 促进前后端分离 

* ... 暂时没想到

# 开发相关 

* 暂时代码都提交到master分支，等稳定了开个develop分支，master分支设为保护状态 
* (简单的开发手册)[https://github.com/rookiejin/swoole/issues/1]


































