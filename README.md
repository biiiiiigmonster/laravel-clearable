English | [中文](./README-CN.md)

<div align="center">

# LARAVEL ELOQUENT TRAIT

<p>
    <a href="https://github.com/biiiiiigmonster/laravel-clearable/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-7389D8.svg?style=flat" ></a>
    <a href="https://github.com/biiiiiigmonster/laravel-clearable/releases" ><img src="https://img.shields.io/github/release/biiiiiigmonster/laravel-clearable.svg?color=4099DE" /></a> 
    <a href="https://packagist.org/packages/biiiiiigmonster/laravel-clearable"><img src="https://img.shields.io/packagist/dt/biiiiiigmonster/laravel-clearable.svg?color=" /></a> 
</p>

</div>



# Environment

- laravel >= 9


# Installation

```bash
composer require biiiiiigmonster/laravel-clearable
```

# Introductions
`relationship` is powerful, it can help us maintain complex data relation.

一般来说，“删除”操作作为数据生命周期的最后一节，受到的关注度较小，我们往往在删除数据本身的同时可能会疏忽掉与之关联的模型数据的处理，
业务中数据完整的关联性也会因为这些残留数据而遭到破坏。

这个包可以很方便的帮您管理这些关联数据删除关系，仅仅只需要简单的定义。让我们来尝试一下吧！

## Usage
For example, `User` model related `Post` model, it's also hoped that the associated `Post` model can be deleted after the `User` model deleted:

```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{    
    /**
     * Get the posts for the user.
     *
     * @return \HasMany
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

## Clear Configuration
### Custom Clear
Sometimes you may occasionally need to define your own clear's logic, You may accomplish this by defining a class that implements the `InvokableClear` interface.

To generate a new clear object, you may use the `make:clear` Artisan command. we will place the new rule in the `app/Clears` directory. If this directory does not exist, We will create it when you execute the Artisan command to create your clear:
```bash
php artisan make:clear PostWithoutReleasedClear
```

Once the clear has been created, we are ready to define its behavior. A clear object contains a single method: `__invoke`.
This method will determine whether the relation data is cleared.

```injectablephp
<?php

namespace App\Clears;

use BiiiiiigMonster\Clears\Contracts\InvokableClear;
use Illuminate\Database\Eloquent\Model;

class PostWithoutReleasedClear implements InvokableClear
{
    /**
     * Decide if the clearable cleared.
     *
     * @param Model $post
     * @return bool
     */
    public function __invoke($post)
    {
        return $post->status != 'published';
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

### Use Queue
When the relation data that we need to clear may be very large, it is a very good strategy to use `queue` to execute it.

Making it work is also simple, add the attribute name to the `clearQueue` property of your model.
```injectablephp
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use BiiiiiigMonster\Clears\Concerns\HasClears;
use App\Clears\PostClear;

class User extends Model
{
    use HasClears;
    
    /**
     * The clearable that will be dispatch on this name queue.
     * 
     * @var bool|string 
     */
    protected $clearQueue = true;
}
```
像这样定义完成后，posts关联的clear操作将放置到自定义的队列中去执行，减少了并行的压力。

### Clearing At Runtime
At runtime, you may instruct a model instance to using the `clear` or `setClears` method just like
[`append`](https://laravel.com/docs/9.x/eloquent-serialization#appending-at-run-time):
```injectablephp
$user->clear(['posts' => PostClear::class])->delete();

$user->setClears(['posts' => PostClear::class])->delete();
```

## PHP8 Attribute
在php8中为我们引入了Attribute的特性，它提供了另外一种形式的配置，clear也已经为他做好了准备。

使用Attribute非常的简单，我们定义了一个`#[Clear]`的Attribute，你只需要在对应的关联方法中引入即可。
```injectablephp
namespace App\Models;

use BiiiiiigMonster\Clears\Attributes\Clear;
use BiiiiiigMonster\Clears\Concerns\HasClears;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasClears;
        
    /**
     * Get the posts for the user.
     *
     * @return HasMany
     */
    #[Clear] 
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```
Similarly, you can set `Custom Clear` in `#[Clear]`, or even configure `clearQueue` separately:
```injectablephp
#[Clear(PostClear::class, 'queue-name')]
public function posts()
{
    return $this->hasMany(Post::class);
}
```
> Tips：`#[Clear]` will overwrite the corresponding configuration in `protected $clears`

## Support Relationship
Data's "deletion" is generally a sensitive operation, we do not want important data to declare `clear` by any relationships. Therefore, we don't support `clear` in the `BelongsTo` relationships.

Support-List:
- HasOne
- HasOneThrough
- HasMany
- HasManyThrough
- MorphMany
- MorphOne
- BelongsToMany
- MorphToMany
> Tips：When the `BelongsToMany` and `MorphToMany` relationship declare is `clear`, deleted is the pivot model data

Not-Support-List:
- BelongsTo
- MorphTo

# Test
```bash
composer test
```

# License
[MIT](./LICENSE)
