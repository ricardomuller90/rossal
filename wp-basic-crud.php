<?php
/*
* Plugin Name: WP Basic Crud 
* Description: This plugin to create custom contact list-tables from database using WP_List_Table class.
* Version:     1.0.1
* Author:      Labarta
* Author URI:  https://labarta.es/
* License:     GPL2
* License URI: https://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: wpbc
* Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( '¡Sin trampas!' );

add_action( 'plugins_loaded', 'wpbc_plugin_load_textdomain' );

function wpbc_plugin_load_textdomain() {
load_plugin_textdomain( 'wpbc', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

global $wpbc_db_version;
$wpbc_db_version = '1.0.1'; 


function wpbc_install()
{
    global $wpdb;
    global $wpbc_db_version;

    $table_name = $wpdb->prefix . 'cte'; 


    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) NOT NULL AUTO_INCREMENT,
      name VARCHAR (50) NOT NULL,
      lastname VARCHAR (100) NOT NULL,
      email VARCHAR(100) NOT NULL,
      phone int(12) NULL,
      address VARCHAR (250) NULL,
      notes VARCHAR (250) NULL,
      PRIMARY KEY  (id)
    );";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option('wpbc_db_version', $wpbc_db_version);

    $installed_ver = get_option('wpbc_db_version');
    if ($installed_ver != $wpbc_db_version) {
        $sql = "CREATE TABLE " . $table_name . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          name VARCHAR (50) NOT NULL,
          lastname VARCHAR (100) NOT NULL,
          email VARCHAR(100) NOT NULL,
          phone int(12) NULL,
          address VARCHAR (250) NULL,
          notes VARCHAR (250) NULL,
          PRIMARY KEY  (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('wpbc_db_version', $wpbc_db_version);
    }
}

register_activation_hook(__FILE__, 'wpbc_install');






function wpbc_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cte'; 

}

register_activation_hook(__FILE__, 'wpbc_install_data');


function wpbc_update_db_check()
{
    global $wpbc_db_version;
    if (get_site_option('wpbc_db_version') != $wpbc_db_version) {
        wpbc_install();
    }
}

add_action('plugins_loaded', 'wpbc_update_db_check');



if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class Custom_Table_Example_List_Table extends WP_List_Table
 { 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'curso',
            'plural' => 'cursos',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_phone($item)
    {
        return '<em>' . $item['phone'] . '</em>';
    }

    function column_noSite($item)
    {


    		switch ($item['noSite']) {
    case '1':
       return 'SIM';
        break;
        case '2':
       return 'NÃO';
        break;

        
    }}


    function column_data($item)
    {


   $item['data'] = str_replace("-", "/", $item['data']);
    $item['data'] = date('d/m/Y', strtotime($item['data']));

    return $item['data'];
	}


    function column_cidade($item)
    {


$db = new mysqli('localhost', 'franci80_wp742', 'Fr33S4nm4r1n0', 'franci80_wp742');
if(mysqli_connect_errno()){
echo mysqli_connect_error();
}

$result2 = $db->query('SELECT * FROM `cidade` WHERE id = "'.$item['cidade'].'"');
if($result2){
   while ($row2 = $result2->fetch_assoc()){
     return $row2['nome'];
   }
   $result2->free();
}





    }


   



    function column_nome($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=contacts_form&idC=%s">%s</a>', $item['idC'], __('Editar', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=delete&idC=%s">%s</a>', $_REQUEST['page'], $item['idC'], __('Apagar', 'wpbc')),
            'listaInscritos' => sprintf('<a href="?page=listaInscritos&idC=%s&nome=%s&data=%s">%s</a>', $item['idC'], $item['nome'], $item['data'],__('Ver inscritos', 'wpbc')),
        );

        return sprintf('%s %s',
            $item['nome'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="idC[]" value="%s" />',
            $item['idC']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'data' => __('Data', 'wpbc'),
            'nome' => __('Nome', 'wpbc'),
            'cidade' => __('Cidade', 'wpbc'),
            'noSite' => __('Disponível no site', 'wpbc'),
            

            
      
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
        	'data' => array('data', true),
            'nome' => array('nome', true),
            'cidade' => array('cidade', true),
            'valor' => array('valor', false),
            'noSite' => array('noSite', false),
            
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cte'; 

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['idC']) ? $_REQUEST['idC'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE idC IN($ids)");
            }
        }
    }


function prepare_items2()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'inscrito'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'lastname';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }












    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cte'; 

        $per_page = 20; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();


        $total_items = $wpdb->get_var("SELECT COUNT(idC) FROM $table_name");


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'data';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);




        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}







































//inicio classe 2



class Custom_Table_Example_List_Table2 extends WP_List_Table
 { 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'contact',
            'plural' => 'contacts',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_phone($item)
    {
        return '<em>' . $item['phone'] . '</em>';
    }
	
	function column_comprovante($item)
    {   
		if(is_null($item['comprovante'])){}else{
        return '<a href="http://franciscorossal.com.br/dev/uploads/' . $item['comprovante'] . '"><img src="http://franciscorossal.com.br/dev/img/doc.png" width="20"></a>';
		}
	}


     function column_pago($item)
    {

             
            switch ($item['pago']) {
    case '1':
      return '<em>Aguardando pagamento</em>';
        break;
    case '2':
         return '<em>Pagamento em análise</em>';
        break;
     case '3':
         return '<em>Pagamento efetuado</em>';
        break;
     case '4':
         return '<em>Pagamento liberado</em>';
        break;
        case '5':
         return '<em>Pagamento em disputa</em>';
        break;
        case '6':
         return '<em>Devolvido</em>';
        break;
        case '7':
         return '<em>Cancelado</em>';
        break;
   default:
               return '<em>Erro...</em>';
        break;
} 

        
    }

 

    function column_nome($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=contacts_form2&id=%s&idC=%s">%s</a>', $item['id'], $item['idCurso'], __('Editar', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s&idC='.$_GET['idC'].'&nome='.$_GET['nome'].'&data='.$_GET['data'].'">%s</a>', $_REQUEST['page'], $item['id'], __('Deletar', 'wpbc')),
            

        );

        return sprintf('%s %s',
            strtoupper($item['nome']),
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'nome' => __('Nome', 'wpbc'),            
            'celular' => __('Celular', 'wpbc'),
            'cpf' => __('CPF', 'wpbc'),
            'email' => __('Email', 'wpbc'),
            'pago' => __('Pagamento', 'wpbc'),
			'valor' => __('Valor', 'wpbc'),
			'comprovante' => __('Comprovante', 'wpbc'),
			
           
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'nome' => array('nome', true),
            'email' => array('email', true),
            'celular' => array('celular', false),
            'pago' => array('pago', false),
         );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
			'export_mail' => 'Exportar emails selecionados',
            'export_list' => 'Exportar para Excel',
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'inscrito'; 

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
		








		
		if ('export_list' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {

                /*$wpdb->query("SELECT email FROM $table_name WHERE id IN($ids)");*/
				
	$fivesdrafts = $wpdb->get_results( 
	"
	SELECT * FROM $table_name WHERE id IN($ids)
	"
);
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js">
 
