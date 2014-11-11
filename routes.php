<?php return array(
  'general' => array(
    'route' => '(/controller)(/action(.format<[a-z]{2,4}>))(/#id)(/slug<[A-Za-z0-9\-]+>)',
    'defaults' =>   array(
      'controller' => 'index',
      'action' => 'index',
      'format' => 'html',
      'id' => 1,
      'slug' => 'default-slug',
    ),
    'regex' => '/(?:\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))?(?:\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?:\.(?P<format>[a-z]{2,4}))?)?(?:\/(?P<id>[0-9]+))?(?:\/(?P<slug>[A-Za-z0-9\-]+))?/'
  ),
  'controller-action-id' => array(
    'route' => '/controller/action/#id',
    'regex' => '/\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<id>[0-9]+)/'
  ),
  'controller-action' => array(
    'route' => '/controller/action',
    'regex' => '/\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/'
  )
); ; ?>