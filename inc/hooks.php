<?php
date_default_timezone_set('America/Lima');

/* hook de registro de cliente */
add_action('user_register', 'hpUser_register', 10, 3);
function hpUser_register($user_id)
{
     global $wpdb;
     $fecha_actual = date("Y-m-d H:i:s");
     $sql = "INSERT INTO wp_userssap (user_id,cod,date_created) VALUES ($user_id,0,%s)";
     $wpdb->query($wpdb->prepare($sql, $fecha_actual));
     $wpdb->flush();
};

// hook de actualizacion de clientes
add_action('profile_update', 'hpUser_update', 10, 3);
function hpUser_update($user_id, $old_user_data)
{
     global $wpdb;
     $sql = "UPDATE wp_userssap SET cod = 1 WHERE user_id =$user_id";
     $wpdb->query($sql);
     $wpdb->flush();
}

//opcionalmente si se elimina usuario
add_action('delete_user', 'hpDelete_user');
function hpDelete_user($user_id)
{
     global $wpdb;
     $sql = "DELETE FROM wp_userssap WHERE user_id = $user_id";
     $wpdb->query($sql);
     $wpdb->flush();
     $sql = "DELETE FROM wp_prflxtrflds_user_field_data WHERE user_id = $user_id";
     $wpdb->query($sql);
     $wpdb->flush();
}
add_action('woocommerce_new_order', 'hpWooNewOrder');
// add_action('woocommerce_resume_order', 'hpWooNewOrder');
function hpWooNewOrder($id_order)
{
     global $wpdb;
     $fecha_actual = date("Y-m-d H:i:s");
     $order =  wc_get_order($id_order);
     $cod = 2;
     // si es una cotizacion
     if (strval($order->payment_method) ==  "yith-request-a-quote") {
          $cod = 0;
     } else {
          $cod = 1;
     }
     $sql = "INSERT INTO wp_cotizaciones (id_order,customer_id,cod,date_created) VALUES ($order->id,$order->customer_id,$cod,%s)";
     $wpdb->query($wpdb->prepare($sql, $fecha_actual));
}

add_action('woocommerce_delete_order', 'hpWooDeleteOrder');
function hpWooDeleteOrder($id_order)
{
     global $wpdb;
     $sql = "DELETE FROM wp_cotizaciones WHERE id_order = $id_order";
     $wpdb->query($wpdb->prepare($sql));
}
// agrega la acción 
add_action('woocommerce_order_status_changed', 'action_order_status_changed_hook_punkuhr', 10, 4);
// define la devolución de llamada woocommerce_order_status_changed 
function action_order_status_changed_hook_punkuhr($id_order)
{
     global $wpdb;
     $fecha_actual = date("Y-m-d H:i:s");
     $sql = "UPDATE wp_cotizaciones SET date_created=%s WHERE id_order =$id_order";
     $wpdb->query($wpdb->prepare($sql, $fecha_actual));
     $wpdb->flush();
     // haz que la acción mágica suceda aquí ...

};