</script>


<form action="../dev/xls.php" method="post" target="_self" id="form">
    <?php 
    $data = str_replace("-", "/", $_GET['data']);
    $data = date('d/m/Y', strtotime($data));
    $titulo = '<H1>INSCRIÇÕES '.$_GET['nome'].'-'.$data.'</h1>';









//criando conteudo arquivo
//declaramos uma variavel para monstarmos a tabela
    $dadosXls  = "";
    $dadosXls .= "  <table border='1'>";
    $dadosXls .= "          <tr>";
    $dadosXls .= "          <th>Nome</th>";
    $dadosXls .= "          <th>Celular</th>";
    $dadosXls .= "          <th>Email</th>";
    $dadosXls .= "          <th>Pago</th>";
    $dadosXls .= "          <th>Valor</th>";
    $dadosXls .= "      </tr>";


    foreach($fivesdrafts as $valor){
        $dadosXls .= "<tr>";

        $valor->nome = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($valor->nome)));
        $valor->nome = strtoupper($valor->nome);  

        $dadosXls .= "<td>".$valor->nome."</td>";
        $dadosXls .= "<td>".$valor->celular."</td>";

        $valor->email = strtoupper($valor->email);  
        
        $dadosXls .= "<td>".$valor->email."</td>";
        
        
        $pago = $valor->pago;

        switch ($pago) {

      
        case '1':
    $pago = 'Aguardando';
        break;
    case '2':
    $pago = 'Em análise';
        break;
     case '3':
        $pago = 'Aprovado';
        break;
         case '4':
        $pago = 'Liberado';
        break;
             case '5':
        $pago = 'Em disputa';
        break;
             case '6':
        $pago = 'Devolvida';
        break;
             case '7':
        $pago = 'Cancelada';
        break;
        default:
        $pago = '';
        break;
  }

        $dadosXls .= "<td>".$pago."</td>";
        $dadosXls .= "<td>".$valor->valor."</td>";
        $dadosXls .= "</tr>";
    }





    $dadosXls .= "  </table>";
    echo '<input type="hidden" name=titulo value="'.$titulo.'">';
   echo '<input type="hidden" name="corpo" value="'.$dadosXls.'">';

?>




</form>
<script>
    document.getElementById("form").submit();
</script>

<?php exit; ?>

<button onclick="window.open('https://novo.nitronews.com.br/listas/nova', '_blank')">Ir para ferramenta de Email Marketing</button>
<!-- 
<button id="botao">Copiar emails exportados</button>-->



<!--

<script>
 
$("#botao").click(function(){
 
$("#url").select();
 
document.execCommand('copy');
 
}) 

</script>-->
 
		 
				 
			<?php	 
            }
        }
















        
        if ('export_mail' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {

                /*$wpdb->query("SELECT email FROM $table_name WHERE id IN($ids)");*/
                
    $fivesdrafts = $wpdb->get_results( 
    "
    SELECT email FROM $table_name WHERE id IN($ids)
    "
);
?>
<script src="https://code.jquery.com/jquery-3.2.1.min.js">
 
</script>

<form action="../dev/teste.php" method="post" target="_blank" id="form">
    <?php 
    $data = str_replace("-", "/", $_GET['data']);
    $data = date('d/m/Y', strtotime($data));
    $str = 'alunos_'.$_GET['nome'].'-'.$data;
    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
    $str = preg_replace('/[éèêë]/ui', 'e', $str);
    $str = preg_replace('/[íìîï]/ui', 'i', $str);
    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
    $str = preg_replace('/[úùûü]/ui', 'u', $str);
    $str = preg_replace('/[ç]/ui', 'c', $str);
    // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
    $str = preg_replace('/[^a-z0-9]/i', '_', $str);
    $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
    ?>
<input type="hidden" value="<?php echo $str;?>" name="arquivo">
<input type="hidden" id="url" name="url"; value="<?php
foreach ( $fivesdrafts as $fivesdraft ) 
{
    echo $fivesdraft->email; echo ';';
}?>">
</form>
<script>
    document.getElementById("form").submit();
    alert("Arquivo exportado com sucesso. ")
</script>

<button onclick="window.open('https://novo.nitronews.com.br/listas/nova', '_blank')">Ir para ferramenta de Email Marketing</button>
<!-- 
<button id="botao">Copiar emails exportados</button>-->



<!--

<script>
 
$("#botao").click(function(){
 
$("#url").select();
 
document.execCommand('copy');
 
}) 

</script>-->
 
         
                 
            <?php    
            }
        }
















    }














function prepare_items2()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'inscrito'; 

        $per_page = 100; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        //$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE idCurso = %d", $_REQUEST['idC']);

        $total_items = $wpdb->get_var( $wpdb->prepare( 
	"
		SELECT COUNT(id) FROM $table_name WHERE idCurso = %d
	", 
	$_REQUEST['idC']
) );
        


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'nome';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE idCurso = %d ORDER BY $orderby $order LIMIT %d OFFSET %d", $_REQUEST['idC'], $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }












    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'inscrito'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'lastname';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}


// fim classe 2












//inicio classe 3 



