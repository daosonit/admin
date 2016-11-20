<?php namespace App\Mytour\Classes;



class MytourHelper {

	
	

	public function getConfig()
	{
		return 'mytour';
	}


	/**
	 * Định dạng tiền tệ
	 * @param float $number : giá truyền vào
	 * @param int $decimals : phần thập phân
	 * @param string $dec_point : Ký tự ngăn cách giữa phần nguyên và phần thập phân
	 * @param string $thousands_sep : Ký tự ngăn cách mỗi nhóm số đơn vị hàng ngìn 
	 * @return string
	 */
	public function CurrencyFormat($number , $decimals = 0 , $dec_point = "." , $thousands_sep = ",")
	{
		return number_format($number, $decimals, $dec_point, $thousands_sep);
	}



	/**
	 * Định dạng và làm tròn giá
	 * @param int $price : giá truyền vào
	 * @param string $round_type : kiểu làm tròn
	 * @param string $is_format_number : định dạng tiền tệ (1: có | 0: không)
	 * @return mixed
	 */
	public function generatePrice($price, $round_type = '', $is_format_number = 1){

	    switch ($round_type) {
	        case 'down':
	            $price = floor($price / 1000) * 1000;
	            break;
	        
	        case 'up':
	            $price = ceil($price / 1000) * 1000;
	            break;

	        default:            
	            break;
	    }
	    if($is_format_number == 1){
	        $price = number_format($price, 0, '.', ',');
	    }
	    return $price;
	}



	public function resolveBitMaskSum($input){
	    $i = 1;
	    $arr_result = [];
	    while($i <= $input)
	    {
	        if($i&$input)
	        {
	            $arr_result[] = $i;
	        }
	        $i*=2;
	    }
	    return $arr_result;
	}


	/**
	 * Check một chuỗi có phải là địa chỉ email hay ko 
	 * @param string $email : chuỗi truyền vào
	 * @param string $round_type : kiểu làm tròn
	 * @param string $is_format_number : định dạng tiền tệ (1: có | 0: không)
	 * @return mixed
	 */
	public function isEmailAddress($email)
	{
		return (!filter_var($email, FILTER_VALIDATE_EMAIL) === false);
	}




	public function getHotelStarCollection()
	{
		return collect([
			HOTEL_ONE_STAR => 'Khách sạn 1 sao',
			HOTEL_TWO_STARS => 'Khách sạn 2 sao',
			HOTEL_THREE_STARS => 'Khách sạn 3 sao',
			HOTEL_FOUR_STARS => 'Khách sạn 4 sao',
			HOTEL_FIVE_STARS => 'Khách sạn 5 sao',
		]);
	}


}
