<?php

declare(strict_types=1);

uses()->group('components', 'flash');

test('flash component can be rendered', function () {

    session()->flash('alert', 'Info alert');

    $view = (string) $this->blade('<x-alerts.flash />');

    $expected = <<<'HTML'
<div role="alert" class="alert alert-info shadow">
    Info alert
</div>
HTML;

    $this->assertComponentRenders($expected, $view);

});

test('flash component success can be rendered', function () {

    session()->flash('success', 'Success alert');

    $viewSuccess = (string) $this->blade('<x-alerts.flash type="success" />');
    $expectedSuccess = <<<'HTML'
                    <div role="alert" class="alert alert-success shadow">
                        Success alert
                    </div>
                    HTML;

    $this->assertComponentRenders($expectedSuccess, $viewSuccess);

});

test('flash component error can be rendered', function () {

    session()->flash('error', 'Error alert');

    $viewError = (string) $this->blade('<x-alerts.flash type="error" />');
    $expectedError = <<<'HTML'
                    <div role="alert" class="alert alert-danger shadow">
                        Error alert
                    </div>
                    HTML;

    $this->assertComponentRenders($expectedError, $viewError);

});

test('flash component can be slotted', function () {

    session()->flash('alert', 'Form was successfully submitted.');

    $template = <<<'HTML'
            <x-alerts.flash>
                <span>Hello World</span>
                {{ $component->message() }}
            </x-alerts.flash>
            HTML;

    $expected = <<<'HTML'
            <div role="alert" class="alert alert-info shadow">
                <span>Hello World</span>
                Form was successfully submitted.
            </div>
            HTML;

    $this->assertComponentRenders($expected, $template);

});

test('flash component multiple messages can be used', function () {

    session()->flash('alert', [
        'Form was successfully submitted.',
        'We have sent you a confirmation email.',
    ]);

    $template = <<<'HTML'
            <x-alerts.flash>
                <span>Hello World</span>
                {{ implode(' ', $component->messages()) }}
            </x-alerts.flash>
            HTML;

    $expected = <<<'HTML'
            <div role="alert" class="alert alert-info shadow">
                <span>Hello World</span>
                Form was successfully submitted. We have sent you a confirmation email.
            </div>
            HTML;

    $this->assertComponentRenders($expected, $template);

});
