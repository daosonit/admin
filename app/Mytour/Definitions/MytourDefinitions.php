<?php

// định nghĩa giá trị APP_ENV của các môi trường server 
define('ENV_DEV', 'dev'); // môi trường dev
define('ENV_LOCAL', 'local');// môi trường local
define('ENV_LIVE', 'live');// môi trường thật


define("BOOKING_NEW", 0);//Đặt phòng mới
define("BOOKING_PROCESSING", 1);//Đang xử lý
define("BOOKING_FAIL", 2);//Thất bại
define("BOOKING_SUCCESS", 3);//Thành công -- thay đổi ngày 25/6/2013
define("BOOKING_WAITTING", 4);//Chờ duyệt -- thay đổi ngày 25/6/2013
define("BOOKING_CANCEL", 5);//Khách hủy

define('HOTEL_MODULE', 1);

define('DEAL_MODULE', 2);

define('TOUR_MODULE', 3);

define('TICKET_MODULE', 4);

define('HANOI_BRANCH', 1);

define('HANOI_HCM', 2);

define('ACTIVE', 1);

define('NO_ACTIVE', 0);

define('SUCCESS_ALERT', 'Cập nhật thông tin thành công!');

define('ERROR_ALERT', 'Cập nhật thông tin thất bại, vui lòng thử lại!');

define('DELETE_ALERT', 'Bạn có muốn xóa bản ghi không?');

define('NUM_PER_PAGE', 30);


//hotel star rate
define('HOTEL_ONE_STAR', 1);
define('HOTEL_TWO_STARS', 2);
define('HOTEL_THREE_STARS', 3);
define('HOTEL_FOUR_STARS', 4);
define('HOTEL_FIVE_STARS', 5);

/**
 * Prefix image
 */
define('SIZE_SMALL', 'small');
define('SIZE_MEDIUM', 'medium');
define('SIZE_DEFAULT', 'default');
define('SIZE_IMPROVE', 'improve');
define('SIZE_LARGE', 'large');
define('SIZE_ORIGIN', 'origin');



