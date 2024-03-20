<select name="office_layer_id" id="office_layer_id" class="form-control rounded-0" required>
    <option value="0">--Select--</option>
    @foreach ($custom_layers as $custom_layer)
        <option value="{{ $custom_layer['id'] }}">{{ $custom_layer['name'] }}</option>
    @endforeach
</select>
