<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Member.
 */
class Member extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inviter_id',
        'phone',
        'password',
        'credit1',
        'credit2',
        'level1',
        'level2',
        'status',
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $hidden = [
        'user_id',
        'inviter_id',
        'level1',
        'level2',
        'password',
    ];

    /**
     * The "booting" method of the model.
     */
    public static function boot()
    {
        parent::boot();
        //创建之前 加密密码
        self::creating(function ($model) {
            $model->password = bcrypt(request('password'));
        });

        //编辑用户时 如果设置了密码  则更新密码
        if (request('password') != '') {
            self::updating(function ($model) {
                $model->password = bcrypt(request('password'));
            });
        }
    }

    /**
     * 当前用户邀请人.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|string
     */
    public function inviter()
    {
        return $this->belongsTo(self::class, 'inviter_id')->withDefault(null);
    }

    /**
     * 当前用户会员等级.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|string
     */
    public function level()
    {
        return $this->belongsTo('App\Models\Member\Level', 'level1')->withDefault(null);
    }


    /**
     * 组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id')->withDefault(null);
    }

    /**
     * 当前用户会员分销等级.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|string
     */
    public function commissionLevel()
    {
        return $this->belongsTo('App\Models\Member\CommissionLevel', 'level2')->withDefault(null);

    }

    /**
     * 用户收货地址
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * 用户订单.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

    /**
     * 用户评论.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    /**
     * 增加字段数量
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    protected function increment($column, $amount = 1, array $extra = [])
    {
        //TODO 调用事件  $extra['remark'] 事件备注  $extra['type'] 字段类型 $extra['operater_id'] 后端操作人ID user_id
        return parent::increment ($column, $amount, $extra); // TODO: Change the autogenerated stub
    }

    /**
     * 减少字段数值
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    protected function decrement($column, $amount = 1, array $extra = [])
    {
        //TODO 调用事件  $extra['remark'] 事件备注  $extra['type'] 字段类型 $extra['operater_id'] 后端操作人ID user_id
        return parent::decrement ($column, $amount, $extra); // TODO: Change the autogenerated stub
    }
}
