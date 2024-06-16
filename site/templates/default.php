<?php

/** @var \Kirby\Cms\Page $page */



$data = $page->toArray();

$data['__meta__'] = [
  'template' => $page->intendedTemplate()->name(),
  'isHomePage' => $page->isHomePage(),
  'isErrorPage' => $page->isErrorPage()
];

$data['title'] = $page->title()->value();
$data['uuid'] = $page->uuid()->id();



echo \Kirby\Data\Json::encode($data);
