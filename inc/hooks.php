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
//cambio el nombre del archivo que llega al correo
add_filter('wpo_wcpdf_filename', 'wpo_wcpdf_custom_filename', 10, 4);
function wpo_wcpdf_custom_filename($filename, $template_type, $order_ids, $context)
{
     // prepend your shopname to the file
     $invoice_string = _n('invoice', 'invoices', count($order_ids), 'woocommerce-pdf-invoices-packing-slips');
     $new_prefix = "Pedido-";
     $new_filename = str_replace($invoice_string, $new_prefix, $filename);

     return $new_filename;
}

// cambia el texto del boton Ver Factura
add_filter('wpo_wcpdf_myaccount_button_text', 'wpo_wcpdf_myaccount_button_text', 10, 1);
function wpo_wcpdf_myaccount_button_text($button_text)
{
     return 'Ver PDF'; // your preferred button text
}



// Agregar Contenido debajo la tabla de productos 
add_filter('woocommerce_review_order_before_cart_contents', 'action_woocommerce_review_order_before_cart_contents', 10, 0);

//agrego boton de mostrar productos al checkout
add_action('woocommerce_checkout_order_review', 'hookNewContentInOrderReview', 15);
function hookNewContentInOrderReview()
{

     echo "<button class='button' type='button' style='    border-radius: 1.3rem;
     width: 100%;
     padding: .8rem;
     color: white;
     background: #00396E;
     margin-bottom: 1rem;' id='btnShowModalProducts'>Ver Productos</button>";
     echo "
     <script>
     let modal = document.querySelectorAll('#myModalProducts')[0];
     let btn = document.getElementById('btnShowModalProducts');
     let span = document.getElementsByClassName('closeModalProducts')[0];
     btn.onclick = function() {
          modal.style.display = 'block';
     }
     span.onclick = function() {
          modal.style.display = 'none';
     }
     window.onclick = function(event) {
          if (event.target == modal) {
               modal.style.display = 'none';
          }
     } 
</script>
     ";
}
