<?php namespace App\Models\Components;

use App\Models\Model;
use Config;

class Partner extends Model
{
    protected $primaryKey = 'pn_id';
    protected $table = 'all_partner';
    protected $fillable = array('pn_type', 'pn_name', 'pn_logo', 'pn_name', 'pn_info', 'pn_active', 'pn_link');
    public $timestamps = false;

    public function scopeFindType($query, $pn_type)
    {
        return $query->where('pn_type', '=', $pn_type);
    }

    public function scopeFindName($query, $pn_name)
    {
        return $query->where('pn_type', '=', $pn_name);
    }

    public function getSmallLogo()
    {
        return url_s3() . Config::get('image_config.pathPartner') . 'small_' . $this->pn_logo;
    }

    public function getMediumLogo()
    {
        return url_s3() . Config::get('image_config.pathPartner') . 'medium_' . $this->pn_logo;
    }

    public function getLargeLogo()
    {
        return url_s3() . Config::get('image_config.pathPartner') . 'large_' . $this->pn_logo;
    }
}
