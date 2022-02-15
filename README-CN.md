[English](./README.md) | 中文

<div align="center">

# LARAVEL ELOQUENT TRAIT

<p>
    <a href="https://github.com/biiiiiigmonster/laravel-cleanable/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/biiiiiigmonster/laravel-cleanable/releases" ><img src="https://img.shields.io/github/release/biiiiiigmonster/laravel-cleanable.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/biiiiiigmonster/laravel-cleanable"><img src="https://img.shields.io/packagist/dt/biiiiiigmonster/laravel-cleanable.svg?color=" /></a> 
    <a><img src="https://img.shields.io/badge/php-8.0.2+-59a9f8.svg?style=flat" /></a> 
</p>

</div>



# 环境

- laravel >= 9


# 安装

```bash
composer require biiiiiigmonster/laravel-cleanable
```

# 简介
“模型关联”是一个非常出色的概念，它可以有效的帮我们维护着数据之间复杂的关系。
一般来说，“删除”操作作为数据生命周期的最后一节，受到的关注度较小，我们往往在删除数据本身的同时可能会疏忽掉与之关联的模型数据的处理。
业务中模型完整的关联性也会因为这些残留数据而遭到破坏。

这个包可以很方便的帮您管理这些关联数据删除关系，仅仅只需要简单的定义。让我们来尝试一下吧！

# 使用
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Cleanable;
    
    protected array $cleanable = ['comments'];
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```
定义好这些关联后，
# 协议
[MIT](./LICENSE)
