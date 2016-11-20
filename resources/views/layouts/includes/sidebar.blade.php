  <!-- Sidebar user panel -->
  <div class="user-panel">
    <div class="pull-left image">
      <img src="{{ asset($adminUser->getAvatar()) }}" class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
      <p>{{ $adminUser->name }}</p>
      <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
  </div>
  <!-- search form -->
  <form action="#" method="get" class="sidebar-form">
    <div class="input-group">
      <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
    </div>
  </form>

  <!-- /.search form -->
  <!-- sidebar menu: : style can be found in sidebar.less -->
  <ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>    
    @foreach($menuGroups as $menuGroup)
      @if($adminUser->hasRole($menuGroup->getVisibleRoles()))
      <li class="treeview">
        <a href="#">
          <i class="fa {{ $menuGroup->getIcon() }}"></i> <span>{{ $menuGroup->name }}</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
          @foreach($menuGroup->menus as $menu)
            @if($adminUser->hasRole($menu->getVisibleRoles()))
            <li>
              <a href="#"> {{ $menu->name }}<i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                @foreach($menu->menuItems as $menuItem)
                  @if(($adminUser->can($menuItem->visible_on) || $adminUser->hasRole($menuItem->visible_on)) && Route::getRoutes()->hasNamedRoute($menuItem->route))
                    <li class="menu_item">
                      <a data-src="{{ route($menuItem->route) }}" data-tab-id="tab_menu_{{ $menuItem->id }}" href="{{ route($menuItem->route) }}"></i>{{ $menuItem->name }}</a>
                    </li>
                  @endif
                @endforeach
              </ul>
            </li>
            @endif
          @endforeach
        </ul>
      </li>
      @endif
    @endforeach
  </ul>




