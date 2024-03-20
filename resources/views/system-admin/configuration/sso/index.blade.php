@extends('layout.master')
@section('content')
    <div class="row">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title"></div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <button class="btn btn-primary" onclick="ConfigureSSOContainer.createSSOPanel()">Add SSO Configuration</button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="configure_sso_lists_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable no-footer" id="configured_sso_lists">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2 sorting_disabled" rowspan="1" colspan="1" aria-label="">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#configure_sso_lists .form-check-input" value="1">
                                    </div>
                                </th>
                                <th class="text-end min-w-100px sorting" tabindex="0" aria-controls="configure_sso_lists" rowspan="1" colspan="1" aria-label="Name">Name</th>
                                <th class="text-end min-w-100px sorting" tabindex="0" aria-controls="configure_sso_lists" rowspan="1" colspan="1" aria-label="Status">Status</th>
                                <th class="text-end min-w-70px sorting_disabled" rowspan="1" colspan="1" aria-label="Actions">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="fw-bold text-gray-600">
                            @foreach($sso_lists as $sso_list)
                                <tr class="{{$loop->odd ? "odd" : "even"}}">
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ml-5">
                                                <p class="text-gray-800 text-hover-primary fs-5 fw-bolder" data-kt-ecommerce-product-filter="product_name">{{$sso_list['sso_name']}}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <!--begin::Status=-->
                                    <td class="text-end pe-0" data-order="Scheduled">
                                        <!--begin::Badges-->
                                        <div class="badge badge-light-primary">{{$sso_list['is_active'] == 1 ? 'Active' : 'readonly'}}</div>
                                        <!--end::Badges-->
                                    </td>
                                    <!--end::Status=-->
                                    <!--begin::Action=-->
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path
                                                        d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                        fill="black"></path>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="javascript:;" class="menu-link px-3">Edit</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:;" class="menu-link px-3" onclick="ConfigureSSOContainer.setApplicationSSO($(this))" data-sso-id="{{$sso_list['id']}}">Set Application SSO</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:;" class="menu-link px-3" data-kt-ecommerce-product-filter="delete_row">Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row d-none">
                        <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start">
                            <div class="dataTables_length" id="configure_sso_lists_length"><label><select name="configure_sso_lists_length" aria-controls="configure_sso_lists" class="form-select form-select-sm form-select-solid">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select></label>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                            <div class="dataTables_paginate paging_simple_numbers" id="configure_sso_lists_paginate">
                                <ul class="pagination">
                                    <li class="paginate_button page-item previous disabled" id="configure_sso_lists_previous"><a href="#" aria-controls="configure_sso_lists" data-dt-idx="0" tabindex="0" class="page-link"><i class="previous"></i></a></li>
                                    <li class="paginate_button page-item active"><a href="#" aria-controls="configure_sso_lists" data-dt-idx="1" tabindex="0" class="page-link">1</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="configure_sso_lists" data-dt-idx="2" tabindex="0" class="page-link">2</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="configure_sso_lists" data-dt-idx="3" tabindex="0" class="page-link">3</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="configure_sso_lists" data-dt-idx="4" tabindex="0" class="page-link">4</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="configure_sso_lists" data-dt-idx="5" tabindex="0" class="page-link">5</a></li>
                                    <li class="paginate_button page-item next" id="configure_sso_lists_next"><a href="#" aria-controls="configure_sso_lists" data-dt-idx="6" tabindex="0" class="page-link"><i class="next"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Table-->
            </div>
            <!--end::Card body-->
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var ConfigureSSOContainer = {
            createSSOPanel: function () {
                url = '{{route('admin.configuration.sso.create')}}';
                data = {};
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status != 'error') {
                        $('#m_panel_body').html(response)
                        $('#m_panel_toggle').click(quickPanelToggler());
                    } else {
                        toastr.error('Trouble in creating sso configuration');
                        console.log(response)
                    }

                })
            },

            storeSSOPanel: function () {
                url = '{{route('admin.configuration.sso.store')}}';
                data = $('#add_sso_form').serialize();
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status == 'success') {
                        toastr.error('Successfully stored sso configuration');
                        $('#m_panel_toggle').click(quickPanelToggler());
                    } else {
                        toastr.error('Trouble in saving sso configuration');
                        console.log(response)
                    }
                })
            },

            setApplicationSSO: function (elem) {
                url = '{{route('admin.configuration.sso.set')}}'
                data = {sso_id: $(elem).attr('data-sso-id')}
                ajaxCallAsyncCallbackAPI(url, data, 'post', function (response) {
                    if (response.status == 'success') {
                        toastr.success('Successfully set sso configuration');
                    } else {
                        toastr.error('Trouble in setting sso configuration');
                        console.log(response)
                    }
                })
            },
        };
    </script>
@endsection
