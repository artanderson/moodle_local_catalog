<?php

require_once "../../config.php";

require_login();

$page = optional_param("page",null, PARAM_INT);
$categoryid = optional_param('categoryid',null, PARAM_INT);

$data = new stdClass();

if($categoryid != null) {
    try {
        $data->category = core_course_category::get($categoryid, MUST_EXIST);
    } 
    catch (moodle_exception $e) {
        $data->haserror = true;
        $data->error = new stdClass();
        $data->error->message = $e->getMessage();
        $data->error->code = $e->errorcode;
    }
}
elseif($page != null) {
    $page = null;
}

$data->page = $page;

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('base');
$PAGE->add_body_classes(['fullwidth', 'noheader', 'centered-main', 'page-catalog']);
$PAGE->set_primary_active_tab('catalog');
$PAGE->set_url(new moodle_url('/local/catalog/index.php', ['categoryid'=>$categoryid]));

if(isset($data->haserror) && $data->haserror) {
    $site = get_site();
    
    $PAGE->set_title('Error');
    $PAGE->set_heading($site->fullname);

    $data->heading = $site->fullname;
}
elseif(isset($data->category)) {
    $PAGE->set_title($data->category->name);
    $PAGE->set_heading($data->category->name);
    
    $data->ismain = true;
}
else {
    $PAGE->set_title(get_string('pluginname', 'local_catalog'));
    $PAGE->set_heading(get_string('catalogheading', 'local_catalog'));
}

echo $OUTPUT->header();

$main = new \local_catalog\output\main($data);
echo $PAGE->get_renderer('local_catalog')->render($main);

echo $OUTPUT->footer();
