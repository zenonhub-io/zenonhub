<?php

declare(strict_types=1);

uses()->group('components', 'button');

test('button component can be rendered', function () {

    $view = $this->blade('<x-buttons.button :text="$text" />', [
        'text' => 'Basic button',
    ]);

    $view->assertSee('type="button"', false);
    $view->assertSee('Basic button');

});

test('button component type can be changed', function () {

    $view = $this->blade('<x-buttons.button :text="$text" :type="$type" />', [
        'text' => 'Submit button',
        'type' => 'submit',
    ]);

    $view->assertSee('type="submit"', false);
    $view->assertSee('Submit button');

});

test('button component content can be changed', function () {

    $view = $this->blade('<x-buttons.button><i class="bi bi-info"></i> Custom Button</x-buttons.button>');

    $view->assertSee('<i class="bi bi-info"></i> Custom Button', false);

});
