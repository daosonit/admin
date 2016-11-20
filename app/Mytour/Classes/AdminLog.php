<?php
namespace App\Mytour\Classes;

use App\Models\Components\AdminLog AS AdminLogModel;

class AdminLog
{
    public function __construct(
        AdminLogModel $adminLog
    )
    {
        $this->adminLog = $adminLog;
    }

    /**
     * save log
     * @param int $module : id cua module
     * @param string $table : tên bảng đang thay đổi
     * @param int $action : 1-INSERT, 2-UPDATE, 3-UPDATE
     * @param string $query : Query thực hiện
     * @param string $url : url hiện tại
     * @param string $id_field : primary key or foreign key
     * @param string $record_id : id record (primary key)
     */
    public function saveLog($data, $ip, $adm_id, $module, $table, $action, $query, $url, $id_field = '', $record_id = 0)
    {
        if($adm_id <= 0) return;

        $insert = array('adl_module_id' => $module,
                        'adl_table'     => $table,
                        'adl_action'    => $action,
                        'adl_admin_id'  => $adm_id,
                        'adl_record_id' => $record_id,
                        'adl_data'      => json_encode($data),
                        'adl_query'     => base64_encode($query),
                        'adl_url'       => base64_encode($url),
                        'adl_ip'        => $ip,
                        'adl_date'      => time());
        
        $result = $this->adminLog->insertSaveLog($insert);
    }

}