<?php

use Kirby\Cms\App;



/**
 * Series Events Plugin for Kirby.
 *
 * This plugin provides a custom page method and a custom section for Kirby.
 * The page method 'seriesEvents' filters the children of the 'schedule' page by the 'series' field of the event.
 * The custom section 'series-events' provides a computed property 'events' that maps the filtered events to a specific format.
 *
 */
Kirby::plugin('chuntfm/series-events', [
    'pageMethods' => [
        'seriesEvents' => function () {
            $schedulePage = App::instance()->site()->page('schedule');
            if ($schedulePage) {
                $filteredEvents = $schedulePage->children()->filter(function ($event) {
                    $series = $event->content()->get('series');
                    return in_array((string) $this->uuid(), $series->split());
                });

                return $filteredEvents;
            }
            return [];
        }
    ],
    'sections' => [
        'series-events' => [
            'computed' => [
                'events' =>   function () {
                    $seriesEvents = $this->model()->seriesEvents();
                    $kirby = App::instance();

                    $seriesEvents = $seriesEvents->map(function ($event) use ($kirby) {
                        return [
                            'id' => $event->id(),
                            'text' => $event->title()->value(),
                            'link' => 'pages/schedule+' . $event->slug(),
                            'info' => $event->starttimestamputc()->toString(),
                            ];
                    })->values();

                    return $seriesEvents;
                }
            ],
            'props' => [
                'label' => function (string $label) {
                    return $label;
                },
                'layout' => function (string $layout = 'list') {
                    return $layout;
                },
            ],
        ]
    ]
]);


?>