class Custom_Table_Example_List_Table3 extends WP_List_Table
 { 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'contact',
            'plural' => 'contacts',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_data($item)
    {
				$item['data'] = str_replace("-", "/", $item['data']);
    			$item['data'] = date('d/m/Y', strtotime($item['data']));

        return '<em>' . $item['data'] . '</em>';
    }






    function column_nome($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=contacts_form3&id=%s&idC=%s">%s</a>', $item['id'], $item['idCurso'], __('Edit', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=deleteP&idC=%s&idP=%s">%s</a>', $_REQUEST['page'], $item['idCurso'], $item['id'],__('Delete', 'wpbc')),
           

        );

        return sprintf('%s %s',
            $item['nome'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
          
            'nome' => __('Nome', 'wpbc'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
          
            'nome' => array('nome', true),
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programas'; 

        if ('deleteP' === $this->current_action()) {
            $ids = isset($_REQUEST['idP']) ? $_REQUEST['idP'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }














function prepare_items3()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programas'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name WHERE idCurso = %d", $_REQUEST['idC']);


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'lastname';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE idCurso = %d ORDER BY $orderby $order LIMIT %d OFFSET %d", $_REQUEST['idC'], $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }












    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'programas'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'lastname';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }
}


// fim classe 3











//inicio classe 4  - valores



class Custom_Table_Example_List_Table4 extends WP_List_Table
 { 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'contact',
            'plural' => 'contacts',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }


    function column_data($item)
    {
				$item['data'] = str_replace("-", "/", $item['data']);
    			$item['data'] = date('d/m/Y', strtotime($item['data']));

        return '<em>' . $item['data'] . '</em>';
    }






    function column_publico($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=contacts_form4&idV=%s&idC=%s">%s</a>', $item['idV'], $item['idCurso'], __('Edit', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=deleteV&idC=%s&idV=%s">%s</a>', $_REQUEST['page'], $item['idCurso'], $item['idV'], __('Delete', 'wpbc')),
           

        );

        return sprintf('%s %s',
            $item['publico'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['idV']
        );
    }
	
	function column_valor($item)
    {
		$item['valor'] = number_format($item['valor'], 2, ',', '.'); // retorna R$100.000,50
      return 'R$ ' . $item['valor'] . '';
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'publico' => __('Público', 'wpbc'),
            'valor' => __('Valor', 'wpbc'),
          
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'publico' => array('publico', true),
           
          
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'valoresCursos'; 

        if ('deleteV' === $this->current_action()) {
            $ids = isset($_REQUEST['idV']) ? $_REQUEST['idV'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE idV IN($ids)");
            }
        }
    }














function prepare_items4()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'valoresCursos'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'idV';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE idCurso = %d ORDER BY $orderby $order LIMIT %d OFFSET %d", $_REQUEST['idC'], $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }

 }



// fim classe 4





















//inicio classe 5  - valores



class Custom_Table_Example_List_Table5 extends WP_List_Table
 { 
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'contact',
            'plural' => 'contacts',
        ));
    }


    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }









    function column_data($item)
    {  $item['data'] = str_replace("-", "/", $item['data']);
                $item['data'] = date('d/m/Y', strtotime($item['data']));

        $actions = array(
            'edit' => sprintf('<a href="?page=contacts_form5&idDH=%s&idC=%s">%s</a>', $item['idDH'], $item['idCurso'], __('Editar', 'wpbc')),
            'delete' => sprintf('<a href="?page=%s&action=deleteDH&idC=%s&idDH=%s">%s</a>', $_REQUEST['page'], $item['idCurso'], $item['idDH'], __('Deletar', 'wpbc')),
           

        );

        return sprintf('%s %s',
            $item['data'],
            $this->row_actions($actions)
        );
    }


    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['idDH']
        );
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', 
            'data' => __('Data', 'wpbc'),
            'hora' => __('Hora inicial', 'wpbc'),
			'horaFinal' => __('Hora final', 'wpbc'),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'data' => array('data', true),
            
          
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'datasHoras'; 

        if ('deleteDH' === $this->current_action()) {
            $ids = isset($_REQUEST['idDH']) ? $_REQUEST['idDH'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE idDH IN($ids)");

                echo '<script> alert("Data e horário excluído.");</script>';
         echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';


            }
        }
    }














function prepare_items5()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'datasHoras'; 

        $per_page = 10; 

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->process_bulk_action();

        $total_items = $wpdb->get_var("SELECT COUNT(idDH) FROM $table_nameWHERE idCurso = %d", $_REQUEST['idC']);


        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'idDH';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';


        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE idCurso = %d ORDER BY $orderby $order LIMIT %d OFFSET %d", $_REQUEST['idC'], $per_page, $paged), ARRAY_A);


        $this->set_pagination_args(array(
            'total_items' => $total_items, 
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page) 
        ));
    }

 }



// fim classe 5





  





function wpbc_admin_menu()
{
    add_menu_page(__('Contacts', 'wpbc'), __('Cursos', 'wpbc'), 'activate_plugins', 'contacts', 'wpbc_contacts_page_handler');
   
    add_submenu_page('contacts', __('', 'wpbc'), __('Cadastrar curso', 'wpbc'), 'activate_plugins', 'novo_contacts_form', 'wpbc_novo_contacts_form_page_handler');
     add_submenu_page('contacts', __('', 'wpbc'), __('Listar cursos', 'wpbc'), 'activate_plugins', 'contacts', 'wpbc_contacts_page_handler');
    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'contacts_form', 'wpbc_contacts_form_page_handler');

    

    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'cria_programa', 'wpbc_cria_programa_page_handler');
	
	 add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'cria_valor', 'wpbc_cria_valor_page_handler');

     add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'cria_dataHora', 'wpbc_cria_dataHora_page_handler');

    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'contacts_form2', 'wpbc_contacts_form2_page_handler');

    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'contacts_form3', 'wpbc_contacts_form3_page_handler');

	 add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'contacts_form4', 'wpbc_contacts_form4_page_handler');

     add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'contacts_form5', 'wpbc_contacts_form5_page_handler');

    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'listaInscritos', 'wpbc_listaInscritos_form_page_handler');

    add_submenu_page('contacts', __('', 'wpbc'), __('', 'wpbc'), 'activate_plugins', 'programasCurso', 'wpbc_programasCurso_form_page_handler');
    

  	



}

add_action('admin_menu', 'wpbc_admin_menu');




















function wpbc_listaInscritos_form_page_handler()
{
    global $wpdb;

    $table2 = new Custom_Table_Example_List_Table2();
    $table2->prepare_items2();

    $message = '';
    if ('delete' === $table2->current_action()) {

 echo '<script> alert("Item excluido");</script>';

       /* $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Itens deletados: %d', 'wpbc'), count($_REQUEST['id'])) . '</p></div>'; */
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <?php $item['data'] = str_replace("-", "/", $item['data']);
                $item['data'] = date('d/m/Y', strtotime($item['data'])); ?>

                <?php $results = $wpdb->get_results( "SELECT * FROM wp_cte WHERE idC = ".$_GET['idC']."", OBJECT ); foreach ( $results as $result ) 
{

    $dataa = $result->data;
    $cidade_id = $result->cidade;
    $dataa = str_replace("-", "/", $dataa);
    $dataa = date('d/m/Y', strtotime($dataa));




$cidades = $wpdb->get_results( "SELECT * FROM cidade WHERE id = ".$cidade_id."", OBJECT ); foreach ( $cidades as $cidade ) 
{ $nomee = $cidade->nome; }




     ?>



    <h2><?php _e('Inscritos no curso '.$result->nome.', dia '.$dataa.' em '.$nomee.'', 'wpbc')?> 


<?php } ?>  

</h2>
    <h2>
                                 <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="contacts-table" method="POST">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table2->display() ?>
    </form>

</div>
<?php
}













function wpbc_contacts_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {

         echo '<script> alert("Item excluido");</script>';
         /*
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Itens deletados: %d', 'wpbc'), count($_REQUEST['idC'])) . '</p></div>'; */
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Cursos', 'wpbc')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=novo_contacts_form');?>"><?php _e('Adicionar novo', 'wpbc')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="contacts-table" method="POST">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}





function wpbc_programas_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table3();
    $table->prepare_items3();

    $message = '';
    if ('deleteP' === $table->current_action()) {
        if($_POST['sinal'] == '1'){}else{

        echo '<script> alert("Programa excluído.");</script>';
         echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#conteudo_programatico"
                            </script>';
    }
     /*    $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Itens deletados: %d', 'wpbc'), count($_REQUEST['idP'])) . '</p></div>'; */
    }
    ?>
<div class="wrap" id="conteudo_programatico">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Conteúdo Programático', 'wpbc')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cria_programa&idC='.$_GET['idC'].'');?>"><?php _e('Adicionar novo', 'wpbc')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="contacts-table" method="POST" style="width:70%; margin-left:73px">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}







