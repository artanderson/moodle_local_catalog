<?php

defined('MOODLE_INTERNAL') || die;

if($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_catalog_settings', new lang_string('pluginname', 'local_catalog')));
    $settingspage = new admin_settingpage('managelocalcatalog', new lang_string('manage', 'local_catalog'));

	if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configcheckbox(
            'local_catalog/showinnavigation',
            new lang_string('showinnavigation', 'local_catalog'),
            new lang_string('showinnavigation_desc', 'local_catalog'),
            1
        ));
		$settingspage->add(new admin_setting_configtext(
			'local_catalog/coursesperpage',
			new lang_string('coursesperpage', 'local_catalog'),
            new lang_string('coursesperpage_desc', 'local_catalog'),
            '5',
			PARAM_INT
		));
    }

    $ADMIN->add('localplugins', $settingspage);
}