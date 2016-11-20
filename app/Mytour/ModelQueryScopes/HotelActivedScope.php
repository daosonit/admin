<?php 
namespace App\Mytour\ModelQueryScopes;
use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HotelActivedScope implements ScopeInterface
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model){
		$builder->where('hot_active', ACTIVE);
	}




	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 *
	 * @return void
	 */
	public function remove(Builder $builder, Model $model){
		
		$query = $builder->getQuery();
		$column = 'hot_active';
		$query->wheres = collect($query->wheres)->reject(function($where) use ($column)
		{
			return ($where == $column);
		})->values()->all();
	}
}