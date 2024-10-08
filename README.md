# Nacosvel Database Manager

[![GitHub Tag](https://img.shields.io/github/v/tag/nacosvel/database-manager)](https://github.com/nacosvel/database-manager/tags)
[![Total Downloads](https://img.shields.io/packagist/dt/nacosvel/database-manager?style=flat-square)](https://packagist.org/packages/nacosvel/database-manager)
[![Packagist Version](https://img.shields.io/packagist/v/nacosvel/database-manager)](https://packagist.org/packages/nacosvel/database-manager)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/nacosvel/database-manager)](https://github.com/nacosvel/database-manager)
[![Packagist License](https://img.shields.io/github/license/nacosvel/database-manager)](https://github.com/nacosvel/database-manager)

## Installation

You can install the package via [Composer](https://getcomposer.org/):

```bash
composer require nacosvel/database-manager
```

## 文档

因不同框架的容器对象不同，需要借助 `nacosvel/container-interop` 完成容器交互。

```php
use Nacosvel\Container\Interop\Discover;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Discover::container();
    }

}
```

> 不同框架实现方式可能不一致，可以在服务提供者中实现容器发现功能。
> 具体操作查看 [nacosvel/container-interop](https://github.com/nacosvel/container-interop/blob/main/README.md)

如果当前使用的框架可以通过`容器['db']`方式获取数据库管理对象，可以跳过本章节。

```php
use App\Support\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Nacosvel\Container\Interop\Discover;
use Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(DatabaseManagerInterface::class, function () {
            return new DatabaseManager($this->app['db']);
        });
        Discover::container();
    }

}
```

> 自定义 `App\Support\DatabaseManager` 数据库管理对象，并实现 `Nacosvel\Contracts\DatabaseManager\DatabaseManagerInterface`
> 接口，然后绑定到容器。

## License

Nacosvel Contracts is made available under the MIT License (MIT). Please see [License File](LICENSE) for more information.
