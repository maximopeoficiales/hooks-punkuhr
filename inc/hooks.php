<?php
date_default_timezone_set('America/Lima');
// function wmHome()
// {
//      return ["msg" => "fasdfasdf"];
// }

// add_action("rest_api_init", function () {
//      register_rest_route("webhooks-maxco/v1", "/hola", array(
//           "methods" => "GET",
//           "callback" => "wmHome",
//           'args'            => array(),
//      ));
// });


add_action('woocommerce_new_order', 'hpWooNewOrder');
function hpWooNewOrder($id_order)
{
     global $wpdb;
     $fecha_actual = date("Y-m-d H:i:s");
     $order =  wc_get_order($id_order);
     // si es una cotizacion
     if ($order->status ==  "ywraq-new") {
          $sql = "INSERT INTO wp_cotizaciones (id_order,cod,date_created) VALUES ($order->id,0,%s)";
          $wpdb->query($wpdb->prepare($sql, $fecha_actual));
     }
}
