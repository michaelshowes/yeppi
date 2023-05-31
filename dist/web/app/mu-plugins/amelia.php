<?php

// Services
function get_amelia_services() {
  global $wpdb;
  $row = $wpdb->get_results("SELECT * FROM wp_amelia_services");
  return $row;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'amelia/v1', 'services', array(
    'methods' => 'GET',
    'callback' => 'get_amelia_services',
    'permission_callback' => '__return_true'
  ));
});

// categories
function get_amelia_categories() {
  global $wpdb;
  $row = $wpdb->get_results("SELECT * FROM wp_amelia_categories");
  return $row;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'amelia/v1', 'categories', array(
    'methods' => 'GET',
    'callback' => 'get_amelia_categories',
    'permission_callback' => '__return_true'
  ));
});

// Users
function get_amelia_staff() {
  global $wpdb;
  $row = $wpdb->get_results("SELECT * FROM wp_amelia_users");
  return $row;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'amelia/v1', 'staff', array(
    'methods' => 'GET',
    'callback' => 'get_amelia_staff',
    'permission_callback' => '__return_true'
  ));
});
