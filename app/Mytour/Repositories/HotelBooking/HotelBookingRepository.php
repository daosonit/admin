<?php namespace App\Mytour\Repositories\HotelBooking;
 
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
 
class HotelBookingRepository extends Repository {
 
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'App\Models\Components\HotelBooking';
    }
}