function wpbc_valores_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table4();
    $table->prepare_items4();

    $message = '';
    if ('deleteV' === $table->current_action()) {

        if($_POST['sinal'] == '1'){}else{
         echo '<script> alert("Valor excluido");</script>';



                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#valores"
                            </script>';
     }
         /*
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Itens deletados: %d', 'wpbc'), count($_REQUEST['idP'])) . '</p></div>'; */
    }
    ?>
<div class="wrap" id="valores">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Valores', 'wpbc')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cria_valor&idC='.$_GET['idC'].'');?>"><?php _e('Adicionar novo', 'wpbc')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="contacts-table" method="POST" style="width:70%; margin-left:73px">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}










function wpbc_datasHoras_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table5();
    $table->prepare_items5();

    $message = '';
    if ('delete' === $table->current_action()) {

         echo '<script> alert("Data e horário excluido");</script>';

          echo '<script language= "JavaScript">
                            alert("Valor cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';
         /*
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Itens deletados: %d', 'wpbc'), count($_REQUEST['idDH'])) . '</p></div>'; */
    }
    ?>
<div class="wrap" id="dataH">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Datas e Horários', 'wpbc')?> <a class="add-new-h2"
                                 href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cria_dataHora&idC='.$_GET['idC'].'');?>"><?php _e('Adicionar novo', 'wpbc')?></a>
    </h2>
    <?php echo $message; ?>

    <form id="contacts-table" method="POST" style="width:70%; margin-left:73px">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}


































function wpbc_cria_programa_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'programas'; 

    $message = '';
    $notice = '';


    $default = array(
        'id' => 0,
        'nome' => '',
        'data' => '',
        'hora' => '',
        'descricao' => '',
        'idCurso' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {

            	$item['data'] = str_replace("/", "-", $item['data']);
    			$item['data'] = date('Y-m-d', strtotime($item['data']));
            	
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {

                         echo '<script language= "JavaScript">
                            alert("Programa cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#conteudo_programatico"
                            </script>';


                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');

                }
            } else {

            	

                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {

     echo '<script language= "JavaScript">
                            alert("Programa alterado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#conteudo_programatico"
                            </script>';

                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Cadastro de programa', 'wpbc'), 'wpbc_cria_programa_form_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Cursos', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts_form&idC='.$_GET['idC'].'#conteudo_programatico');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>




                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">

                   <!--<?php wpbc_programas_page_handler(); ?>-->

                </div>
            </div>
        </div>
    </form>
</div>
<?php
}




function wpbc_cria_programa_form_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		
    <form >

    	<p>  
            <label for="nome"><?php _e('Nome:', 'wpbc')?></label>
		<br>
		
            <input type="text" id="nome" name="nome" value="<?php echo esc_attr($item['nome'])?>" style="width: 60%">
		</p>
	
        <p>			
		    <label for="descricao"><?php _e('Descricao:', 'wpbc')?></label>
		<br>	
               <textarea id="descricao" name="descricao" cols="100" rows="33" maxlength="10000"><?php echo esc_attr($item['descricao'])?></textarea>        

		</p>
       

            <input type="hidden" name="idCurso" value="<?php  if (empty($item['idCurso'])) {
           echo esc_attr($_GET['idC']); 
        } else {
            echo esc_attr($item['idCurso']);
        }?>">

 
		</p>
		</form>
		</div>
</tbody>
<?php
}





function wpbc_cria_valor_form_meta_box_handler($item)
{
    ?>
<tbody >
    <style>
    div.postbox {width: 70%; margin-left: 73px;}
    </style>    
        
    <div class="formdata">      
        
		
		
		
		<script>
		
		function formatarMoeda() {
  var elemento = document.getElementById('valor');
  var valor = elemento.value;
  
  valor = valor + '';
  valor = parseInt(valor.replace(/[\D]+/g,''));
  valor = valor + '';
  valor = valor.replace(/([0-9]{2})$/g, ",$1");

  if (valor.length > 6) {
    valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
  }

  elemento.value = valor;
}

</script>
		
		
		
		
		
		
    <form>

        <p>  
            <label for="publico"><?php _e('Público:', 'wpbc')?></label>
        <br>
        
            <input type="text" id="publico" name="publico" value="<?php echo esc_attr($item['publico'])?>" style="width: 60%">
        </p>
    
        <p>         
            <label for="valor"><?php _e('Valor:', 'wpbc')?></label>
        <br>    
                <input type="text" id="valor" name="valor" value="<?php echo esc_attr($item['valor'])?>" style="width: 60%" onkeyup="formatarMoeda();">      

        </p>
       

            <input type="hidden" name="idCurso" value="<?php  if (empty($item['idCurso'])) {
           echo esc_attr($_GET['idC']); 
        } else {
            echo esc_attr($item['idCurso']);
        }?>">

 
        </p>
        </form>
        </div>
</tbody>
<?php
}










