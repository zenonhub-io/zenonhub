<?php

namespace App\Http\Controllers;

/**
 * PageController
 * Contains methods for returning responses to the client, all API controllers need to extend from this
 */
class PageController
{
    protected array $page = [
        'meta' => [
            'title' => 'Zenon Network Explorer',
            'description' => 'Zenon Hub is an explorer for the Zenon Network blockchain, providing a range of tools for interacting with and building on-top of the Network of Momentum',
        ],
        'active' => null,
        'data' => null,
    ];

    public function render(string $view, array $data = []): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $this->page['meta']['title'] .= ' | '.config('app.name');

        return view($view, array_merge($this->page, $data));
    }
}
