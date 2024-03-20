<?php

namespace App\View\Components;

use App\Traits\UserInfoCollector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class OfficeSelect extends Component
{
    use UserInfoCollector;

    public $custom_layers = [];
    public $view_grid;
    public $is_unit_show;
    public $only_office;
    public $show_organogram;
    public $prefix_select_id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($grid, $unit, $onlyoffice = false, $organogram = false, $selectprefix = '')
    {
        $this->view_grid = $grid;
        $this->is_unit_show = $unit;
        $this->only_office = $onlyoffice;
        $this->show_organogram = $organogram;
        $this->prefix_select_id = $selectprefix;

//        $layers = Cache::remember('office-custom-layers', 60 * 60 * 24, function () {
//            return OfficeCustomLayer::get();
//        });


        // $layer_levels = OfficeCustomLayer::select('layer_level')->groupBy('layer_level')->get();
//        $layer_levels = array_column($layers->toArray(), null, 'layer_level');
//        $custom_layers_temp = array();
//        $custom_layers = array();
//        foreach ($layer_levels as $key => $value) {
//            $name = '';
//            foreach ($layers as $key => $layer) {
//
//                if ($value['layer_level'] == $layer->layer_level) {
//                    if ($value['layer_level'] == 3) {
//                        $name = 'অন্যান্য দপ্তর/সংস্থা';
//                    } else {
//                        $name .= $layer->name . '/';
//                    }
//                }
//            }
//            $custom_layers_temp['id'] = $value['layer_level'];
//            $custom_layers_temp['name'] = trim($name, '/');
//            $custom_layers[$value['layer_level']] = $custom_layers_temp;
//        }
//        ksort($custom_layers);

        $custom_layers =  $this->initHttpWithClientToken($this->getUsername() ?: '200000002962')->post(config('constants.api_url') . 'api/custom-layer-level', [])->json();

//        dd($custom_layers);
        $this->custom_layers = $custom_layers['status'] == 'success' ? $custom_layers['data'] : [];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $custom_layers = $this->custom_layers;
        if (Auth::check() && ($this->getUserOrganogramRole() == config('menu_role_map.office_admin') || $this->getUserOrganogramRole() == config('menu_role_map.unit_admin'))) {
            $office_id = Auth::user()->current_office_id();
        } else {
            $office_id = null;
        }

        return view('components.office-select', compact('custom_layers', 'office_id'));
    }
}
