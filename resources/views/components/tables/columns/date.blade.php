@props(['date' => null, 'human' => false, 'tooltip' => true, 'syntax' => true])

<x-date-time.carbon :date="$date" :human="$human" :show-tooltip="$tooltip" :syntax="$syntax" />
