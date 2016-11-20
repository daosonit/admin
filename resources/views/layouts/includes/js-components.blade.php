<!-- jQuery 2.1.4 -->
<script src="{{ asset('assets/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/dist/js/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- Morris.js charts -->
<script src="{{ asset('assets/dist/js/raphael-min.js') }}"></script>

<!-- Sparkline -->
<script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('assets/plugins/knob/jquery.knob.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('assets/dist/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('assets/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/app.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->



<!-- AdminLTE for demo purposes -->
<script src="{{ asset('assets/dist/js/demo.js') }}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

<script src="{{ asset('assets/plugins/ckeditor/ckeditor.js') }}"></script>

<script src="{{ asset('assets/mytour/js/mytour.app.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->


<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script> 


<script src="{{ asset('assets/plugins/tokeninput/src/jquery.tokeninput.js') }}"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script> -->

<script src="{{ asset('assets/plugins/morris/morris.min.js') }}"></script> 

<script src="{{ asset('assets/dist/js/pages/dashboard.js') }}"></script>

<script type="text/javascript">
	//iCheck for checkbox and radio inputs
  $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_square-blue',
	radioClass: 'iradio_square-blue',
	increaseArea: '20%' // optional
  });
</script>

<script type="text/javascript">
  $(function () {
    $("#example1").DataTable();
    $(".textarea").wysihtml5();
  });
</script>

 