function wpbc_cria_valor_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'valoresCursos'; 

    $message = '';
    $notice = '';


    $default = array(
        'idV' => 0,
		'idCurso' => '',
        'publico' => '',
        'valor' => '',
     
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idV'] == 0) {

				$item['valor'] = str_replace('.', '', $item['valor']);
				$item['valor'] = str_replace(',', '.', $item['valor']);
            
                $result = $wpdb->insert($table_name, $item);
                $item['idV'] = $wpdb->insert_id;
                if ($result) {

                       echo '<script language= "JavaScript">
                            alert("Valor cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#valores"
                            </script>';

                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');

                }
            } else {

            $item['valor'] = str_replace('.', '', $item['valor']);
				$item['valor'] = str_replace(',', '.', $item['valor']);

                $result = $wpdb->update($table_name, $item, array('idV' => $item['idV']));
                if ($result) {

                       echo '<script language= "JavaScript">
                            alert("Valor atualizado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#valores"
                            </script>';

                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idV'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idV = %d", $_REQUEST['idV']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Cadastro de valores', 'wpbc'), 'wpbc_cria_valor_form_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap" id="valores">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Valores', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts_form&idC='.$_GET['idC'].'#valores');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>




                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">

                  <!-- <?php wpbc_valores_page_handler(); ?>-->

                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

















function wpbc_cria_dataHora_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'datasHoras'; 

    $message = '';
    $notice = '';


    $default = array(
        'idDH' => 0,
        'idCurso' => '',
        'data' => '',
        'hora' => '',
		'horaFinal' => '',
     
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idDH'] == 0) {

                 $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));

                
                $result = $wpdb->insert($table_name, $item);
                $item['idDH'] = $wpdb->insert_id;
                if ($result) {

                    echo '<script language= "JavaScript">
                            alert("Data e horários cadastrados com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';

                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');

                }
            } else {

                $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));

                

                $result = $wpdb->update($table_name, $item, array('idDH' => $item['idDH']));
                if ($result) {


                    echo '<script language= "JavaScript">
                            alert("Data e horários cadastrados com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';


                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idDH'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idDH = %d", $_REQUEST['idDH']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Cadastro de datas e horários', 'wpbc'), 'wpbc_cria_dataHora_form_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap" id="dataH">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Datas e horários', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts_form&idC='.$_GET['idC'].'#dataH');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>




                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">

                   <!-- <?php wpbc_datasHoras_page_handler(); ?>-->

                </div>
            </div>
        </div>
    </form>
</div>
<?php
}







function wpbc_cria_dataHora_form_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		
    <form>

    	<p>  
            <label for="data"><?php _e('Data:', 'wpbc')?></label>
		<br>
		
            <input type="date" id="data" name="data" value="<?php echo esc_attr($item['data'])?>" style="width: 60%">
		</p>
		<p>  
            <label for="hora inicial"><?php _e('Hora inicial:', 'wpbc')?></label>
		<br>

	
            <input type="text" id="hora" name="hora" value="<?php echo esc_attr($item['hora']) ?>">
		</p>
		
		<p>  
            <label for="hora final"><?php _e('Hora final:', 'wpbc')?></label>
		<br>

	
            <input type="text" id="horaFinal" name="horaFinal" value="<?php echo esc_attr($item['horaFinal']) ?>">
		</p>

		 <input type="hidden" id="idDH" name="idV" value=" <?php echo esc_attr($item['idDH']) ?>">

            <input type="hidden" name="idCurso" value="<?php if (empty($item['idCurso'])) {
           echo esc_attr($_GET['idC']); 
        } else {
            echo esc_attr($item['idCurso']);
        }?>">

 
		</p>
		</form>
		</div>
</tbody>
<?php
}























