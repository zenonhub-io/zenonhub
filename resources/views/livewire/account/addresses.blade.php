<form action="{{ route('account.addresses') }}" method="post" class="needs-validation">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header">
            <h4 class="mb-0">Linked addresses</h4>
        </div>
        @if (auth()->user()->nom_accounts->count())
            <div class="table-responsive">
                <table class="table table-nowrap align-middle table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Address</th>
                            <th>Nickname</th>
                            <th>Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (auth()->user()->nom_accounts as $account)
                            <tr>
                                <td>
                                    <div class="d-flex">
                                        <div class="pe-1 flex-grow-1">
                                            <a href="{{ route('explorer.account', ['address' => $account->address]) }}">
                                                {{ $account->address }}
                                            </a>
                                        </div>
                                        <div class="ps-1">
                                            @if ($account->active_pillar)
                                                <span class="ms-2 d-inline" data-bs-toggle="tooltip" data-bs-title="Pillar">
                                                    {!! svg('pillar', 'opacity-70', 'height: 18px') !!}
                                                </span>
                                            @endif
                                            @if ($account->active_sentinel)
                                                <span class="ms-2 d-inline" data-bs-toggle="tooltip" data-bs-title="Sentinel">
                                                    {!! svg('sentinel', '', 'width: 16px') !!}
                                                </span>
                                            @endif
                                            @if ($account->is_embedded_contract)
                                                <span class="d-inline" data-bs-toggle="tooltip" data-bs-title="Embedded contract">
                                                    <i class="bi bi-file-text-fill opacity-70"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $account->pivot->nickname }}
                                </td>
                                <td>
                                    @if ($account->pivot->is_default)
                                        <span class="legend-indicator bg-success"></span>
                                    @else
                                        <span class="legend-indicator bg-danger"></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="card-body">
            @if (session('alert'))
                <x-alert
                    message="{{ session('alert.message') }}"
                    type="{{ session('alert.type') }}"
                    icon="{{ session('alert.icon') }}"
                    class="d-flex align-items-center"
                />
            @else
                <x-alert
                    message="Use SYRIUS to sign the message below and submit the generated signature"
                    type="info"
                    icon="info-circle-fill"
                    class="d-flex align-items-center"
                />
            @endif
            <div class="row mb-4 align-items-center">
                <label for="form-address" class="form-label col-md-6">Address</label>
                <div class="col-md-18">
                    <input
                        type="text"
                        id="form-address"
                        name="address"
                        class="form-control @error('address')is-invalid @enderror"
                        value="{{ old('address') }}"
                    >
                    <div class="invalid-feedback">
                        @error('address') {{ $message }} @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <label for="form-nickname" class="form-label col-md-6">Nickname</label>
                <div class="col-md-18">
                    <input
                        type="text"
                        id="form-nickname"
                        name="nickname"
                        class="form-control @error('nickname')is-invalid @enderror"
                        value="{{ old('nickname') }}"
                    >
                    <div class="invalid-feedback">
                        @error('nickname') {{ $message }} @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <label for="form-default" class="form-label col-md-6">Default</label>
                <div class="col-md-18">
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" name="default" value="1" id="form-default">
                    </div>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <label for="form-message" class="form-label col-md-6">Message</label>
                <div class="col-md-18">
                    <div class="input-group">
                        <input
                            type="text"
                            id="form-message"
                            name="message"
                            value="{{ Str::upper(Str::random(8)) }}"
                            class="form-control @error('message')is-invalid @enderror"
                            readonly
                        >
                        <span class="input-group-text js-copy" data-clipboard-target="#form-message" data-bs-toggle="tooltip" data-bs-title="Copy">
                            <i class="bi-clipboard text-zenon-blue"></i>
                        </span>
                    </div>
                    <div class="invalid-feedback">
                        @error('message') {{ $message }} @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <label for="form-signature" class="form-label col-md-6">Signature</label>
                <div class="col-md-18">
                    <input
                        type="text"
                        id="form-signature"
                        name="signature"
                        class="form-control @error('signature')is-invalid @enderror"
                    >
                    <div class="invalid-feedback">
                        @error('signature') {{ $message }} @enderror
                    </div>
                </div>
            </div>
            <button class="w-100 btn btn-outline-primary" type="submit">
                <i class="bi bi-link-45deg me-2"></i>
                Link address
            </button>
        </div>
    </div>
</form>
