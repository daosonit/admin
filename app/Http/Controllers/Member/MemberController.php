<?php namespace App\Http\Controllers\Member;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller {

	private $member;

	public function __construct(Member $member)
	{
		$this->member = $member;
	}



	public function showList()
	{
		$members = $this->member->all();
		return View::make('components.member.index', compact('members'));
	}


}
