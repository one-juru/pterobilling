@extends('layouts.admin')

@inject('extension_model', 'App\Models\Extension')

@section('title', 'Cloudflare Settings')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('api.admin.extension.update', ['id' => 'Cloudflare']) }}" method="PUT" data-callback="settingForm">
                    @csrf

                    <div class="card-body row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="emailInput">Account Email</label>
                                <input type="text" name="email" value="{{ $extension_model->where(['extension' => 'Cloudflare', 'key' => 'email'])->value('value') }}" class="form-control" id="emailInput" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="apiKeyInput">API Token</label>
                                <input type="text" name="api_key" value="{{ $extension_model->where(['extension' => 'Cloudflare', 'key' => 'api_key'])->value('value') }}" class="form-control" id="apiKeyInput" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
    <script>
        function settingForm(data) {
            if (data.success) {
                toastr.success(data.success)
            } else if (data.error) {
                toastr.error(data.error)
            } else if (data.errors) {
                data.errors.forEach(error => { toastr.error(error) });
            } else {
                wentWrong()
            }
        }
    </script>
@endsection
