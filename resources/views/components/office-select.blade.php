<div class="row">
    <div class="col-md-{{ $view_grid }} {{$office_id ? 'd-none' : ''}}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="layer_id">Custom Office Layer </label>
            <select id="layer_id" class="form-control rounded-0 select-select2"
                    name="layer_id">
                <option value="" selected="selected">--Select--</option>
                @foreach($custom_layers as $layer)
                    <option value="{{$layer['layer_level']}}">{{$layer['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div style="display: none" class="col-md-{{ $view_grid }} {{$office_id ? 'd-none' : ''}}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="office_layer_id">Ministry/Division</label>
            <select name="office_layer" id="office_layer_id" class="form-control rounded-0 select-select2">
                <option value="0">--Select--</option>

            </select>
        </div>
    </div>
    <div id="custom_layer_div" style="display: none;" class="col-md-{{ $view_grid }} {{$office_id ? 'd-none' : ''}}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="custom_layer_id">Custom Office Layer </label>
            <select id="custom_layer_id" class="form-control rounded-0 select-select2"
                    name="custom_layer_id">
                <option value="0">--Select--</option>

            </select>
        </div>
    </div>
    <div id="office_origin_div" style="display: none;" class="col-md-{{ $view_grid }} {{$office_id ? 'd-none' : ''}}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="office_origin_id">Office Type</label>
            <select name="office_origin" id="office_origin_id"
                    class="form-control rounded-0 select-select2">
                <option value="0">--Select--</option>

            </select>
        </div>
    </div>
    <div id="office_div" style="display: none;" class="col-md-{{ $view_grid }}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="office_id">Office</label>
            <select id="office_id" class="form-control rounded-0 select-select2"
                    name="office_id" {{$office_id ? 'disabled' : ''}}>
                @if(Auth::check() && $office_id)
                    <option value="{{$office_id}}">{{Auth::user()->current_designation->office_name_en}}</option>
                @else
                    <option value="0">--Select--</option>
                @endif
            </select>
        </div>
    </div>
    <div id="office_unit_div" style="display: none;" class="col-md-{{ $view_grid }}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="office_unit_id">Office Unit</label>
            <select id="office_unit_id" class="form-control rounded-0 select-select2" name="office_unit_id">
                <option value="0">--Select--</option>

            </select>
        </div>
    </div>

    <div id="office_org_div" style="display: none;" class="col-md-{{ $view_grid }}">
        <div class="form-group">
            <label class="form-label font-weight-bolder" for="office_unit_org_id">Designation</label>
            <select id="office_unit_org_id" class="form-control rounded-0 select-select2" multiple="multiple"
                    name="office_unit_org_id[]">
                <option value="0">--Select--</option>
            </select>
        </div>
    </div>

</div>

<script !src="">

    $("select#office_ministry_id").change(function () {
        var ministry_id = $(this).children("option:selected").val();
        loadLayer(ministry_id);
    });

    $("select#office_unit_id").change(function () {
        var unit_id = $(this).children("option:selected").val();
        // unitOrganogram(unit_id);
        @if($show_organogram == 'true')
        unitOrganogram(unit_id);
        @endif
    });

    $("select#layer_id").change(function () {
        var layer_id = $(this).children("option:selected").val();
        if (layer_id == 1 || layer_id == 2) {
            $('#office_div').show();
            $('#custom_layer_div').hide();
            $('#office_origin_div').hide();
            $('#office_unit_div').hide();
            loadOfficeByLayer(layer_id);
            // loadOffice(custom_layer_id);
        } else if (layer_id == 3) {
            $('#custom_layer_div').show();
            $('#office_div').hide();
            $('#office_origin_div').hide();
            $('#office_unit_div').hide();
            loadOfficeCustomLayer(layer_id);
        } else if (layer_id == '') {
            $('#custom_layer_div').hide();
            $('#office_div').hide();
            $('#office_origin_div').hide();
            $('#office_unit_div').hide();
        } else {
            loadOfficeOrigin(layer_id);
            $('#office_div').hide();
            $('#office_origin_div').show();
            $('#custom_layer_div').hide();
            $('#office_unit_div').hide();
        }
    });

    function loadLayer(ministry_id) {
        var url = 'load_layer_ministry_wise';
        var data = {ministry_id};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_layer_id").html(responseDate);
        });
    }

    function unitOrganogram(unit_id) {
        var url = 'load_org_wise_data';
        var data = {unit_id};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_unit_org_id").html(responseDate);
            $('#office_org_div').show();
        });
    }

    function loadOfficeCustomLayer(office_layer_level) {
        var url = '{{url('guests/load-custom-layer-level-wise')}}';
        var data = {office_layer_level};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#custom_layer_id").html(responseDate);
        });
    }

    $("select#custom_layer_id").change(function () {
        var custom_layer_id = $(this).children("option:selected").val();
        loadOfficeCustomLayerWise(custom_layer_id);
    });

    function loadOfficeCustomLayerWise(custom_layer_id) {
        var url = '{{url('guests/load-office-custom-layer-wise')}}';
        var data = {custom_layer_id};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_div").show();
            $("#office_id").html(responseDate);
        });
    }

    $("select#office_layer_id").change(function () {
        var office_layer_id = $(this).children("option:selected").val();
        $('#office_layer').val(office_layer_id);
        loadOfficeOrigin(office_layer_id);
    });

    function loadOfficeOrigin(office_layer_level) {
        var url = '{{url('guests/load-office-origin-layer-level-wise')}}';
        var data = {office_layer_level};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_origin_id").html(responseDate);
        });
    }

    $("select#office_origin_id").change(function () {
        var office_origin_id = $(this).children("option:selected").val();
        $('#office_unit_div').hide();
        loadOffice(office_origin_id);
    });

    function loadOfficeByLayer(office_layer_level) {
        var url = '{{url('guests/load-office-layer-wise')}}';
        var data = {office_layer_level};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_id").html(responseDate);
        });
    }

    function loadOffice(office_origin_id) {
        var url = '{{url('guests/load-office-origin-wise')}}';
        var data = {office_origin_id};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_div").show();
            $("#office_id").html(responseDate);
        });
    }

    function loadOfficeUnit(office_id) {
        var url = '{{url('guests/load-unit-office-wise')}}';
        var data = {office_id};
        var datatype = 'html';
        ajaxCallAsyncCallback(url, data, datatype, 'GET', function (responseDate) {
            $("#office_unit_div").show();
            $("#office_unit_id").html(responseDate);
        });
    }

    @if($office_id)
    $(document).ready(function () {
        $("#office_div").show();
        @if($only_office != 'true' && $is_unit_show == 'true')
        loadOfficeUnit({{$office_id}})
        @endif
    });
    @endif

    @if($is_unit_show == 'true')
    $("select#office_id").change(function () {
        var office_id = $(this).children("option:selected").val();
        loadOfficeUnit(office_id);
    });
    @endif
</script>
