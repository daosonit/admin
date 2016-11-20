<?php namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Auth\Authenticatable;
use Auth;
use App\Models\MenuGroup;

class AppComposer {

    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $user;

    /**
     * Create a new app composer.
     * @param App\Models\ModuleGroup $moduleGroup, 
     * @param Illuminate\Contracts\Auth\Authenticatable  $users
     * @return void
     */
    public function __construct(MenuGroup $menuGroup, Authenticatable $user)
    {
        // Dependencies automatically resolved by service container...
        $this->menuGroups = $menuGroup->with('menus.menuItems')
                                      ->orderBy('order')
                                      ->orderBy('name')
                                      ->get();
        $this->user = $user;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('menuGroups', $this->menuGroups);
        $view->with('adminUser', $this->user);
    }

}