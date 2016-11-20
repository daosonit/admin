<?php namespace App\Models\Components;

use App\Models\Model;

class RoomServicesStatus extends Model {

	protected $primaryKey = 'romst_id';

    protected $table;

    public $timestamps = false;

    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    public function __construct()
    {
        $current_date = date('Ym', time());
        $tableName = 'room_services_status_' . $current_date;
        $this->setTable($tableName);
    }

    /**
     * Lấy thông tin chi tiết phòng theo ngày
     */
    public function getInfoRoomServices(array $params = [])
    {
        $table            = array_get($params, 'table');
        $field_select     = array_get($params, 'field_select', '*');
        $list_hotels      = array_get($params, 'list_hotel');
        $list_rooms       = array_get($params, 'list_room');
        $romst_time_start = array_get($params, 'romst_time_start');
        $romst_time_end   = array_get($params, 'romst_time_end');
        $get_service_room = array_get($params, 'get_service_room', 0);
        $order_by_asc     = array_get($params, 'order_by_asc');
        $order_by_desc    = array_get($params, 'order_by_desc');
        $join             = array_get($params, 'join', "");

        if (!empty($table)) {
            $this->setTable($table);
        }

        $data = $this->select($field_select);

        if (!empty($join)) {
            $data->join('rooms', 'romst_room_id', 'rom_id');
        }

        if (!empty($list_hotels)) {
            $data->whereIn('romst_hotel_id', $list_hotels);
        }

        if (!empty($list_rooms)) {
            $data->whereIn('romst_room_id', $list_rooms);
        }

        if (!empty($romst_time_start)) {
            $data->where('romst_time', '>=', $romst_time_start);
        }

        if (!empty($romst_time_end)) {
            $data->where('romst_time', '<', $romst_time_end);
        }

        if (!empty($order_by_desc)) {
            $data->orderBy($order_by_desc, 'DESC');
        }

        if (!empty($order_by_asc)) {
            $data->orderBy($order_by_asc);
        }

        return $data->get();
    }

}
