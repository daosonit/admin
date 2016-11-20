<?php namespace App\Mytour\Repositories\Voucher;
 
use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
 
class FindVoucherByName extends Criteria {





    public function __construct($strCode)
    {
        $this->code = $strCode;
    }
 
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $query = $model->where('name', 'LIKE', '%'.$this->code.'%');
        return $query;
    }
}