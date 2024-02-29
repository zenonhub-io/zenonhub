<?php

test('alert component can be rendered', function () {

    $view = $this->blade('<x-alerts.alert :message="$message" />', [
        'message' => 'Alert info',
    ]);

    $view->assertSee('alert-info');
    $view->assertSee('Alert info');

})->group('components', 'alert');

test('alert component type can be changed', function () {

    $view = $this->blade('<x-alerts.alert :message="$message" :type="$type" />', [
        'message' => 'Alert success',
        'type' => 'success',
    ]);

    $view->assertSee('alert-success');
    $view->assertSee('Alert success');

})->group('components', 'alert');

test('alert component content can be changed', function () {

    $view = $this->blade('<x-alerts.alert><i class="bi bi-info"></i> Custom content</x-alerts.alert>');

    $view->assertSee('alert-info');
    $view->assertSee('<i class="bi bi-info"></i> Custom content', false);

})->group('components', 'alert');
