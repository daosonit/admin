<?php namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\MenuGroup;
use Log;
class MenuController extends Controller {



	public function __construct(Menu $menu, MenuGroup $menuGroup)
	{
		$this->middleware('admin.su');
		$this->menu = $menu;
		$this->menuGroup = $menuGroup;
	}



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$menuQuery = $this->menu->select('*');
		if($request->get('menu_group_id') > 0){
			$menuQuery->ofGroup($request->get('menu_group_id'));
		}
		$menus = $menuQuery->get();
		$menus->load('menuGroup');
		$menuGroups = $this->getMenuGroups();
		return view('components.system.menu.index', compact('menus', 'menuGroups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$menuGroups = $this->getMenuGroups();
		return view('components.system.menu.create', compact('menuGroups'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(MenuRequest $request)
	{
		$this->menu->create($request->all());
		return redirect()->route('system.menus.index');
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
			$menu = $this->menu->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menuGroups = $this->getMenuGroups();
		return view('components.system.menu.edit', compact('menuGroups', 'menu'));
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(MenuRequest $request, $id)
	{
		try {
			$menu = $this->menu->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menu->fill($request->all());
		$menu->active = $request->get('active', 0);
		$menu->save();
		return redirect()->route('system.menus.index');
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
			$menu = $this->menu->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menu->delete();
		return redirect()->route('system.menus.index');
	}


	private function getMenuGroups()
	{
		$menuGroups = [];
		$this->menuGroup->all()->map(function($item) use(&$menuGroups) {
		    $menuGroups[$item->id] = $item->name;
		});
		return $menuGroups;
	}

}
