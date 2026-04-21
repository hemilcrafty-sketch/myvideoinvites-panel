@inject('roleManager', 'App\Http\Controllers\Utils\RoleManager')
@inject('contentManager', '\App\Http\Controllers\Admin\Utils\ContentManager')
@include('layouts.masterhead')
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="row">
            <div class="col-xl-3 mb-30">
                <a href="{{ route('show_v_cat') }}">
                    <div class="card-box height-200-p widget-style2">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-600 font-13">Video Templates Category</div>
                                <div class="h5 mb-0" style="opacity: 0;">Video Templates Category</div>
                                <div class="h5 mb-0">Total - {{ $datas['video_cat_item'] }}</div>
                                <div class="h5 mb-0">Live - {{ $datas['video_cat_item_live'] }}</div>
                                <div class="h5 mb-0">Unlive - {{ $datas['video_cat_item_unlive'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 mb-30">
                <a href="{{ route('show_v_item') }}">
                    <div class="card-box height-200-p widget-style2">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-600 font-13">Video Templates Item</div>
                                <div class="h5 mb-0" style="opacity: 0;">Video Templates Item</div>
                                <div class="h5 mb-0">Total - {{ $datas['video_template_item'] }}</div>
                                <div class="h5 mb-0">Live - {{ $datas['video_template_item_live'] }}</div>
                                <div class="h5 mb-0">Unlive - {{ $datas['video_template_item_unlive'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div style="display: none;">
            <form method="post" id="dynamic_form" enctype="multipart/form-data">
                <span id="result"></span>
                @csrf
                <div class="row">
                    <div class="col-md-2 col-sm-12">
                        <div class="form-group">
                            <h6>Cache Version</h6>
                            <input class="form-control-file form-control" type="number" name="cache_ver"
                                value="{{ $datas['cache'] }}" required>
                        </div>
                    </div>
                    <div class="col-md-1 col-sm-12">
                        <div class="form-group">
                            <h6 style="opacity: 0;">.</h6>
                            <input class="btn btn-primary" type="submit" name="submit" value="update">
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@include('layouts.masterscript')
<script>
    $('#dynamic_form').on('submit', function(event) {
        event.preventDefault();
        count = 0;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        var formData = new FormData(this);
        $.ajax({
            url: 'update_cache_ver',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                var main_loading_screen = document.getElementById("main_loading_screen");
                main_loading_screen.style.display = "block";
            },
            success: function(data) {
                hideFields();
                if (data.error) {
                    var error_html = '';
                    for (var count = 0; count < data.error.length; count++) {
                        error_html += '<p>' + data.error[count] + '</p>';
                    }
                    $('#result').html('<div class="alert alert-danger">' + error_html + '</div>');
                } else {
                    $('#result').html('<div class="alert alert-success">' + data.success +
                        '</div>');
                }
                setTimeout(function() {
                    $('#result').html('');
                }, 3000);
            },
            error: function(error) {
                hideFields();
                window.alert(error.responseText);
            },
            cache: false,
            contentType: false,
            processData: false
        })
    });

    function hideFields() {
        var main_loading_screen = document.getElementById("main_loading_screen");
        main_loading_screen.style.display = "none";
    }
</script>
</body>

</html>
