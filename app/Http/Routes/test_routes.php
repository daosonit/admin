<?php 


Route::get('test-env', function (){
	dd(is_live_env());
});






Route::get('get-booking',  function(){
	$bookings = \App\Models\Components\HotelBooking::with('city')->get()->first();
	dnd($bookings->getDateTimeCheckin()->timestamp);
// return 'sdfsd';

});

// Route::get('model', function (){
// 	$users = App\Models\Member::get()->keyBy(function($item){
// 		return $item->use_id;
// 	})->valueBy(function($item){
// 		return $item->use_name;
// 	});
// 	$bookings = App\Models\Components\HotelBooking::get();
// 	// $user = new App\Models\Member;

// 	dd($bookings->keyBy(function($item){
// 		return $item->boo_id;
// 	})->valueBy(function($item){
// 		return $item->boo_code;
// 	})->toArray());
// });



Route::get('storage', function(){

	// $files = Storage::files('/resources/plugins/ckeditor/');

	// $files = $disk->files('/');
	// $content = Storage::get('/');

	// return response()->download('http://static.mytour.vn/resources/plugins/ckeditor/build-config.js');
	$files = Storage::disk('s3')->allFiles('/resources/upload/temp_files');
	dd($files);
});


// Route::get('download-excel', function(){

// 	$users = App\Models\AdminUser::get();
// 	// dd($users);
// 	dd($users->keyBy(function($item){
// 		return strtolower($item->id);
// 	})->map(function($item){
// 		return $item = $item->name;
// 	})->toArray());

// 	// dd($data1->toArray());

// 	Excel::create('new_file_excel_'.Carbon\Carbon::now()->toDateString('mmddY'), function($excel) {
// 		// Set the title
// 	    $excel->setTitle('Our new awesome title');

// 	    // Chain the setters
// 	    $excel->setCreator('Maatwebsite')
// 	          ->setCompany('Maatwebsite');

// 	    // Call them separately
// 	    $excel->setDescription('A demonstration to change the file properties');

// 	     // Our first sheet
// 	    $excel->sheet('First sheet', function($sheet) {
// 	    	$data = DB::select("SELECT use_id, use_name FROM users");

// 	    	foreach($data as $key => $item){
// 	    		$data[$key] = (array)$item;
// 	    	}

// 	    	$sheet->fromArray($data);
// 	    	$dataTitle = ['A1' => 'ID', 'B1' => 'User Name'];
// 	    	foreach($dataTitle as $cel => $value){
// 	    		$sheet->cell($cel, function($cell) use($value) {

// 				    $cell->setValue($value);

// 				});
// 	    	}

// 	    	$sheet->cells('A1:A5', function($cells) {

// 			    // manipulate the range of cells
// 			    $cells->setFontWeight('bold');

// 			    // 
// 			    $cells->setFont(array(
// 				    'family'     => 'Calibri',
// 				    'size'       => '16',
// 				    'bold'       =>  true
// 				));

// 				// Set all borders (top, right, bottom, left)
// 				$cells->setBorder('solid', 'none', 'none', 'solid');

// 				// Set borders with array
// 				$cells->setBorder(array(
// 				    'borders' => array(
// 				        'top'   => array(
// 				            'style' => 'solid'
// 				        ),
// 				    )
// 				));

// 				// Set alignment to center
// 				$cells->setAlignment('center');	

// 			});


	    	
// 	    });

// 	})->store('xlsx');

// });