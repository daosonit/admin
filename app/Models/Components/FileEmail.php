<?php namespace App\Models\Components;

use App\Models\Model;

class FileEmail extends Model
{
    public static $file_path = '/resources/pictures/emai_template/';

    protected $table = 'file_email';
    protected $fillable = ['file_picture', 'file_html', 'time_create'];

    public function url()
    {
        return url_s3() . self::$file_path;
    }

    public function urlImage()
    {
        return $this->url() . $this->file_picture;
    }

    public function urlFile()
    {
        return $this->url() . $this->file_html;
    }
}
