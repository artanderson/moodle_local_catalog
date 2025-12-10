<?php

require_once "../../config.php";

require_login();

$page = optional_param("page",null, PARAM_INT);
$query = optional_param('q', null, PARAM_RAW);
$query = trim(strip_tags($query));

$data = new stdClass();
$data->page = $page;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('base');
$PAGE->add_body_classes(['fullwidth', 'noheader', 'centered-main', 'page-catalog', 'page-catalog-search']);
$PAGE->set_primary_active_tab('catalog');
$PAGE->set_url('/local/catalog/search.php', ['q'=>$query, 'page'=>$page]);
$PAGE->set_title('Search');

if($query != null) {
    $PAGE->set_title("Search Results for '$query'");
    $PAGE->set_heading("Search Results for '$query'");
    
    $data->query = $query;

    $eventparams = ['context' => $PAGE->context, 'other' => ['query' => $query]];
    $event = \core\event\courses_searched::create($eventparams);
    $event->trigger();
}

echo $OUTPUT->header();

$main = new \local_catalog\output\main($data);
echo $PAGE->get_renderer('local_catalog')->render($main);

echo $OUTPUT->footer();