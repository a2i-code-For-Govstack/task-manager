<select name="office_unit_id" id="office_unit_id" class="form-control rounded-0">
    <option value="0">--Select--</option>
    @foreach($units as $unit)
        <option value="{{$unit['office_unit_id']}}" data-unit-name-en="{{$unit['unit_name_eng']}}">{{$unit['unit_name_bng']}}</option>
    @endforeach
</select>

