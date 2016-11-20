<?php 

namespace App\Mytour\Contracts;


interface MytourBooking {
	
	/**
	 * Trả về tổng số tiền của Booking
	 */
	public function getBookingMoney();

	/**
	 * Trả về tổng số tiền phải thanh toán của Booking
	 */
	public function getTotalMoney();

	/**
	 * Trả về tổng số tiền phụ thu của Booking
	 */
	public function getDateTimeBook();

	/**
	 * Trả về tổng số tiền của booking phải thanh toán cho ks
	 */
	public function getSupplierMoney();

	/**
	 * Trả về trạng thái hiện tại của booking
	 */
	public function getBookingStatus();

	/**
	 * Trả về thông tin khách hàng của Booking
	 */
	public function getCustomerInfo();

	/**
	 * Trả về tổng số tiền khuyến mãi (tiền giảm trừ) của Booking
	 */
	public function getDiscountMoney();

	/**
	 * Đặt - thai đổi trạng thái của booking
	 */
	public function setStatus($status);

	/**
	 * Trả về tổng số tiền phụ thu của Booking
	 */
	public function getExtraMoney();

}