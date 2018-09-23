<?php

namespace App\Repositories\Member;

use App\Criteria\RequestCriteria;
use App\Models\Member\MemberHistory;
use App\Repositories\Interfaces\MemberHistoryRepository;
use App\Validators\Member\MemberHistoryValidator;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class MemberHistoryRepositoryEloquent.
 */
class MemberHistoryRepositoryEloquent extends BaseRepository implements MemberHistoryRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'member_id',
        'user_id',
        'created_at',
    ];

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return MemberHistory::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return MemberHistoryValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
