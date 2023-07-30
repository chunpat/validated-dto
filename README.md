<h1 align="center"> validated-dto </h1>

<p align="center"> data transfer object.</p>


## Installing

源项目是这个
[laravel-validated-dto](https://github.com/WendellAdriel/laravel-validated-dto)
但是PHP和Laravel都是最新版本，没兼容低版本，所以我需要修修改改。


```shell
$ composer require chunpat/validated-dto -vvv
```

## TODO

- [x] 兼容php7.2 语法
- [x] 兼容Laravel Components ^7.0
- [x] 兼容ThinkPHP5.1

## Usage

### Laravel Components ^7.0 

继承了既可以使用，如下案例
```php
<?php

namespace App\Repositories\DTOs;

use Chunpat\ValidatedDTO\ValidatedDTO;

/**
 * 登录
 */
class AuthDTO extends ValidatedDTO
{
    protected function rules(): array
    {
        return [
            'store_id' => ['integer'],
            'store_name' => ['integer'],
            'account_id' => ['required', 'integer'],
            'account_name' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }

    protected function defaults(): array
    {
        return [
            'store_id' => 0,
            'store_name' => '匿名',
            'account_id' => ['required', 'integer'],
            'account_name' => ['required', 'string'],
        ];
    }

    protected function casts(): array
    {
        return [
        ];
    }
}

```


### ThinkPHP5.1

实现一个基类，重写验证方法与失败后的处理。业务DTO继承基类，如下案例:

BaseDTO基类
```php
<?php

namespace app\common\dto;

use Chunpat\ValidatedDTO\ValidatedDTO;
use think\exception\ValidateException;
use think\facade\Validate;

class BaseDTO extends ValidatedDTO
{
    private $errMsg;

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }

    protected function rules(): array
    {
        return [];
    }

    protected function isValidData(): bool
    {
        $validate = Validate::make($this->rules(),$this->messages());
        $validate->check($this->data);
        $this->errMsg = $validate->getError();
        return empty($validate->getError());
    }

    protected function failedValidation(): void
    {
        throw new ValidateException(self::class  . ' '. $this->errMsg);
    }
}

```

业务DTO

```php
<?php

namespace app\common\dto;

class DepotGoodsDto extends BaseDTO
{
    protected function defaults(): array
    {
        return [
            'sex' => 0,
        ];
    }

    protected function casts(): array
    {
        return [
        ];
        // TODO: Implement casts() method.
    }

    protected function rules(): array
    {
        // TODO: Implement rules() method.
        return [
            'name'     => ['require'],
            'email'    => ['require','email'],
        ];
    }
}
```

使用
```php
$dto = new DepotGoodsDto([
    'name' => '11',
    'email' => 'john.doeex@ample.com',
]);
```

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/chunpat/validated-dto/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/chunpat/validated-dto/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT