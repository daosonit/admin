<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session, View;

use Illuminate\Http\Request;

class ErrorsController extends Controller {

	//

	public function getAccessForbidden()
	{
		return View::make('error_access');
	}

}
