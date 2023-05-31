<?php

add_action('init', function() {

  add_role('yeppi_manager', 'Yeppi Manager', array(
    'read' => true
  ));
});