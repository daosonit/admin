<?php
namespace App\Mytour\Classes\Images;

use Image, Storage, File, Config;

class UploadImage implements UploadImageInterface
{
    protected $request = null; //Request file
    protected $prefix_size = array();
    protected $first_name = ''; //first name file
    protected $full_name = '';
    protected $message_error = array();
    protected $error = false;
    protected $path = '';

    /**
     * make
    */
    public function make($option = array())
    {
        $this->path = array_get($option, 'path', '');
        $this->prefix_size = array_get($option, 'prefix_size', []);
        $this->first_name = array_get($option, 'first_name', '');

        return $this;
    }

    /**
     * save image
    */
    public function save($request)
    {
        $this->request = $request;

        $image_origin = Image::make($this->request);

        $path = $this->path($this->path);
        $this->checkFileSize($image_origin);
        $this->full_name = $this->fileName();

        if (!$this->error) {
            foreach ($this->prefix_size as $prefix => $size) {
                $full_path = $path . $prefix . '_' . $this->full_name;

                $width_crop = (isset($size['w'])) ? $size['w'] : 0;
                $height_crop = (isset($size['h'])) ? $size['h'] : 0;

                $crop_size = $this->getSizeCrop($image_origin, $width_crop, $height_crop);

                $image_new = $image_origin;

                $image_new->fit($crop_size['width'], $crop_size['height'])->resize($width_crop, $height_crop);

                Storage::put($full_path, $image_new->stream()->__toString(), 'public');
            }
        }
    }

    /**
     * get file name
    */
    public function fileName()
    {
        if (empty($this->full_name)) {
            $this->full_name = $this->createName();
        }
        return $this->full_name;
    }

    /**
     * get error
    */
    public function error()
    {
        return $this->message_error;
    }

    /**
     * delete image
    */
    public function delete($image)
    {
        $path = $this->path($this->path);

        foreach ($this->prefix_size as $prefix => $size) {
            $full_path = $path . $prefix . '_' . $image;

            if (Storage::exists($full_path)) {
                Storage::delete($full_path);
            }
        }

    }

    private function getSizeCrop($image_origin, $width_crop, $height_crop)
    {
        $width_img = $image_origin->width();
        $height_img = $image_origin->height();

        $set_size = function ($ratio) use ($width_crop, $height_crop) {
            return array('width'  => $width_crop * $ratio,
                         'height' => $height_crop * $ratio);
        };

        if (($width_img / $width_crop) > ($height_img / $height_crop)) {
            $ratio = floor($height_img / $height_crop);
            $data_return = $set_size($ratio);
        } elseif (($width_img / $width_crop) < ($height_img / $height_crop)) {
            $ratio = floor($width_img / $width_crop);
            $data_return = $set_size($ratio);
        } else {
            $data_return = array('width'  => $width_img,
                                 'height' => $height_img);
        }

        return $data_return;
    }

    private function path($path)
    {
        return str_finish($path, '/');
    }

    private function createName()
    {
        if (empty($this->first_name)) {
            return strtolower(str_random(4)). time() . '.' . $this->request->getClientOriginalExtension();
        } else {
            return $this->first_name . time() . '.' . $this->request->getClientOriginalExtension();
        }
    }

    private function checkFileSize($image_origin)
    {
        $max_size = Config::get('image_config.maxSize');

        if ($this->request->getClientSize() > $max_size) {
            $this->message_error[] = 'Image size too large.';
            $this->error = true;
        }

        if (!$this->error) {
            $width_img = $image_origin->width();
            $height_img = $image_origin->height();

            foreach ($this->prefix_size as $prefix => $size) {
                if (isset($size['w']) && isset($size['h'])) {
                    if ($width_img < $size['w'] && $height_img < $size['h']) {
                        $this->message_error[] = 'Origin image size is smaller than the crop image.';
                        $this->error = true;
                        break;
                    }
                } else {
                    $this->message_error[] = 'Initialize parameter fail.';
                    $this->error = true;
                    break;
                }
            }
        }
    }

}