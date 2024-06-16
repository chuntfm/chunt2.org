<?php


use Kirby\Toolkit\Collection;


/**
 * Custom search component for SchedulePage.
 *
 * This component overrides the native search component of Kirby.
 * It performs the search operation on a collection of SchedulePage but removes the event key first from the keys to search.
 * This is because the event key is a nested array and the native search component does not support nested arrays.
 * If the collection is not a SchedulePage, it falls back to the native search component.
 *
 * @param Kirby $kirby The Kirby application instance.
 * @param Collection $collection The collection to search in.
 * @param string $query The search query.
 * @param array $params Additional parameters for the search.
 * @return mixed The search results.
 */
Kirby::plugin('chuntfm/schedule-search', [
    'components' => [
        'search' => function (Kirby $kirby, Collection $collection, string $query = null, $params = []) {

             // if the collection is not a SchedulePage, use the native search component
            if (!is_a($collection->parent(), 'SchedulePage'))
            {
                return $kirby->nativeComponent('search')($kirby, $collection, $query, $params);
            }

            // get all keys of the first child in the collection
            $keys = array_keys($collection->first()->content()->toArray());

            // remove the event key
            $keys = array_diff($keys, ['event']);

            // use the native component for other searches (users, files...)
            return $kirby->nativeComponent('search')($kirby, $collection, $query, $params=[
                'fields' => $keys
                ]);
        }
    ]
]);
