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

## 使用
例如你的用户模型建立了一个手机模型关联，希望在删除了用户模型后能自动的清除其关联的手机模型数据。

```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{    
    /**
     * Get the posts for the user.
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```
To accomplish this, you may add the `BiiiiiigMonster\Cleans\Concerns\HasCleans` trait to the models you would like to auto-cleaned.
After adding one of the traits to the model, add the attribute name to the `cleans` property of your model.
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;

class User extends Model
{
    use HasCleans;
    
    /**
     * The relationships that will be auto-cleaned when deleted.
     * 
     * @var array 
     */
    protected $cleans = ['posts'];
}
```
Once the relationship has been added to the `cleans` list, it will be auto-cleaned when deleted.

### Cleaning At Runtime
At runtime, you may instruct a model instance to using the `clean` or `setCleans` method just like [`append`](https://laravel.com/docs/9.x/eloquent-serialization#appending-at-run-time):
```injectablephp
$user->clean('posts')->delete();

$user->setCleans(['posts'])->delete();
```

### 条件性清理
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;
use App\Cleans\PostClean;

class User extends Model
{
    use HasCleans;
    
    /**
     * The relationships that will be auto-cleaned when deleted.
     * 
     * @var array 
     */
    protected $cleans = [
        'posts' => PostClean::class
    ];
}
```

### 软删除传播

```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;
use App\Cleans\PostClean;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasCleans, SoftDeletes;
    
    /**
     * The relationships that will be auto-cleaned when deleted.
     * 
     * @var array 
     */
    protected $cleans = [
        'posts' => [PostClean::class, true]
    ];
}
```
如果你想要给全部的cleans设置软删除传播，直接在模型中添加属性：

```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasCleans, SoftDeletes;
    
    /**
     * Determine if propagate soft delete to the cleans.
     * 
     * @var bool 
     */
    protected $cleanWithSoftDelete = true;
    
    // ……
}
```
Tips：`cleans`中关联存在此配置项时，会覆盖掉`cleanWithSoftDelete`的设置

### 队列执行
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;
use App\Cleans\PostClean;

class User extends Model
{
    use HasCleans;
    
    /**
     * The relationships that will be auto-cleaned when deleted.
     * 
     * @var array 
     */
    protected $cleans = [
        'posts' => [PostClean::class, true, 'cleaning']
    ];
}
```
Tips：`cleans`中如果要配置执行队列，在这之前一定要配置上软删除清理的值

如果你想要给全部的cleans设置执行队列，直接在模型中添加属性：
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;

class User extends Model
{
    use HasCleans;
    
    /**
     * Execute clean use the queue.
     * 
     * @var string|null 
     */
    protected $cleanQueue = 'cleaning';
    
    // ……
}
```
Tips：`cleans`中关联存在此配置项时，会覆盖掉`cleanQueue`的设置

Similarly, `setCleanWithSoftDelete` and `setCleanQueue` support at runtime too.
```injectablephp
$user->setCleanWithSoftDelete(true)->delete();

$user->setCleanQueue('cleaning')->delete();
```

### 可清理关联类型

### Attribute
```injectablephp
namespace App\Models;

use BiiiiiigMonster\Cleans\Attributes\Clean;
use BiiiiiigMonster\Cleans\Concerns\HasCleans;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasCleans;
        
    /**
     * Get the posts for the user.
     *
     * @return HasMany
     */
    #[Clean(PostClean::class, true, 'cleaning')] 
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```
Tips：`#[Clean]` Attribute 的配置优先级最高，会覆盖其`cleans`中的同名配置

## Test
```shell
composer test
```

# 协议
[MIT](./LICENSE)
