<!DOCTYPE html>
<html>
<head>
    @include('layouts.includes.head-meta')
    @section('css')
    @show
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <!-- Content Wrapper. Contains page content -->
    <!-- Main content -->
    <section class="content">
      	@yield('content')
    </section>
    <!-- /.content -->
<!-- ./wrapper -->
@include('layouts.includes.js-components')
@yield('js')

@section('js-footer')
@show
</body>
</html>
