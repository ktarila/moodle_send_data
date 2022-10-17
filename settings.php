<?php


if ($hassiteconfig) {

    // Create the new settings page
    // - in a local plugin this is not defined as standard, so normal $settings->methods will throw an error as
    // $settings will be NULL
    $settings = new admin_settingpage('local_send_grade', 'Send Grade Settings');

    // Create
    $ADMIN->add('localplugins', $settings);

    $link = new \moodle_url('/local/send_grade/login_test.php');

    $managelink = \html_writer::link(
        $link,
        get_string('testlogin', 'local_send_grade'),
        ['class' => 'btn btn-warning', 'target' => '_blank']
    );

    $settings->add(new admin_setting_heading('local_send_grade/testlogin', get_string('warning_text_click', 'local_send_grade'), $managelink));

    // Add a setting field to the settings for this page
    $settings->add(new admin_setting_configtext(

        // This is the reference you will use to your configuration
        'local_send_grade/login',

        // This is the friendly title for the config, which will be displayed
        'External API: Login URL',

        // This is helper text for this config field
        'This is the url of the external api to get login JSON Web Token',

        // This is the default value
        'No URL Defined',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));

    // Add a setting field to the settings for this page
    $settings->add(new admin_setting_configtext(

        // This is the reference you will use to your configuration
        'local_send_grade/url',

        // This is the friendly title for the config, which will be displayed
        'External API: URL',

        // This is helper text for this config field
        'This is the url of the external api to send grades to',

        // This is the default value
        'No URL Defined',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(

        // This is the reference you will use to your configuration
        'local_send_grade/username',

        // This is the friendly title for the config, which will be displayed
        'External API Login: Username',

        // This is helper text for this config field
        'This is the username that will be used to login to the api',

        // This is the default value
        'No Username Defined',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configpasswordunmask(

        // This is the reference you will use to your configuration
        'local_send_grade/password',

        // This is the friendly title for the config, which will be displayed
        'External API Pogin: password',

        // This is helper text for this config field
        'This is the user password that will be used to login to the api',

        // This is the default value
        'No Password Defined',

        // This is the type of Parameter this config is
        PARAM_TEXT
    ));
}
