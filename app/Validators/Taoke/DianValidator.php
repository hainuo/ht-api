<?php

namespace App\Validators\Taoke;

use Prettus\Validator\LaravelValidator;
use Prettus\Validator\Contracts\ValidatorInterface;

/**
 * Class DianValidator.
 */
class DianValidator extends LaravelValidator
{
    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = [

        ValidatorInterface::RULE_CREATE => [
            'thumb' => 'required',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'thumb' => 'required',
        ],
    ];
    protected $messages = [
        'thumb.required' => '主图不能为空',
    ];
}
