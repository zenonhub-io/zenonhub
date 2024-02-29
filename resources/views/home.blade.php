<x-app-layout>

    <x-includes.header :title="__('Home Page')" responsiveBorder>
        <div class="d-none d-md-block">
            <ul class="nav nav-tabs nav-fill nav-tabs-flush gap-6 overflow-x border-0">
                <li class="nav-item">
                    <a href="#" class="nav-link active">General</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Billing</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Password</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Notifications</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">Team</a>
                </li>
            </ul>
        </div>
        <div class="d-block d-md-none">
            <div class="dropdown">
                <button class="btn btn-sm btn-neutral w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dropdown button
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>
        </div>
    </x-includes.header>

    <x-cards.card title="Card heading" class="mb-5">
        <p class="card-text mb-4">With supporting text below as a natural lead-in to additional content.</p>

        <div class="row mb-6">
            <div class="col">
                <x-modals.modal>
                    <x-slot:trigger class="btn btn-neutral w-100">
                        Modal
                    </x-slot:trigger>

                    <x-slot:heading>
                        Modal heading
                    </x-slot:heading>

                    Some random body

                    <x-slot:footer>
                        <button type="button" class="btn btn-neutral" data-bs-dismiss="modal">Close</button>
                    </x-slot:footer>
                </x-modals.modal>
            </div>

            <div class="col">
                <button type="button" class="btn btn-neutral w-100"
                        x-data
                        x-on:click="$dispatch('open-livewire-modal', { alias: 'example-modal', params: {test: false} })"
                >Livewire modal</button>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <x-offcanvas.offcanvas>
                    <x-slot:trigger class="btn btn-neutral w-100">
                        Offcanvas
                    </x-slot:trigger>

                    <x-slot:heading>
                        Slideout heading
                    </x-slot:heading>

                    Some random body

                </x-offcanvas.offcanvas>
            </div>

            <div class="col">
                <button type="button" class="btn btn-neutral w-100"
                        x-data
                        x-on:click="$dispatch('open-livewire-offcanvas', { alias: 'example-offcanvas', title: 'Canvas heading', params: {test: false} })"
                >Livewire offcanvas</button>
            </div>
        </div>
    </x-cards.card>

    <x-cards.card class="my-3">
        <x-forms.form>
            <x-forms.group label="Form input" name="test" class="mb-3" />
            <div class="mb-3">
                @php($uuid = Str::random(8))
                <x-forms.label label="Text" for="{{ $uuid }}" />
                <x-forms.inputs.input name="text_input" id="{{ $uuid }}"/>
            </div>
            <div class="mb-3">
                @php($uuid = Str::random(8))
                <x-forms.label label="Email" for="{{ $uuid }}" />
                <x-forms.inputs.email name="email_input" id="{{ $uuid }}" />
            </div>
            <div class="mb-3">
                @php($uuid = Str::random(8))
                <x-forms.label label="Password" for="{{ $uuid }}" />
                <x-forms.inputs.password name="password_input" id="{{ $uuid }}"/>
            </div>
            <div class="mb-3">
                @php($uuid = Str::random(8))
                <x-forms.label label="Text area" for="{{ $uuid }}" />
                <x-forms.inputs.textarea name="textarea_input" id="{{ $uuid }}"/>
            </div>
            <div class="mb-3">
                <x-forms.label label="Checkbox"/>
                <x-forms.inputs.checkbox label="Check me out!" name="checked_out" />
            </div>
            <div class="mb-3">
                <x-forms.label label="Switch"/>
                <x-forms.inputs.checkbox label="Off" name="switch_me_on" value="true" switch="true" />
            </div>
            <div>
                <x-forms.label label="Radio options"/>
                <x-forms.inputs.radio label="Radio one" name="radio_buttons" value="one" />
                <x-forms.inputs.radio label="Radio two" name="radio_buttons" value="two" />
            </div>
        </x-forms.form>
    </x-cards.card>

    <div class="d-grid gap-4 my-5">
        <x-alerts.alert message="Info alert"/>
        <x-alerts.alert message="Success alert" type="success"/>
        <x-alerts.alert message="Warning alert" type="warning"/>
        <x-alerts.alert message="Danger alert" type="danger"/>
    </div>

    <x-date-time.carbon :date-="now()->subDays(2)->subMinutes(30)" class="mb-3" human />
    <x-date-time.carbon :date-="now()" />
</x-app-layout>
