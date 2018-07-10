ThinkPHP 5.1 + GatewayWorker的demo
===============
转自https://my.oschina.net/u/2394701/blog/860269
===============
原文用workerman写的，我用thinkphp5.1+GatewayWorker重写了一遍
## 目录结构

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─push        模块目录
│  │  ├─controller      控制器目录
│  │  │  ├─Events.php  业务处理类
│  │  │  ├─start_*.php workerman启动文件
│  │  ├─view            视图目录
├─start.php               命令行入口文件
~~~
linux在项目根目录直接通过php start.php start启动
windows切换到app/push/controller使用php start_xxxx.php start_xxxx.php start_xxxx.php 启动