function wpbc_contacts_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cte'; 

    $message = '';
    $notice = '';


    $default = array(
        'idC' => 0,
        'data' => '',
        'nome' => '',
        'valor' => '',
        'descricao' => '',
		'objetivos' => '',
        'endereco' => '',
        'name' => '',
        'lastname' => '',
        'email' => '',
        'phone' => null,
        'address' => '',
        'notes' => '',
        'noSite' => '',
		'cidade' => '',
        'estado' => '',
        'limiteInscr' => '',
        'cargaHoraria' => '',
        'ministradoPor' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idC'] == 0) {

            	$item['data'] = str_replace("/", "-", $item['data']);
    			$item['data'] = date('Y-m-d', strtotime($item['data']));

    			

                $result = $wpdb->insert($table_name, $item);
                $item['idC'] = $wpdb->insert_id;
                if ($result) {

                     echo '<script language= "JavaScript">
                            alert("Curso cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'"
                            </script>';


                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {

            	$item['data'] = str_replace("/", "-", $item['data']);
    			$item['data'] = date('Y-m-d', strtotime($item['data']));



                $result = $wpdb->update($table_name, $item, array('idC' => $item['idC']));
                if ($result) {


 echo '<script language= "JavaScript">
                            alert("Programa atualizado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'"
                            </script>';



                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idC'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idC = %d", $_REQUEST['idC']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Cadastro do curso', 'wpbc'), 'wpbc_contacts_form_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Cursos', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>


<!--<input type="submit" value="<?php _e('Save', 'wpbc')?>" id="submit" class="button-primary" name="submit">-->




<?php wpbc_programas_page_handler(); ?>

<?php wpbc_valores_page_handler(); ?>

<?php wpbc_datasHoras_page_handler(); ?>






                    
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}
























function wpbc_novo_contacts_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cte'; 

    $message = '';
    $notice = '';


    $default = array(
        'idC' => 0,
        'data' => '',
        'nome' => '',
        'valor' => '',
        'descricao' => '',
		'objetivos' => '',
        'endereco' => '',
        'name' => '',
        'lastname' => '',
        'email' => '',
        'phone' => null,
        'address' => '',
        'notes' => '',
        'noSite' => '',
		'cidade' => '',
        'estado' => '',
        'limiteInscr' => '',
        'cargaHoraria' => '',
        'ministradoPor' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idC'] == 0) {

                $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));

                

                $result = $wpdb->insert($table_name, $item);
                $item['idC'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item cadastrado com sucesso.', 'wpbc');

                    echo '<script language= "JavaScript">
                            alert("Curso cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts"
                            </script>';
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {

                $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));



                $result = $wpdb->update($table_name, $item, array('idC' => $item['idC']));
                if ($result) {


                    $message = __('Curso atualizado com sucesso.', 'wpbc');



                     echo '<script language= "JavaScript">
                            alert("Curso alterado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts"
                            </script>';


                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idC'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idC = %d", $_REQUEST['idC']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Cadastro do curso', 'wpbc'), 'wpbc_contacts_form_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Cursos', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>


<!--<input type="submit" value="<?php _e('Save', 'wpbc')?>" id="submit" class="button-primary" name="submit">-->











                    
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}













function wpbc_contacts_form2_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'inscrito'; 

    $message = '';
    $notice = '';


    $default = array(
        'id' => 0,
        'name' => '',
        'lastname' => '',
        'email' => '',
        'phone' => null,
        'address' => '',
        'notes' => '',
        'celular' => '',
        'pago' => '',
        'concluido' => '',
         'nome' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {


              
 if ($item['pago'] == '3') {




foreach( $wpdb->get_results("SELECT * FROM wp_cte WHERE idC=".$_GET['idC']."") as $key => $row) {
// each column in your row will be accessible like this
  $nome = $row->nome;
   $dataInicio = $row->data;
} 




include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 

// Inicia a classe PHPMailer 
$mail = new PHPMailer(); 

// Método de envio 
$mail->IsSMTP(); 
 
// Enviar por SMTP 
$mail->Host = "franciscorossal.com.br"; 
 
// Você pode alterar este parametro para o endereço de SMTP do seu provedor 
$mail->Port = 25; 
//$mail->SMTPDebug = 4;
 
 
// Usar autenticação SMTP (obrigatório) 
$mail->SMTPAuth = true; 
 
// Usuário do servidor SMTP (endereço de email) 
// obs: Use a mesma senha da sua conta de email 
$mail->Username = 'contato@franciscorossal.com.br'; 
$mail->Password = 'Acesso@2019!'; 
 
// Configurações de compatibilidade para autenticação em TLS 
$mail->SMTPOptions = array( 'ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true ) ); 
 
// Você pode habilitar esta opção caso tenha problemas. Assim pode identificar mensagens de erro. 
//$mail->SMTPDebug = 2; 
 
// Define o remetente 
// Seu e-mail 
$mail->From = "contato@franciscorossal.com.br"; 
 
// Seu nome 
$mail->FromName = "Francisco Rossal"; 
 
// Define o(s) destinatário(s) 
$mail->AddAddress($item['email'], $item['nome']); 
 
// Opcional: mais de um destinatário
// $mail->AddAddress('fernando@email.com'); 
 
// Opcionais: CC e BCC
// $mail->AddCC('joana@provedor.com', 'Joana'); 
// $mail->AddBCC('roberto@gmail.com', 'Roberto'); 
 
// Definir se o e-mail é em formato HTML ou texto plano 
// Formato HTML . Use "false" para enviar em formato texto simples ou "true" para HTML.
$mail->IsHTML(true); 
 
// Charset (opcional) 
$mail->CharSet = 'UTF-8'; 
 
// Assunto da mensagem 
$mail->Subject = "Confirmação de inscrição"; 
 
// Corpo do email 
$mail->Body = 'Olá! Sua inscrição no curso '.$nome.' foi confirmada. </br></br></br>

Para maiores informações acesse o site <a href="https://www.franciscorossal.com.br">clicando aqui</a>.'; 
 
// Opcional: Anexos 
// $mail->AddAttachment("/home/usuario/public_html/documento.pdf", "documento.pdf"); 
 
// Envia o e-mail 
$enviado = $mail->Send(); 
 
// Exibe uma mensagem de resultado 
if ($enviado) 
{ 
    //echo "Seu email foi enviado com sucesso!"; 
} else { 
    echo "Houve um erro enviando o email: ".$mail->ErrorInfo; 
} 
                 } 






                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Dados do inscrito', 'wpbc'), 'wpbc_contacts_form2_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Cursos', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=listaInscritos&idC='.$_REQUEST['idC'].'');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>


        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}



function wpbc_contacts_form2_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		
    <form >
        <p>			
		    <label for="name"><?php _e('Nome:', 'wpbc')?></label>
		<br>	
            <input id="name" name="nome" type="text" style="width: 60%" value="<?php echo esc_attr($item['nome'])?>"
                    required>

		</p><p>
            <label for="email"><?php _e('E-Mail:', 'wpbc')?></label> 
		<br>	
            <input id="email" name="email" type="email" style="width: 60%" value="<?php echo esc_attr($item['email'])?>"
                   required>
        </p><p>	  
            <label for="phone"><?php _e('Celular:', 'wpbc')?></label> 
		<br>
			<input id="phone" name="celular" type="tel" style="width: 60%" value="<?php echo esc_attr($item['celular'])?>">
        </p>
        <p>   
            <label for="phone"><?php _e('Pagamento:', 'wpbc')?></label> 
        <br>
        <select name="pago">
        <?php     switch ($item['pago']) {
    case '1':
      echo '<option value="1">Aguardando pagamento</option>';
        break;
    case '2':
       echo '<option value="2">Pagamento em análise</option>';
        break;
     case '3':
       echo '<option value="3">Pagamento aprovado</option>';
        break;
         case '4':
       echo '<option value="4">Pagamento liberado</option>';
        break;
             case '5':
       echo '<option value="5">Em disputa</option>';
        break;
             case '6':
       echo '<option value="6">Devolvida</option>';
        break;
             case '7':
       echo '<option value="7">Cancelada</option>';
        break;
   default:
         echo '<option value="0">eRRO - cONTATE O SUPORTE</option>';
        break;
} ?>

<option value="1">Aguardando pagamento</option><option value="2">Pagamento em análise</option><option value="3">Pagamento aprovado</option><option value="4">Pagamento liberado</option>
<option value="5">Em disputa</option><option value="6">Devolvida</option><option value="7">Cancelada</option>
</select>
        </p>


         <p>   
            <label for="concluido"><?php _e('Concluído:', 'wpbc')?></label> 
        <br>
<select name="concluido">
        <?php     switch ($item['concluido']) {
    case '1':
      echo '<option value="1">Sim</option>';
        break;
    case '0':
       echo '<option value="0">Não</option>';
        break;
    
   default:
         echo '<option value="0">eRRO - cONTATE O SUPORTE</option>';
        break;
} ?>

<option value="1">Sim</option><option value="0">Não</option>
</select>
        </p>


		</form>
		</div>
</tbody>
<?php
}






















function wpbc_contacts_form3_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'programas'; 

    $message = '';
    $notice = '';


    $default = array(
        'id' => 0,
        'nome' => '',
        'data' => '',
        'hora' => '',
        'lastname' => '',
        'email' => '',
        'phone' => null,
        'address' => '',
        'notes' => '',
        'descricao' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {

                $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));

                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {

                         echo '<script language= "JavaScript">
                            alert("Programa cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#conteudo_programatico"
                            </script>';



                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {

                $item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));

                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {


                    echo '<script language= "JavaScript">
                            alert("Programa atualizado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#conteudo_programatico"
                            </script>';


                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Edição de programa de curso', 'wpbc'), 'wpbc_contacts_form3_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Conteúdo Programático', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts_form&idC='.$_GET['idC'].'#conteudo_programatico');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}



function wpbc_contacts_form3_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		
    <form>
        <p>			
		    <label for="nome"><?php _e('Nome:', 'wpbc')?></label>
		<br>	
            <input id="nome" name="nome" type="text" style="width: 60%" value="<?php echo esc_attr($item['nome'])?>"
                    required>
		</p>
		<p>	


<!--<p>	
            <label for="lastname"><?php _e('Last Name:', 'wpbc')?></label>
		<br>
		    <input id="lastname" name="lastname" type="text" style="width: 60%" value="<?php echo esc_attr($item['lastname'])?>"
                    required>
        </p><p>
            <label for="email"><?php _e('E-Mail:', 'wpbc')?></label> 
		<br>	
            <input id="email" name="email" type="email" style="width: 60%" value="<?php echo esc_attr($item['email'])?>"
                   required>
        </p><p>	  
            <label for="phone"><?php _e('Phone:', 'wpbc')?></label> 
		<br>
			<input id="phone" name="phone" type="tel" style="width: 60%" value="<?php echo esc_attr($item['phone'])?>">
        </p><p>
		    <label for="address"><?php _e('Address:', 'wpbc')?></label> 
		<br>
            <textarea id="address" name="address" cols="100" rows="3" maxlength="240"><?php echo esc_attr($item['address'])?></textarea>
		</p><p>  
            <label for="notes"><?php _e('Notes:', 'wpbc')?></label>
		<br>
            <textarea id="notes" name="notes" cols="100" rows="3" maxlength="240"><?php echo esc_attr($item['notes'])?></textarea>
		</p>-->
		<p>  
            <label for="descricao"><?php _e('Descrição:', 'wpbc')?></label>
		<br>
            <textarea id="descricao" name="descricao" cols="150" rows="33" maxlength="50000"><?php echo esc_attr($item['descricao'])?></textarea>
		</p>
		</form>
		</div>
</tbody>
<?php
}


















function wpbc_contacts_form4_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'valoresCursos'; 

    $message = '';
    $notice = '';


    $default = array(
        'idV' => 0,
        'publico' => '',
        'valor' => '',
      
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idV'] == 0) {



                $result = $wpdb->insert($table_name, $item);
                $item['idV'] = $wpdb->insert_id;
                if ($result) {

                       echo '<script language= "JavaScript">
                            alert("Valor cadastrado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#valores"
                            </script>';
                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {

$item['valor'] = str_replace('.', '', $item['valor']);
				$item['valor'] = str_replace(',', '.', $item['valor']);

                $result = $wpdb->update($table_name, $item, array('idV' => $item['idV']));
                if ($result) {


                     echo '<script language= "JavaScript">
                            alert("Valor atualizado com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#valores"
                            </script>';

                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idV'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idV = %d", $_REQUEST['idV']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Edição de valor de curso', 'wpbc'), 'wpbc_contacts_form4_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap" id="valores">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Valores', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=contacts_form&idC='.$_GET['idC'].'#valores');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}



function wpbc_contacts_form4_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		
    <form>
        <p>			
		    <label for="publico"><?php _e('Publico:', 'wpbc')?></label>
		<br>	
            <input id="publico" name="publico" type="text" style="width: 60%" value="<?php echo esc_attr($item['publico'])?>"
                    required>
		</p>
		<p>	


		
		<script>
		
		function formatarMoeda() {
  var elemento = document.getElementById('valor');
  var valor = elemento.value;
  
  valor = valor + '';
  valor = parseInt(valor.replace(/[\D]+/g,''));
  valor = valor + '';
  valor = valor.replace(/([0-9]{2})$/g, ",$1");

  if (valor.length > 6) {
    valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
  }

  elemento.value = valor;
}

</script>
		
				<?php $item['valor'] = number_format($item['valor'], 2, ',', '.');  ?>


            <label for="valor"><?php _e('Valor:', 'wpbc')?></label>
		<br>
		    <input id="valor" onkeyup="formatarMoeda();" name="valor" type="text" style="width: 60%" value="<?php echo esc_attr($item['valor'])?>"
                    required >
        </p>
       
	
		</form>
		</div>
</tbody>
<?php
}








function wpbc_contacts_form5_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'datasHoras'; 

    $message = '';
    $notice = '';


    $default = array(
        'idDH' => 0,
        'idCurso' => '',
        'data' => '',
        'hora' => '',
		'horaFinal' => '',
    );


    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        $item_valid = wpbc_validate_contact($item);
        if ($item_valid === true) {
            if ($item['idDH'] == 0) {



                $result = $wpdb->insert($table_name, $item);
                $item['idDH'] = $wpdb->insert_id;
                if ($result) {

                    echo '<script language= "JavaScript">
                            alert("Data e horários cadastrados com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';


                    $message = __('Item cadastrado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar cadastrar...', 'wpbc');
                }
            } else {

				$item['data'] = str_replace("/", "-", $item['data']);
                $item['data'] = date('Y-m-d', strtotime($item['data']));
				
				                $result = $wpdb->update($table_name, $item, array('idDH' => $item['idDH']));
                if ($result) {

                    echo '<script language= "JavaScript">
                            alert("Data e horários cadastrados com sucesso.");
                            </script>';


                    echo '<script language= "JavaScript">
                            location.href="http://franciscorossal.com.br/wp-admin/admin.php?page=contacts_form&idC='.$_REQUEST['idC'].'#dataH"
                            </script>';


                    $message = __('Item atualizado com sucesso.', 'wpbc');
                } else {
                    $notice = __('Ocorreu um erro ao tentar atualizar...', 'wpbc');
                }
            }
        } else {
            
            $notice = $item_valid;
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['idDH'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE idDH = %d", $_REQUEST['idDH']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'wpbc');
            }
        }
    }

    
    add_meta_box('contacts_form_meta_box', __('Edição de datas e horas do curso', 'wpbc'), 'wpbc_contacts_form5_meta_box_handler', 'contact', 'normal', 'default');

    ?>
<div class="wrap" id="dataH">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Datas e horários', 'wpbc')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=cria_dataHora&idC='.$_GET['idC'].'#dataH');?>"><?php _e('Voltar', 'wpbc')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    
                    <?php do_meta_boxes('contact', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}



function wpbc_contacts_form5_meta_box_handler($item)
{
    ?>
<tbody >
    <style>
    div.postbox {width: 70%; margin-left: 73px;}
    </style>    
        
    <div class="formdata">      
        
    <form>
        <p>         
            <label for="data"><?php _e('Data:', 'wpbc')?></label>
        <br>    
		
		
		
		
            <input id="data" name="data" type="date" style="width: 60%" value="<?php echo esc_attr($item['data'])?>"
                    required>
        </p>
       


            <input type="hidden" name="idCurso" value="<?php if (empty($item['idCurso'])) {
           echo esc_attr($_GET['idC']); 
        } else {
            echo esc_attr($item['idCurso']);
        }?>">
		<p> 
            <label for="hora"><?php _e('Hora inicial:', 'wpbc')?></label>
        <br>
            <input id="hora" name="hora" type="text" style="width: 60%" value="<?php echo esc_attr($item['hora'])?>"
                    required>
        </p>
		
		<p> 
            <label for="hora"><?php _e('Hora final:', 'wpbc')?></label>
        <br>
            <input id="horaFinal" name="horaFinal" type="text" style="width: 60%" value="<?php echo esc_attr($item['horaFinal'])?>"
                    required>
        </p>
       
    
        </form>
        </div>
</tbody>
<?php
}

// datas horas













































function wpbc_contacts_form_meta_box_handler($item)
{
    ?>
<tbody >
	<style>
    div.postbox {width: 70%; margin-left: 73px;}
	</style>	
		
	<div class="formdata">		
		<?php 

			

			?>	
    

			<script>
function JumpField(fields) {
 if (fields.value.length == fields.maxLength) {
  for (var i = 0; i < fields.form.length; i++) {
   if (fields.form[i] == fields && fields.form[(i + 1)] && fields.form[(i + 1)].type != "hidden") {
	fields.form[(i + 1)].focus();
	break;
   }
  }
 }
}
			</script>




    <form>


    		<!--<p>			
		    <label for="data"><?php _e('Início (campo em teste. Não usar.): ', 'wpbc')?></label>
		<br>	
            <input name="dia" onKeyUp="javascript:JumpField(this);" maxlength="2" size="2">
<input name="mes" onKeyUp="javascript:JumpField(this);" maxlength="2"  size="2">
<input name="ano" onKeyUp="javascript:JumpField(this);" maxlength="4"  size="2"> 


		</p>-->



    	<p>			
		    <label for="data"><?php _e('Início: ', 'wpbc')?></label>
		<br>	
            <input id="data" name="data" type="date" style="width: 60%" value="<?php echo esc_attr($item['data'])?>"
                    required placeholder="11/10/2018"> 


		</p>
		<p>			
		    <label for="nome"><?php _e('Nome:', 'wpbc')?></label>
		<br>	
            <input id="nome" name="nome" type="text" style="width: 60%" value="<?php echo esc_attr($item['nome'])?>"
                    required>
		</p>


		<p>			
		    <label for="limiteInscr"><?php _e('Máximo de inscritos:', 'wpbc')?></label>
		<br>	
            <input id="limiteInscr" name="limiteInscr" type="text" style="width: 60%" value="<?php echo esc_attr($item['limiteInscr'])?>"
                    >
		</p>



        <p>         
            <label for="limiteInscr"><?php _e('Carga horária:', 'wpbc')?></label>
        <br>    
            <input id="limiteInscr"  maxlenght="2" style="width: 60px" name="cargaHoraria" type="text" style="width: 60%" value="<?php echo esc_attr($item['cargaHoraria'])?>"
                    > horas
        </p>




		<input type="hidden" name="sinal" value="1">

		    <label for="noSite"><?php _e('<strong>Disponível no site: </strong>', 'wpbc')?></label>
		<br>	
			
            <select name="noSite">
            <?php
            switch ($item['noSite']) {
    case '1':
        echo '<option value="1">Sim</option><option value="2">Não</option>';
        break;
    case '2':
        echo '<option value="2">Não</option><option value="1">Sim</option>';
        break;
   default:
            echo '<option value="">Escolha</option><option value="1">Sim</option><option value="2">Não</option>';
        break;
} ?>

            	

            </select>
		</p>

		<p>			
		    <label for="descricao"><?php _e('Apresentação: (máximo 1300 caracteres)', 'wpbc')?></label>
		<br>
			<textarea id="descricao" name="descricao" type="text"  rows="15" maxlength="1300" style="width: 90%"><?php echo esc_attr($item['descricao'])?></textarea>
          
		</p>
		
		
		<p>			
		    <label for="objetivos"><?php _e('Objetivos: (máximo 1300 caracteres)', 'wpbc')?></label>
		<br>
			<textarea id="objetivos" name="objetivos" type="text"  rows="15" maxlength="1300" style="width: 90%"><?php echo esc_attr($item['objetivos'])?></textarea>
          
		</p>

        <p>         
            <label for="ministradoPor"><?php _e('Ministrado pelo:', 'wpbc')?></label>
        <br>
            <textarea id="ministradoPor" name="ministradoPor" type="text"  rows="2" maxlength="1300" style="width: 90%"><?php echo esc_attr($item['ministradoPor'])?></textarea>
          
        </p>
		
		
		
		
	<p>			
		    <label for="endereco"><?php _e('Endereço completo:', 'wpbc')?></label>
		<br>	
            <input id="endereco" name="endereco" type="text" style="width: 60%" value="<?php echo esc_attr($item['endereco'])?>"
                    required>
		</p>
		
			<p>			
		    <label for="cidade"><?php _e('Cidade / Estado:', 'wpbc')?></label>
		<br>	




        <select name="cidade">




            <!--<input id="cidade" name="cidade" type="text" style="width: 60%" value="<?php echo esc_attr($item['cidade'])?>"
                    required>-->

<?php 

$db = new mysqli('localhost', 'franci80_wp742', 'Fr33S4nm4r1n0', 'franci80_wp742');
if(mysqli_connect_errno()){
echo mysqli_connect_error();
}

if(empty($item['cidade'])){ echo '<option value="4174">Porto Alegre</option>'; }else{

$result2 = $db->query('SELECT * FROM `cidade` WHERE id = "'.$item['cidade'].'"');
if($result2){
   while ($row2 = $result2->fetch_assoc()){
      echo '<option value="'.$row2['id'].'">'.$row2['nome'].'</option>';
   }
   $result2->free();
}
}

?>

<?php 

$result2 = $db->query('SELECT * FROM `cidade` order by nome');
if($result2){
   while ($row2 = $result2->fetch_assoc()){
      echo '<option value="'.$row2['id'].'">'.$row2['nome'].'</option>';
   }
   $result2->free();
}


?>

</select>




<select name="estado">    



<?php
if(empty($item['estado'])){ echo '<option value="23">RS</option>'; }else{

$result2 = $db->query('SELECT * FROM `estado` WHERE id = "'.$item['estado'].'"');
if($result2){
   while ($row2 = $result2->fetch_assoc()){
      echo '<option value="'.$row2['id'].'">'.$row2['uf'].'</option>';
   }
   $result2->free();
}
};




$result5 = $db->query('SELECT * FROM `estado` WHERE id="23" OR id="24"');
if($result5){
   while ($row5 = $result5->fetch_assoc()){
      echo '<option value="'.$row5['id'].'">'.$row5['uf'].'</option>';
   }
   $result5->free();
}
$db->close();
?>


</select>




















		</p>
        <!--<p>			
		    <label for="name"><?php _e('Name:', 'wpbc')?></label>
		<br>	
            <input id="name" name="name" type="text" style="width: 60%" value="<?php echo esc_attr($item['name'])?>"
                    required>
		</p><p>	
            <label for="lastname"><?php _e('Last Name:', 'wpbc')?></label>
		<br>
		    <input id="lastname" name="lastname" type="text" style="width: 60%" value="<?php echo esc_attr($item['lastname'])?>"
                    required>
        </p><p>
            <label for="email"><?php _e('E-Mail:', 'wpbc')?></label> 
		<br>	
            <input id="email" name="email" type="email" style="width: 60%" value="<?php echo esc_attr($item['email'])?>"
                   required>
        </p><p>	  
            <label for="phone"><?php _e('Phone:', 'wpbc')?></label> 
		<br>
			<input id="phone" name="phone" type="tel" style="width: 60%" value="<?php echo esc_attr($item['phone'])?>">
        </p><p>
		    <label for="address"><?php _e('Address:', 'wpbc')?></label> 
		<br>
            <textarea id="address" name="address" cols="100" rows="3" maxlength="240"><?php echo esc_attr($item['address'])?></textarea>
		</p><p>  
            <label for="notes"><?php _e('Notes:', 'wpbc')?></label>
		<br>
            <textarea id="notes" name="notes" cols="100" rows="3" maxlength="240"><?php echo esc_attr($item['notes'])?></textarea>
		</p>-->
		<input type="submit" value="<?php _e('Salvar', 'wpbc')?>" id="submit" class="button-primary" name="submit">
		</form>
		</div>
</tbody>
<?php
}


function wpbc_validate_contact($item)
{
    $messages = array();

    /*if (empty($item['name'])) $messages[] = __('Name is required', 'wpbc');
    if (empty($item['lastname'])) $messages[] = __('Last Name is required', 'wpbc');*/


    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'wpbc');
    if(!empty($item['phone']) && !absint(intval($item['phone'])))  $messages[] = __('Phone can not be less than zero');
    if(!empty($item['phone']) && !preg_match('/[0-9]+/', $item['phone'])) $messages[] = __('Phone must be number');
    

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}


function wpbc_languages()
{
    load_plugin_textdomain('wpbc', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'wpbc_languages');
