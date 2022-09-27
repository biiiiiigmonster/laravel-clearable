[English](./README.md) | 中文

<div align="center">

# LARAVEL ELOQUENT TRAIT

<p>
    <a href="https://github.com/biiiiiigmonster/laravel-clearable/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/biiiiiigmonster/laravel-clearable/releases" ><img src="https://img.shields.io/github/release/biiiiiigmonster/laravel-clearable.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/biiiiiigmonster/laravel-clearable"><img src="https://img.shields.io/packagist/dt/biiiiiigmonster/laravel-clearable.svg?color=" /></a> 
</p>

</div>



# 环境

- laravel >= 9


# 安装

```bash
composer require biiiiiigmonster/laravel-clearable
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

class User extends Model
{    
    /**
     * Get the posts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```
To accomplish this, you may add the `BiiiiiigMonster\Clears\Concerns\HasClears` trait to the models you would like to auto-clear.
After adding one of the traits to the model, add the attribute name to the `clears` property of your model.
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Clears\Concerns\HasClears;

class User extends Model
{
    use HasClears;
    
    /**
     * The relationships that will be auto-clear when deleted.
     * 
     * @var array 
     */
    protected $clears = ['posts'];
}
```
Once the relationship has been added to the `clears` list, it will be auto-clear when deleted.

## 清除配置
### 自定义清除
有时我们需要自定义清除的逻辑，可以通过定义一个实现`ClearsAttributes`接口的类来实现这一点。

要生成新的清除对象，您可以使用 `make:clear` Artisan 命令。Laravel 会将新的清除对象放在`app/Clears`目录中。 如果此目录不存在，Laravel 将在您执行 Artisan 命令创建规则时创建它：
```bash
php artisan make:clear PostClear
```

实现这个接口的类必须定义一个`confirm`方法，`confirm`方法能决定这个即将被清理的模型是否被保留。作为示例，`User`被删除时，我们将保留他已发布状态的`Post`关联数据。
```injectablephp
<?php

namespace App\Clears;

use BiiiiiigMonster\Clears\Contracts\ClearsAttributes;
use Illuminate\Database\Eloquent\Model;

class PostClear implements ClearsAttributes
{
    /**
     * Decide if the clearable cleared.
     *
     * @param Model $post
     * @param Model $user
     * @return bool
     */
    public function confirm(Model $post, Model $user): bool
    {
        return $post->status === 'published';
    }
}
```

Once you have defined a custom clear type, you may attach it to a model attribute using its class name:
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Clears\Concerns\HasClears;
use App\Clears\PostClear;

class User extends Model
{
    use HasClears;
    
    /**
     * The relationships that will be auto-clear when deleted.
     * 
     * @var array 
     */
    protected $clears = [
        'posts' => PostClear::class
    ];
}
```

### 队列执行
当我们需要清除的关联数据可能非常大时，使用队列去执行它是一个非常好的策略，
让他工作同样非常的简单，只需在关联的清除配置数组中添加第三个值即可。
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Clears\Concerns\HasClears;
use App\Clears\PostClear;

class User extends Model
{
    use HasClears;
    
    /**
     * The relationships that will be auto-clear when deleted.
     * 
     * @var array 
     */
    protected $clears = [
        'posts' => [PostClear::class, 'clearQueue']
    ];
}
```
像这样定义完成后，posts关联的clear操作将放置到自定义的队列中去执行，减少了并行的压力。

Tips：清除配置数组值的顺序不能混乱，配置时需遵守默认的顺序。

### Clearing At Runtime
At runtime, you may instruct a model instance to using the `clear` or `setClears` method just like 
[`append`](https://laravel.com/docs/9.x/eloquent-serialization#appending-at-run-time):
```injectablephp
$user->clear(['posts'=>[PostClear::class, 'clearQueue']])->delete();

$user->setClears(['posts'=>[PostClear::class, 'clearQueue']])->delete();
```

## PHP8 Attribute
在php8中为我们引入了Attribute的特性，它提供了另外一种形式的配置，clear也已经为他做好了准备。

使用Attribute非常的简单，我们定义了一个`#[Clear]`的Attribute，你只需要在对应的关联方法中引入即可。
```injectablephp
namespace App\Models;

use BiiiiiigMonster\Clears\Attributes\Clear;
use BiiiiiigMonster\Clears\Concerns\HasClears;
use Illuminate\Database\Eloquent\Model;
use App\Clears\PostClear;

class User extends Model
{
    use HasClears;
        
    /**
     * Get the posts for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    #[Clear(PostClear::class, 'clearQueue')] 
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```
Tips：`#[Clear]` Attribute 的配置优先级最高，会覆盖`protected $clears`中对应关联的配置

## 可清除关联类型
数据的"删除"一般都是较为敏感的操作，我们不希望重要的数据被其他关联定义上clear，因此我们只支持在父子关联的子关联中实现"删除"。

支持列表：
Illuminate\Database\Eloquent\Relations\HasOne;
Illuminate\Database\Eloquent\Relations\HasOneThrough;
Illuminate\Database\Eloquent\Relations\HasMany;
Illuminate\Database\Eloquent\Relations\HasManyThrough;
Illuminate\Database\Eloquent\Relations\MorphMany;
Illuminate\Database\Eloquent\Relations\MorphOne;
Illuminate\Database\Eloquent\Relations\BelongsToMany;
Illuminate\Database\Eloquent\Relations\MorphToMany;
Tips：`BelongsToMany`与`MorphToMany`关联定义clear时，删除的为中间表数据
不支持列表：
Illuminate\Database\Eloquent\Relations\BelongsTo;
Illuminate\Database\Eloquent\Relations\MorphTo;

## Test
```bash
composer test
```

# 协议
[MIT](./LICENSE)
