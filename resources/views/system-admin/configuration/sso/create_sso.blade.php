<!--begin::Form-->
<form class="form" action="#" id="add_sso_form">
    <div class="modal-body pt-1 pb-1" style="min-height: 70vh;height: 80vh;overflow: scroll;">
        <div class="row">
            <div class="col-md-8">
                <div class="fv-row mb-3">
                    <input type="text" class="form-control form-control-solid" placeholder="SSO Name" name="sso_name"/>
                </div>
                <div class="fv-row mb-3">
                    <input type="text" class="form-control form-control-solid" placeholder="SSO Login Url" name="sso_login_url"/>
                </div>
                <div class="fv-row mb-3">
                    <input type="text" class="form-control form-control-solid" placeholder="SSO Logout Url" name="sso_logout_url"/>
                </div>
                <div class="fv-row mb-3">
                    <input type="text" class="form-control form-control-solid" placeholder="SSO API URL" name="sso_api_url"/>
                </div>
                <div class="fv-row mb-3">
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" checked id="is_active" name="is_active"/>
                        <span class="form-check-label fw-bold" for="is_active">Active Status</span>
                    </label>
                </div>
                <div class="fv-row mb-3">
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" checked id="is_custom" name="is_custom"/>
                        <span class="form-check-label fw-bold" for="is_custom">Is Custom</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer py-2 flex-right" id="m_panel_footer">
        <button onclick="ConfigureSSOContainer.storeSSOPanel($(this))" type="button" id="add_sso_configure_submit" class="btn btn-primary rounded-0">
            <span class="indicator-label">Submit</span>

        </button>
    </div>
</form>
<script>

</script>
