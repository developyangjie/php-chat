<?php
return array (
  'app' => 'Admin',
  'model' => 'Game',
  'action' => 'default',
  'data' => '',
  'type' => '0',
  'status' => '1',
  'name' => '用户管理',
  'icon' => '',
  'remark' => 'Gm用户管理',
  'listorder' => '50',
  'children' => 
  array (
    array (
      'app' => 'Game',
      'model' => 'User',
      'action' => 'batch_send_props',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '批量发道具',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
  ),
);