@props(['name'])

@php
    $icons = [
        'list' => ['M9 5h11M9 12h11M9 19h11M4 5h.01M4 12h.01M4 19h.01'],
        'tasks' => ['M9 5h11M9 12h11M9 19h11M4 5h.01M4 12h.01M4 19h.01'],
        'play' => ['M8 5v14l11-7-11-7Z'],
        'active' => ['M8 5v14l11-7-11-7Z'],
        'users' => [
            'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2',
            'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
            'M22 21v-2a4 4 0 0 0-3-3.87',
            'M16 3.13a4 4 0 0 1 0 7.75',
        ],
        'participants' => [
            'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2',
            'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
            'M22 21v-2a4 4 0 0 0-3-3.87',
            'M16 3.13a4 4 0 0 1 0 7.75',
        ],
        'upload' => ['M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4', 'M17 8l-5-5-5 5', 'M12 3v12'],
        'submissions' => ['M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4', 'M17 8l-5-5-5 5', 'M12 3v12'],
        'clock' => ['M12 6v6l4 2', 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'late' => ['M12 6v6l4 2', 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'alert' => [
            'M12 9v4',
            'M12 17h.01',
            'M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z',
        ],
        'deadline' => [
            'M12 9v4',
            'M12 17h.01',
            'M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z',
        ],
        'check-circle' => ['m9 12 2 2 4-4', 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'done' => ['m9 12 2 2 4-4', 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'pending' => ['M12 6v6h4', 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        'calendar' => ['M8 2v4M16 2v4M3 10h18', 'M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z'],
        'file-text' => ['M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z', 'M14 2v6h6', 'M8 13h8', 'M8 17h5'],
        'chart-line' => ['M3 3v18h18', 'm7 15 4-4 3 3 5-6'],
    ];

    $paths = $icons[$name] ?? $icons['list'];
@endphp

<svg {{ $attributes->merge(['class' => 'size-5']) }} xmlns="http://www.w3.org/2000/svg" fill="none"
    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    aria-hidden="true">
    @foreach ($paths as $path)
        <path d="{{ $path }}" />
    @endforeach
</svg>
