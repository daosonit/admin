<?php
namespace App\Mytour\Classes\Images;
interface UploadImageInterface
{
    public function make($option = array());

    public function save($path);

    public function fileName();

    public function error();

    public function delete($image);
}