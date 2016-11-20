<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddRateIdTracking extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$year_start  = 2015;
		$year_finish = 2019;

        for ($year = $year_start; $year <= $year_finish; $year++) {
        	$m_last = 12;
        	if ($year == 2019) {
        		$m_last = 2;
        	}
        	for ($m = 1; $m <= $m_last; $m++) {
        		if ($m < 10) $m = '0' . $m;
        		$tableName = 'tracking_' . $year . $m;
        		Schema::connection('mysqllog')->table($tableName, function(Blueprint $table) use ($tableName)
        		{
                    if(!Schema::connection('mysqllog')->hasColumn($tableName, 'tra_rate_plan_id')){
                        $table->integer('tra_rate_plan_id')->unsigned()->default('0')->comment('ID rate plan');
                    }
                    if(!Schema::connection('mysqllog')->hasColumn($tableName, 'tra_rate_plan_id')){
                        $table->tinyInteger('tra_person_type')->comment('kieu gia theo so luong nguoi')->default('0');
                    }
                    if(!Schema::connection('mysqllog')->hasColumn($tableName, 'tra_rate_plan_id')){
                        $table->tinyInteger('tra_action')->comment('kieu insert, update hay delete')->default('0');
                    }
        		});
        	}
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$year_start  = 2015;
		$year_finish = 2019;

        for ($year = $year_start; $year <= $year_finish; $year++) {
        	$m_last = 12;
        	if ($year == 2019) {
        		$m_last = 2;
        	}
        	for ($m = 1; $m <= $m_last; $m++) {
        		if ($m < 10) $m = '0' . $m;
        		$tableName = 'tracking_' . $year . $m;
        		Schema::connection('mysqllog')->table($tableName, function(Blueprint $table) use ($tableName)
        		{
                    if(Schema::connection('mysqllog')->hasColumn($tableName, 'tra_rate_plan_id')){
                        $table->dropColumn('tra_rate_plan_id');
                    }

                    if(Schema::connection('mysqllog')->hasColumn($tableName, 'tra_person_type')){
                        $table->dropColumn('tra_person_type');
                    }

                    if(Schema::connection('mysqllog')->hasColumn($tableName, 'tra_action')){
                        $table->dropColumn('tra_action');
                    }
        		});
        	}
        }
	}

}
