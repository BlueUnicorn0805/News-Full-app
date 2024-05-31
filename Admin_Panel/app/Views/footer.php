<!-- /.content-wrapper -->
<footer class="main-footer">
    <strong> News Panel by &copy; <a href="https://www.wrteam.in/" target='_blank'
            rel="noopener noreferrer">WRTeam</a></strong>
</footer>

<!-- jQuery -->
<script src="<?= APP_URL ?>public/plugins/jquery/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<!-- Bootstrap 4 -->
<script src="<?= APP_URL ?>public/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Validadtion js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.js"></script>
<!-- Ekko Lightbox -->
<script src="<?= APP_URL ?>public/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>

<!-- bs-custom-file-input -->
<script src="<?= APP_URL ?>public/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!--datetimepicker-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<!-- Bootstrap Switch -->
<script src="<?= APP_URL ?>public/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>

<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.18.1/extensions/fixed-columns/bootstrap-table-fixed-columns.js"
    integrity="sha512-vUtvztHGEEX0eswg+OM1xGKszUHhpRI32JPmbvlGVaxedq5UPknVAofneF2IiDh5wupeFEbnr10gtPOhzf+OwA=="
    crossorigin="anonymous"></script>

<!-- Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<!-- Admin App -->
<script src="<?= APP_URL ?>public/dist/js/adminlte.min.js"></script>
<!-- Admin for demo purposes -->
<script src="<?= APP_URL ?>public/dist/js/demo.js"></script>

<script>
    $(function () {
        $(document).on('click', '[data-toggle="lightbox"]', function (event) {
            event.preventDefault();
            $(this).ekkoLightbox({
                alwaysShowClose: true
            });
        });
    });
</script>

<script type="text/javascript">
    $(".nav a").each(function () {
        var pageUrl = window.location.href.split(/[?#]/)[0];

        if (this.href == pageUrl) {
            $(this).addClass("active");
            $(this).parent().addClass("active"); // add active to li of the current link
            $(this).parent().parent().prev().addClass("active"); // add active class to an anchor
            $(this).parent().parent().parent().addClass("menu-open");
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#error_msg').delay(3000).fadeOut();
        $('#success_msg').delay(3000).fadeOut();
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>