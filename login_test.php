<?php

require_once(__DIR__ . '/../../config.php');

// Check if user is as site config roles
require_login();
$context = context_system::instance();
$PAGE->set_context($context);
require_capability("moodle/site:config", $context);

$pageurl = new \moodle_url('/local/send_grade/login_test.php');
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('testloginhead', 'local_send_grade'));
$PAGE->set_heading(get_string('testloginhead', 'local_send_grade'));

echo $OUTPUT->header();

$token = local_send_grade\quizgrade_observer::getToken();


if ($token !== null) {
    echo("<div class=\"alert alert-success p-5\" role=\"alert\"> External API settings worked </div>");
} else {
    echo("<div class=\"alert alert-danger p-5\" role=\"alert\"> External API settings did not work</div>");
}
echo $OUTPUT->footer();
