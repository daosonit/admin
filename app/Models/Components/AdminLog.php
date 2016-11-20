<?php namespace App\Models\Components;

use App\Models\Model;

class AdminLog extends Model {

	protected $primaryKey = 'adl_id';

    protected $table;

    protected $connection = 'mysqllog';

    public $timestamps = false;

	public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    public function __construct()
    {
        $current_date = date('Ym', time());
        $tableName = 'admin_logs_' . $current_date;
        $this->setTable($tableName);
    }

    /**
     * insert save log
     */
    public function insertSaveLog(array $params = array())
    {

        $this->insert($params);

    }
}
