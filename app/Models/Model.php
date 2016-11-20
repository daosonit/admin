<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use App\Mytour\MytourExtends\MytourModelCollection;

abstract class Model extends EloquentModel
{
	
	protected $fieldPrefix;


	 /**
     * Create a new MytourModelCollection instance.
     *
     * @param  array  $models
     * @return App\Mytour\MytourExtends\MytourModelCollection
     */
    public function newCollection(array $models = [])
    {
        return new MytourModelCollection($models);
    }



    /**
     * giới hạn kết quả trả về bởi từ khóa theo field
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  string  $keyword
     * @param  string  $field
     * @return void
     */
    public function scopeSearchBy($query, $keyword, $field)
    {
    	$query->where($field, 'LIKE', '%'. $keyword .'%' );
    }



    /**
     * 
     */


















    
	
}