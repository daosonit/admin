<?php 
namespace App\Mytour\Excels\Imports;

use Input, Config;

use Maatwebsite\Excel\Files\ExcelFile;

class VoucherCodeImport extends ExcelFile {

	private $inputName = 'file';

	private $storagePath;

	public function __construct()
	{
		$this->storagePath = Config::get('mytour.tempfiles_path');
	}

    public function getFile()
    {
        return storage_path('exports') . '/file.csv';
    }

    public function getFilters()
    {
        return [
            'chunk'
        ];
    }



    public function setFileInput($inputName)
    {
    	$this->inputName = $inputName;
    	return $this;
    }

}