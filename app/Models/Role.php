<?php namespace App\Models;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	public function scopeIdInList($query, array $ids)
	{
		$query->whereIn('id', $ids);
	}
}