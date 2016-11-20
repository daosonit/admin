<?php namespace App\Http\Controllers;

use App\Http\Requests\MenuGroupRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\MenuGroup;
use Log;
class MenuGroupController extends Controller {



	public function __construct(MenuGroup $menuGroup)
	{
		$this->middleware('admin.su');
		$this->menuGroup = $menuGroup;
	}



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$menuGroups = $this->menuGroup->all();
		return view('components.system.menu-group.index', compact('menuGroups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('components.system.menu-group.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(MenuGroupRequest $request)
	{
		$this->menuGroup->create($request->all());
		return redirect()->route('system.menu-groups.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		try {
			$menuGroup = $this->menuGroup->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('MenuGroup not found!', 404);
		}
		return view('components.system.menu-group.edit', compact('menuGroup'));
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(MenuGroupRequest $request, $id)
	{
		try {
			$menuGroup = $this->menuGroup->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('MenuGroup not found!', 404);
		}
		$menuGroup->fill($request->all());
		$menuGroup->active = $request->get('active', 0);
		$menuGroup->save();
		return redirect()->route('system.menu-groups.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$menuGroup = $this->menuGroup->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('MenuGroup not found!', 404);
		}
		$menuGroup->delete();
		return redirect()->route('system.menu-groups.index');
	}

}
