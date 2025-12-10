<?php

namespace local_catalog\local;

use core\hook\navigation\primary_extend;
use core\navigation\navigation_node;

class hook_callbacks {
    public static function catalog_primary_extend(primary_extend $navigation) {
        $primary = $navigation->get_primaryview();
        $beforekey = $primary->get('siteadminnode') ? 'siteadminnode' : null;
        $primary->add_node(
            new navigation_node([
                'text'=>'Catalog',
                'shorttext'=>'Catalog',
                'key'=> 'catalog',
                'action'=>new \moodle_url('/local/catalog/index.php'),
                'type'=> navigation_node::TYPE_ROOTNODE
            ]),
            $beforekey
        );
    }
}