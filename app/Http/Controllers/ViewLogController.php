<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ViewLogController extends Controller {

	public function __construct(Request $request)
	{
		$this->middleware('ip_filter');

		$this->logDir = storage_path('logs/');

		$this->sqlLogDir = config('sql_logger.directory').'/';
	}

	public function getLogList()
	{
		$data = [];
		$logDir = $this->logDir;
		$files = scandir($logDir);
		foreach($files as $file){
			if(is_file($logDir.$file) && substr($file, -3) == 'log'){
				$data[$file] = route('view-log-detail', ['filename' => $file]);
			}
		}
		return view('components.system.view-log.log-list')->with(['files' => $data]);
	}

	public function getLog($filename)
	{
		$filePath = $this->logDir . $filename;
		if(file_exists($filePath) && $fileContent = file_get_contents($filePath))
			return view('components.system.view-log.log-detail')->with(['content' => $fileContent, 'fileName' => $filename]);
		else 
			return 'logs content not found!';
	}


	public function clearLog($filename)
	{
		$filePath = $this->logDir . $filename;

		if(file_exists($filePath)){
			file_put_contents($filePath, '');
		}
		return redirect()->back();
	}


	public function getSqlLog($filename)
	{
		$filePath = $this->sqlLogDir . $filename;
		if(file_exists($filePath) && $fileContent = file_get_contents($filePath))
			return view('components.system.view-log.sql-log-detail')->with(['content' => $fileContent, 'fileName' => $filename]);
		else 
			return 'logs content not found!';
	}


	public function getSqlLogList()
	{
		$data = [];
		$logDir = $this->sqlLogDir;
		$files = scandir($logDir);
		foreach($files as $file){
			if(is_file($logDir.$file) && substr($file, -3) == 'sql'){
				$data[$file] = route('view-sql-log-detail', ['filename' => $file]);
			}
		}
		return view('components.system.view-log.sql-log-list')->with(['files' => $data]);
	}


	public function clearSqlLog($filename)
	{
		$filePath = $this->sqlLogDir . $filename;

		if(file_exists($filePath)){
			file_put_contents($filePath, '');
		}
		return redirect()->back();
	}


}





// Route::get('view-logs', function(){
	
// });


// Route::get('view-logs/{filename}', function($filename){
// 	
// });