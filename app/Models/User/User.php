<?php

namespace App\Models\User;

use Hashids\Hashids;
use App\Models\Taoke\Setting;
use App\Events\CreditDecrement;
use App\Events\CreditIncrement;
use App\Events\CreditOrderFriend;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetUserPassword;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, Transformable
{
    use Notifiable,
        SoftDeletes,
        TransformableTrait,
        LaratrustUserTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'inviter_id',
        'group_id',
        'level_id',
        'name',
        'email',
        'password',
        'nickname',
        'phone',
        'credit1',
        'credit2',
        'headimgurl',
        'status',
        'wx_unionid',
        'wx_openid1',
        'wx_openid2',
        'realname',
        'alipay',
        'expired_time',
    ];

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string
     */
    protected $guarded = 'user';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'wx_unionid',
        'wx_openid1',
        'wx_openid2',
        'ali_unionid',
        'ali_openid1',
        'ali_openid2',
        'password',
    ];

    public function transform()
    {
        $data = $this->toArray();
        $hashids = new Hashids(config('hashids.SALT'), config('hashids.LENGTH'), config('hashids.ALPHABET'));
        //邀请码
        $hashids = $hashids->encode($data['id']);
        $data['hashid'] = $hashids;

        return $data;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The "booting" method of the model.
     */
    public static function boot()
    {
        parent::boot();
        //创建之前 加密密码
        self::creating(function ($model) {
            if ($model->inviter_id != null) {
                event(new CreditOrderFriend([
                    'user_id' => $model->user_id,
                ], 2));
            }
            $model->password = bcrypt(request('password'));
        });
        //编辑是如果设置了密码 则更新密码
        if (request('password') != '') {
            self::updating(function ($model) {
                if ($model->inviter_id != null) {
                    event(new CreditOrderFriend([
                        'user_id' => $model->inviter_id,
                    ], 2));
                }
                $model->password = bcrypt(request('password'));
            });
        } else {
            self::updating(function ($model) {
                if ($model->inviter_id != null) {
                    event(new CreditOrderFriend([
                        'user_id' => $model->inviter_id,
                    ], 2));
                }
            });
        }
    }

    /**
     * 使用验证码找回密码
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetUserPassword($token));
    }

    /**
     * 增加字段数量.
     * @param string $column
     * @param int $amount
     * @param array $extra
     * @return int
     */
    protected function increment($column, $amount = 1, array $extra = [])
    {
        $extra['type'] = 2;
        if (in_array($column, ['credit1', 'credit2', 'credit3'])) {
            event(new CreditIncrement($this, $column, $amount, $extra));
        }

        return $this->incrementOrDecrement($column, $amount, $extra = [], 'increment');
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
        $extra['type'] = 1;

        if (in_array($column, ['credit1', 'credit2', 'credit3'])) {
            event(new CreditDecrement($this, $column, -$amount, $extra));
        }

        return $this->incrementOrDecrement($column, $amount, $extra = [], 'decrement');
    }

    /**
     * 等级.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo('App\Models\User\Level', 'level_id')->withDefault(null);
    }

    /**
     * 邀请人.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inviter()
    {
        return $this->belongsTo('App\Models\User\User', 'inviter_id')->withDefault(null);
    }

    /**
     * 粉丝.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friends()
    {
        return $this->hasMany('App\Models\User\User', 'inviter_id');
    }

    /**
     * 组.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('App\Models\User\Group', 'group_id')->withDefault(null);
    }

    /**
     * 组.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldGroup()
    {
        return $this->belongsTo('App\Models\User\Group', 'oldgroup_id')->withDefault(null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tbkSetting()
    {
        return $this->hasOne(Setting::class, 'user_id', 'id');
    }
}
