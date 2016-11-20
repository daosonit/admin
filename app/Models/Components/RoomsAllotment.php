<?php namespace App\Models\Components;

use App\Models\Model;
use DB;

class RoomsAllotment extends Model {

	protected $primaryKey = 'roa_id';

	protected $table;

	public $timestamps = false;

	public function __construct()
	{
        parent::__construct();
		$current_date = date('Ym', time());
		$tableName    = 'rooms_allotment_' . $current_date;
		$this->setTable($tableName);
	}

	/**
     * Dynamically set a model's table.
     *
     * @param  $table
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

	/**
	 * [getDataByHotelId description]
	 * get thong tin allotment theo id KS
	 * @param  [type] $hotelID [description]
	 * @param  array  $params  [description]
	 * @return [type]          [description]
	 */
	public function getDataByHotelId($hotelID, array $params)
    {
        $data_return   = [];
        $table         = array_get($params, 'table', "");
        $field_select  = array_get($params, 'field_select', "*");
        $time_checkin  = (int) array_get($params, 'time_checkin', 0);
        $time_checkout = (int) array_get($params, 'time_checkout', 0);

        if ($table != "") {
            $this->setTable($table);
            $data_return = $this->select($field_select)
                                ->where('roa_hotel_id', '=', $hotelID);
            if ($time_checkin > 0) {
                $data_return->where('roa_time', '>=', $time_checkin);
            }

            if ($time_checkout > 0) {
                $data_return->where('roa_time', '<=', $time_checkout);
            }
            $data_return = $data_return->get();
        }

        return $data_return;
    }

    /**
     * [getDataByListRoom description]
     * get thong tin allotment theo list ID Room
     * @param  [type] $hotelID [description]
     * @param  array  $params  [description]
     * @return [type]          [description]
     */
    public function getDataByListRoom(array $listRoom, array $params)
    {
        $data_return   = [];
        $table         = array_get($params, 'table', "");
        $field_select  = array_get($params, 'field_select', "*");
        $time_checkin  = array_get($params, 'time_checkin', 0);
        $time_checkout = array_get($params, 'time_checkout', 0);
        $hotel_pms     = array_get($params, 'hotel_pms', 0);

        if ($table != "") {
            $this->setTable($table);
            $data_return = $this->select($field_select)
                                ->whereIn('roa_room_id', $listRoom);

            if ($time_checkin > 0) {
                $data_return->where('roa_time', '>=', $time_checkin);
            }

            if ($time_checkout > 0) {
                $data_return->where('roa_time', '<=', $time_checkout);
            }

            if ($hotel_pms > 0) {
                $data_return->where('roa_check_allotment_pms', '=', ACTIVE);
            }

            $data_return = $data_return->get();
        }

        return $data_return;
    }

    /**
     * [getAllotmentByRoomId description]
     * get Allotment by Room ID
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getAllotmentByRoomId ($params)
    {
        $data_return   = [];
        $table         = array_get($params, 'table', "");
        $room_id       = array_get($params, 'room_id', 0);
        $time_checkin  = array_get($params, 'time_checkin', 0);
        $time_checkout = array_get($params, 'time_checkout', 0);

        if ($table != "") {
            $this->setTable($table);
            $data_return = $this->where('roa_room_id', '=', $room_id);

            if ($time_checkin > 0) {
                $data_return->where('roa_time', '>=', $time_checkin);
            }

            if ($time_checkout > 0) {
                $data_return->where('roa_time', '<=', $time_checkout);
            }

            $data_return = $data_return->get();
        }

        return $data_return;
    }

    /**
     * [insertAllotmentByData description]
     * insert status allotment by id room
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function insertAllotmentByData ($params)
    {
		$table       = array_get($params, 'table', "");
		$data_insert = array_get($params, 'data_insert', 0);

        if ($table != "" && !empty($data_insert)) {
            $this->setTable($table);

            DB::enableQueryLog();

            return $this->insert($data_insert);
        }

        return 0;
    }

    /**
     * [updateAllotmentByRoomId description]
     * update status allotment by id room
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function updateAllotmentByRoomId ($params)
    {
		$table      = array_get($params, 'table', "");
		$room_id    = array_get($params, 'room_id', 0);
		$dataUpdate = array_get($params, 'data_update', 0);

        if ($table != "" && !empty($dataUpdate)) {
            $this->setTable($table);

            DB::enableQueryLog();

            return $this->where('roa_room_id', '=', $room_id)
             			->update($dataUpdate);
        }

        return 0;
    }

    public function updateAllotmentRawQuery ($params)
    {
        $table        = array_get($params, 'table', "");
        $query_update = array_get($params, 'query_update', "");
        if ($table != "" && $query_update != "") {
            $this->setTable($table);

            DB::enableQueryLog();

            return DB::update($query_update);
        }

        return false;
    }
}
