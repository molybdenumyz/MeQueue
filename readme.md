# NEUQer工作室内部laravel通用脚手架

## 简介

本脚手架适用于各种普通后端服务的业务逻辑开发，基于laravel 5.3版本开发

## 集成模块说明

### 基础用户系统

* 基于token的会话认证，支持多终端同时在线（client区分）

* 使用status验证用户的状态，可用于进行激活，封禁等操作

* 注册：
    
    在config->user.php中配置是否注册后是否需要进行用户验证激活以及激活的方式
    
* 登录：

    接受identifier和password，identifier在数据库中可映射的字段在控制器中设定

### 通用持久层解耦方案(Repository)

### RBAC权限认证系统

### 基于token的API认证服务

### 全局异常捕捉处理器

### 常用工具
