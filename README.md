# PHP Pure Admin

PHP Pure Admin 是一个基于 Laravel 和 Vue 的后台管理系统框架。这个项目利用了强大的 [Laravel](https://github.com/laravel/laravel) 框架来处理后端逻辑，以及使用优质的 [vue-pure-admin](https://github.com/pure-admin/vue-pure-admin) 项目模板来构建前端界面。

## 特性

- **现代技术栈**: 使用最新版本的 Laravel 和 Vue.js，保证了项目的现代性和高效性。
- **灵活性**: 可以轻松地根据你的业务需求进行定制和扩展。
- **响应式设计**: 确保在各种设备上都有良好的用户体验。

## 开始使用

### 先决条件

在开始之前，确保你的开发环境中已安装了以下软件：

- PHP >= v8.2
- Composer >= v2.7
- Node.js >= v18
- pnpm >= v9

### 运行后端服务

克隆仓库：

```bash
git clone https://github.com/wish-cloud/php-pure-admin-backend.git
```

安装依赖：

```bash
cd php-pure-admin-backend
composer install
```

配置文件：
复制 .env.example 文件为 .env，并根据你的环境配置数据库和其他服务。

```bash
cp .env.example .env
```

运行数据库迁移：

```bash
php artisan migrate
```

启动服务：

```bash
php artisan serve
```

细节部分请参考 [Laravel文档](https://laravel.com/docs/11.x/)

### 运行前端项目

克隆仓库：

```bash
git clone https://github.com/wish-cloud/php-pure-admin-frontend.git
```

安装依赖：

```bash
cd php-pure-admin-frontend
pnpm install
```

启动项目：

```bash
pnpm dev
```

现在，你可以在浏览器中访问 <http://localhost:8848> 来查看你的后台管理系统。

## 贡献

我们欢迎所有形式的贡献，无论是小的修复，功能添加，或是代码和文档的改进。请首先讨论你期望的变更，然后提交一个拉取请求。

## 许可证

[MIT license](https://opensource.org/licenses/MIT)
