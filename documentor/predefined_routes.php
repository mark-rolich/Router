<?php
return array(
    'general' => array(
        'route'     => '(/controller)(/action(.format<[a-z]{2,4}>))(/#id)(/slug<[A-Za-z0-9\-]+>)',
        'defaults'  => array(
            'controller' => 'index',
            'action' => 'index',
            'format' => 'html',
            'id' => 1,
            'slug' => 'default-slug'
        )
    ),
    'controller-action-id' => array(
        'route'     => '/controller/action/#id'
    ),
    'controller-action' => array(
        'route'     => '/controller/action'
    )
);
?>