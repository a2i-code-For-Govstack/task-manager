<select name="office_origin_id" id="office_origin_id" class="form-control rounded-0">
    <option value="0">--Select--</option>
    @foreach($office_origins as $office_origin)
        <option value="{{$office_origin['id']}}">{{$office_origin['office_name_bng']}}</option>
    @endforeach
</select>
