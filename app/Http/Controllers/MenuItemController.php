<?php namespace App\Http\Controllers;

use App\Http\Requests\MenuItemRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Menu;
use Log;
class MenuItemController extends Controller {



	public function __construct(MenuItem $menuItem, Menu $menu)
	{
		$this->middleware('admin.su');
		$this->menuItem = $menuItem;
		$this->menu = $menu;
	}



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$menuItemQuery = $this->menuItem->select('*');
		if($request->get('menu_id') > 0){
			$menuItemQuery->ofMenu($request->get('menu_id'));
		}
		$menuItems = $menuItemQuery->orderBy('menu_id', 'ASC')->get();
		$menuItems->load('menu');
		$menus = $this->getMenus();
		return view('components.system.menu-item.index', compact('menuItems', 'menus'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$menus = $this->getMenus();
		return view('components.system.menu-item.create', compact('menus'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(MenuItemRequest $request)
	{
		$this->menuItem->create($request->all());
		return redirect()->route('system.menu-items.index');
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
			$menuItem = $this->menuItem->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menus = $this->getMenus();
		return view('components.system.menu-item.edit', compact('menus', 'menuItem'));
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(MenuItemRequest $request, $id)
	{
		try {
			$menuItem = $this->menuItem->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menuItem->fill($request->all());
		$menuItem->active = $request->get('active', 0);
		$menuItem->save();
		return redirect()->route('system.menu-items.index');
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
			$menuItem = $this->menuItem->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Menu not found!', 404);
		}
		$menuItem->delete();
		return redirect()->route('system.menu-items.index');
	}


	private function getMenus()
	{
		$menus = [];
		$this->menu->all()->map(function($item) use(&$menus) {
		    $menus[$item->id] = $item->name;
		});
		return $menus;
	}

}
