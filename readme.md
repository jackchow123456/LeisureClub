## 快速开始

1.pull 下来该项目之后运行 `php artisan build`构建项目

2.`php artisan migrate --seed` 填充数据

3.`php artisan swoole:http start` 开启swoole-http服务


## 项目架构介绍
基本的项目架构看 ： [点这儿](https://laravelacademy.org/post/9529.html)
### App目录

###### Extend 目录
extend 目录包含了自行编写来扩展应用的扩展文件； 

###### Helpers 目录
helpers 目录是公共方法库；

###### Repository 目录
repository 目录是负责编写功能逻辑的库；

## 项目支持
一:  [jwt-auth](https://jwt-auth.readthedocs.io/en/develop/laravel-installation/), 用于实现单点登录。

二:  [qiniu-sdk](https://github.com/qiniu/php-sdk), 用于实现静态文件上传。

三:  [laravel-excel](https://github.com/Maatwebsite/Laravel-Excel), 用于execl上传，读取，导出。

四:  [laravel-swoole](https://github.com/swooletw/laravel-swoole), 用于实时通讯

五:  [laravel-sms](https://github.com/toplan/laravel-sms), 用于短信发送

六:  [textalk/websocket](https://github.com/Textalk/websocket-php), 用于模拟websocket客户端，实现转发




## 贡献




