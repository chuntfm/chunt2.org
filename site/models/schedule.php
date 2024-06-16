<?php

class SchedulePage extends Page
{
    static $subpages = null;

    public function subpages()
    {
        if (static::$subpages) {
            return static::$subpages;
        }

        return static::$subpages = Pages::factory($this->inventory()['children'], $this);
    }

    public function children(): \Kirby\Cms\Pages
    {
        if ($this->children instanceof Pages) {
            return $this->children;
        }


        // read in json schedule from web
        $schedule = json_decode(file_get_contents(kirby()->option('calScheduleUrl')), true);

        $children = array_map(function ($event) {

            $uid = Str::slug($event['uid']);
            $page = $this->subpages()->find($uid);

            // create a new event array
            $event_new = array();

            $event_new['num'] = strtotime($event['startTimestampUTC']);

            $event_new['content'] = array();

            $event_new['title'] = $event['title'];

            // move the properties of $event to $event_new->content()
            $event_new['content'] = $event;

            $event_new['slug'] = $uid;

            $event_new['template'] = 'event';
            $event_new['model'] = 'event';

            $doNotChange = array('uid', 'title', 'startTimestampUTC', 'endTimestampUTC', 'slug');


            // merge the content of the event with the page overwriting the schedule event with the page content if they are not in doNotChange array
            if ($page) {
                $pageContent = $page->content()->toArray();
                foreach ($pageContent as $key => $value) {
                    if (!in_array($key, $doNotChange)) {
                        $event_new['content'][$key] = $value;
                    }
                }
            }

            $event_new['content']['uuid'] = $uid;

            // if no creatormail is set, set it to host@host
            if (!isset($event_new['content']['creatormail'])){

                $event_new['content']['creatormail'] = 'host@host.com';

            }

            if (isset($event_new['content']['event'])) {
                $event_new['content']['event_str'] = implode(' ', $event_new['content']['event']);
            }


            return $event_new;


        }, $schedule);

        return $this->children = Pages::factory($children, $this);
    }

    public function grouped($returnContent = true)
    {
        $children = $this->children();

        # sort into sections of previous, current, and upcoming events
        $childrenSection = array(
            'previous' => array(),
            'current' => array(),
            'upcoming' => array()
        );

        $now = time();

        foreach ($children as $child) {

            $childStart = $child->startDateTime();
            $childEnd = $child->endDateTime();

            $now = new DateTime(); // get the current date and time
            $now->setTimezone(new DateTimeZone('UTC')); // set the timezone to UTC

            if ($childStart < $now && $childEnd < $now) {
                $childrenSection['previous'][] = $child;
            } elseif ($childStart < $now && $childEnd > $now) {
                $childrenSection['current'][] = $child;
            } elseif ($childStart > $now && $childEnd > $now) {
                $childrenSection['upcoming'][] = $child;
            }
        }

        // sort previous descending by starttimestamputc
        usort($childrenSection['previous'], function ($a, $b) {
            $contentA = $a->content()->toArray();
            $contentB = $b->content()->toArray();
            $dateA = isset($contentA['starttimestamputc']) ? strtotime($contentA['starttimestamputc']) : 0;
            $dateB = isset($contentB['starttimestamputc']) ? strtotime($contentB['starttimestamputc']) : 0;
            return $dateB <=> $dateA;
        });

        // sort upcoming ascending by starttimestamputc
        usort($childrenSection['upcoming'], function ($a, $b) {
            $contentA = $a->content()->toArray();
            $contentB = $b->content()->toArray();
            $dateA = isset($contentA['starttimestamputc']) ? strtotime($contentA['starttimestamputc']) : 0;
            $dateB = isset($contentB['starttimestamputc']) ? strtotime($contentB['starttimestamputc']) : 0;
            return $dateA <=> $dateB;
        });

        // sort current ascending by starttimestamputc
        usort($childrenSection['current'], function ($a, $b) {
            $contentA = $a->content()->toArray();
            $contentB = $b->content()->toArray();
            $dateA = isset($contentA['starttimestamputc']) ? strtotime($contentA['starttimestamputc']) : 0;
            $dateB = isset($contentB['starttimestamputc']) ? strtotime($contentB['starttimestamputc']) : 0;
            return $dateA <=> $dateB;
        });

        if ($returnContent) {
            // iterate over each section and return the content
            foreach ($childrenSection as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    $childrenSection[$key][$key2] = $value2->content()->toArray();
                }
            }
        }

        return $childrenSection;
    }


}

?>
