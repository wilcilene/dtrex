<?php
/**
 * Plugin Name: Braco Robotico Controller
 */

register_activation_hook( __FILE__, 'create_table' );
register_deactivation_hook( __FILE__, 'braco_robotico_remove_table' );
function braco_robotico_remove_table() {
     global $wpdb;
     $table_name = $wpdb->prefix . 'braco_robotico';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("my_plugin_db_version");
} 

function braco_robotico( $atts ) {
    $data = get_braco_data();

    if ( empty( $data ) == 0 ) {
        $data = [
            "id"        => 90,
            "estudante" => 999999,
            "base"      => 90,
            "waist"     => 90,
            "cotovelo"  => 90,
            "micro1"    => 90,
            "pulso"     => 90,
            "garra"     => 50,
            "fisico"    => 0
        ];
    } else {
        $data = json_decode( $data, 1 );
    }

    error_log( print_r( "FRONT", 1 ) );
    error_log( print_r( $data, 1 ) );

    ?>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script>
    // $(document).ready(function(){
    //     $(":input").bind('keyup mouseup', function () {
    //         $.post("http://robo.local:10003/wp-json/braco-robo/v1/robo/", $('form').serialize() ,function(data){
    //             console.log(data);
    //         });          
    //     });
    // });
    </script>
    <form method="POST" action="http://robo.local:10003/wp-json/braco-robo/v1/robo/">
    <?
    foreach( $data as $nome_campo => $valor ) {
        if ( $nome_campo === 'id' ) {
            ?>
            <input type="hidden" name="id" id="id" value="90">
            <?
            continue;
        }

        if ( $nome_campo === 'estudante' ) {
            ?>
            <input type="hidden" name="estudante" id="estudante" value="999999">
            <?
            continue;
        }

        if ( $nome_campo === 'fisico' ) {
            ?>
                <label for="<? echo $nome_campo ?>"><? echo $nome_campo ?></label>
                <br/>
                <input type="number" step=1 id="<? echo $nome_campo ?>" name="<? echo $nome_campo ?>" required size="2" maxlength=2 value="<? echo $valor?>">
                <br/><br/>
            <?

            continue;         
        }

        ?>
            <label for="<? echo $nome_campo ?>"><? echo $nome_campo ?></label>
            <br/>
            <input type="number" step=5 id="<? echo $nome_campo ?>" name="<? echo $nome_campo ?>" required size="2" maxlength=2 value="<? echo $valor?>">
            <br/><br/>
        <?
    }
    ?>
    <input type='submit' value="Enviar">
    </form>
    <?
}

add_shortcode('braco', "braco_robotico");

function get_braco_data() {
    global $wpdb;
    $table = $wpdb->prefix . "braco_robotico";
    $result = $wpdb->get_results("SELECT * FROM $table WHERE id=90");

    error_log( print_r( "RESULT", 1 ) );
    error_log( print_r( $result, 1 ) );

    $result = sizeof( $result ) > 0 ? $result[0] : array();

    return wp_json_encode( $result );
}

function my_put( $data ) {
    global $wpdb;
    $table = $wpdb->prefix . "braco_robotico";

    $data_db = json_decode( get_braco_data(), 1 );
    error_log( print_r( "DATA_DB", 1 ) );
    error_log( print_r( $data_db, 1 ) );

    error_log( print_r( "BRACO-DATA", 1 ) );
    error_log( print_r( $data, 1 ) );

    $response = '';

    $insert_values = [];

    $data = $data->get_params();

    error_log( print_r( "PARAMS", 1 ) );
    error_log( print_r( $data, 1 ) );

    if ( !empty($data_db) ) {
        foreach( $data_db as $nome_campo => $valor ) {
            $valor = $data[$nome_campo];
            if ($nome_campo == 'id') {
                $valor = '90';
            }
            $insert_values[$nome_campo] = "'".strval($valor)."'";
            error_log( print_r( "INSERT", 1 ) );
            error_log( print_r( "UPDATE {$table} SET {$nome_campo}='{$valor}' WHERE id=90", 1 ) );
            $response = $wpdb->query("UPDATE {$table} SET {$nome_campo}='{$valor}' WHERE id=90");
        }
    }

    $insert_values = $data;
    $insert_values = array_map('strval', $insert_values);
    $insert_values = preg_replace( ['/^/','/$/'], "'", $insert_values);

    $insert_values['id'] = 0;
    $insert_values_string = implode( ',', $insert_values );

    $response = $wpdb->query( "INSERT INTO {$table} VALUES ({$insert_values_string}) " );

    error_log( print_r( "ERRO1::::", 1 ) );
    error_log( print_r( $data_db, 1 ));

    if (sizeof($data_db) == 0) {
        $insert_values['id'] = 90;
        $insert_values_string = implode( ',', $insert_values );
    
        $response = $wpdb->query( "INSERT INTO {$table} VALUES ({$insert_values_string}) " );
    }

    wp_redirect( "http://robo.local:10003/robo/" );
}

function my_insert( $data ) {
    global $wpdb;
    $table = $wpdb->prefix . "braco_robotico";

    $response = '';
    error_log( print_r( "DATA::::", 1 ) );
    error_log( print_r( $data->get_params(), 1 ) );

    $insert_values = [];

    foreach( $data->get_params() as $nome_campo => $valor ) {
        $valor = $data[$nome_campo];
        $insert_values[] = $valor;
        error_log( print_r( "DATA::::DATA", 1 ) );
        error_log( print_r( $insert_values, 1 ) );
    }

    $insert_values_string = implode( ',', $insert_values );
    $insert_values_string = str_replace(',,', ',', $insert_values_string);

    $response = $wpdb->query( "INSERT INTO {$table} VALUES ({$insert_values_string}) " );

    error_log( print_r( "ERRO-INSERT::::", 1 ) );
    error_log( print_r( $insert_values_string, 1 ));

    foreach( json_decode( $data_db, 1 ) as $nome_campo => $valor ) {
        $valor = $data[$nome_campo];
        $insert_values[$nome_campo] = $valor;
        $response = $wpdb->query("UPDATE {$table} SET {$nome_campo}={$valor} WHERE id=90");
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'braco-robo/v1', '/robo/',
        array(
            array(
                'methods' => 'GET',
                'callback' => 'get_braco_data',
            ),
            array(
                'methods' => 'POST',
                'callback' => 'my_put',
                'permission_callback' => '__return_true',
                'args' => [
                    'id',
                    'waist',
                ]
            ),
        ) 
    );
    register_rest_route( 'braco-robo/v1', '/robo/externo/',
    array(
        array(
            'methods' => 'POST',
            'callback' => 'my_insert',
            'permission_callback' => '__return_true',
            'args' => [
                'id',
                'student',
            ]
        ),
    ) 
);
} );

function create_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . "braco_robotico";

    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      estudante VARCHAR(20)  NOT NULL,
      base int NOT NULL,
      waist int NOT NULL,
      cotovelo int NOT NULL,
      micro1 int NOT NULL,
      pulso int NOT NULL,
      garra int NOT NULL,
      fisico boolean DEFAULT false,
      PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}