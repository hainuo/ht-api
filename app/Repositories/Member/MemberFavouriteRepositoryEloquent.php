<?php

namespace App\Repositories\Member;

use App\Criteria\RequestCriteria;
use App\Models\Member\MemberFavourite;
use App\Repositories\Interfaces\MemberFavouriteRepository;
use App\Validators\Member\MemberFavouriteValidator;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class MemberFavouriteRepositoryEloquent.
 */
class MemberFavouriteRepositoryEloquent extends BaseRepository implements MemberFavouriteRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'member_id',
        'user_id',
    ];

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return MemberFavourite::class;
    }

    /**
     * @return null|string
     */
    public function validator()
    {
        return MemberFavouriteValidator::class;
    }

    /**
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
