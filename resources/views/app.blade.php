<!DOCTYPE html>
<html>
<head>
  @include('layouts.includes.head-meta')
  
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<header class="main-header">
		@include('layouts.includes.header')
	</header>
	<!-- Left side column. contains the logo and sidebar -->
	<aside class="main-sidebar">
	  	<!-- sidebar: style can be found in sidebar.less -->
	  	<section class="sidebar">
			@include('layouts.includes.sidebar')
		</section>
	</aside>

  <!-- Content Wrapper. Contains page content --> 
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            @include('layouts.includes.content-header')
        </section>
        <!-- Main content -->
        <section id="section-tab-content" class="content">
          <div id="tab-content" class="tab-content">
          	@yield('content')
          </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
    	@include('layouts.includes.footer')
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
    	@include('layouts.includes.control-sidebar')
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

@include('layouts.includes.js-components')
@yield('js')

</body>
</html>
