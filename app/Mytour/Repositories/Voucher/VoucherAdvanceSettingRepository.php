<?php namespace App\Mytour\Repositories\Voucher;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
 
class VoucherAdvanceSettingRepository extends Repository {
 
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Components\VoucherAdvanceSetting';
    }
}
