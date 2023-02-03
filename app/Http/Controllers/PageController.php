<?php

namespace App\Http\Controllers;

/**
 * PageController
 * Contains methods for returning responses to the client, all API controllers need to extend from this
 */
class PageController extends Controller
{
    /**
     * @var array
     */
    protected array $page = [
        'meta' => [
            'title' => 'Zenon Network Explorer',
            'description' => 'Zenon Hub is an explorer for the Network of Momentum, providing a range of tools for interacting with and building on-top of the Zenon Network'
        ],
        'active' => null,
        'data' => null,
    ];

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render(string $view, array $data = []): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view($view, array_merge($this->page, $data));
    }
}
