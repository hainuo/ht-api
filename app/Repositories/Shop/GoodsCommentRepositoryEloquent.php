<?php

namespace App\Repositories\Shop;

use App\Criteria\RequestCriteria;
use App\Models\Shop\GoodsComment;
use App\Validators\Shop\GoodsCommentValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\Interfaces\Shop\GoodsCommentRepository;

/**
 * Class GoodsCommentRepositoryEloquent.
 */
class GoodsCommentRepositoryEloquent extends BaseRepository implements GoodsCommentRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'merch_id',
        'order_id',
        'goods_id',
        'nickname',
    ];

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return GoodsComment::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return GoodsCommentValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return 'Prettus\\Repository\\Presenter\\ModelFractalPresenter';
    }
}
