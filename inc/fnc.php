<?php

class Ahafs_Script{

	function __construct(){

		add_action('admin_menu',array( $this ,'ahafs_menu'));
		add_action( 'wp_head', array( $this , 'frontendHeader'));
		add_action( 'wp_footer', array( $this , 'frontendfooter'));
		if (function_exists( 'wp_body_open')){
			add_action( 'wp_body_open', array( $this , 'frontendBody' ),1);
		}
		if(isset($_GET['page']) && $_GET['page'] == 'ahafs_page'){
			add_action('admin_enqueue_scripts',array( $this , 'ahafs_enque_styles_scripts'));
        	add_action('wp_ajax_ahafsAjax_action',array( $this , 'ahafs_ajx_cbFn_'));
		}

	}
	function ahafs_sanitize_text_field_and_array($array){
	    foreach ( $array as $key => $value ) {
		    if ( is_array( $value ) ) {
		    	$value = $this->ahafs_sanitize_text_field_and_array( $value);
		    }else{
		    	$value = wp_kses_post( $value );
		    }
	    }
	    return $array;
	}
	
// ajax callback function making data friendly with php without refresh
	function ahafs_ajx_cbFn_(){

		if(isset($_POST['ahafs_codemirror'])){
		$retndata = array();
		$tri_head = str_replace(" ","",$_POST['ahafs_codemirror']['editor_header']);
		$tri_body = str_replace(" ","",$_POST['ahafs_codemirror']['editor_body']);
		$tri_foot = str_replace(" ","",$_POST['ahafs_codemirror']['editor_footer']);

		if( $tri_head !== '' || $tri_body !== '' || $tri_foot !== ''){
			if ($_POST['ahafs_codemirror']['editor_header'] !=='' || $_POST['ahafs_codemirror']['editor_body'] !=='' || $_POST['ahafs_codemirror']['editor_footer'] !=='') {
				$post_data = $this->ahafs_sanitize_text_field_and_array($_POST['ahafs_codemirror']);
				foreach ($post_data as $key => $value) {
					$value_n = $value == ''?'':$value;
					$id 	 = 'ahafs_'.$key;
					if (get_option($id) !== $value_n) {
						$update[$key] = update_option($id,$value_n)?true:false;
					}else{
						$insert[$key] = add_option($id,$value_n)?true:false;
					}
				}

				if(isset($update) && ($update['editor_header'] || $update['editor_body'] || $update['editor_footer'])) {
					$retndata['success'] = 'code updated';
				}elseif (isset($insert) && ($insert['editor_header'] || $insert['editor_body'] || $insert['editor_footer'])) {
					$retndata['success'] = 'saved';
				}else{
					$retndata['error'] = 'change code for update';
				}


			}
		}else{
			foreach ($_POST['ahafs_codemirror'] as $key => $value) {
				if($key !== ''){
					$id = 'ahafs_'.$key;
					$delete = get_option($id)?delete_option($id):delete_option($id);
					$retndata['error'] = $delete?'updated':'enter any code';
				}
			}
		}
		echo json_encode($retndata);
	}
		die();
	}
 
// get data from database to show in front page with tracking code
	function ahafs_output( $from_name ){

		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {
			return;
		}

		$value_get = get_option($from_name);

		if (!empty($value_get)) {
			echo wp_unslash($value_get);
		}

	}

	function frontendHeader(){
		echo $this->ahafs_output('ahafs_editor_header');
	}

	function frontendbody(){
		echo $this->ahafs_output('ahafs_editor_body');
	}

	function frontendfooter(){
		echo $this->ahafs_output('ahafs_editor_footer');
	}

	function ahafs_menu(){
        add_menu_page('Add Header And Footer Script' ,'Add Header And Footer Script' ,'manage_options' ,'ahafs_page',array( $this ,'ahafs_content'),plugins_url('../images/icon.png', __FILE__ ));
	}

// enqueue all sheets of js and css of codemirror and custon sheets
	function ahafs_enque_styles_scripts(){
    	wp_enqueue_style('codemirror', AHAFS_URL.'codemirror/lib/codemirror.css', false);
    	wp_enqueue_style('ahafs-style', AHAFS_URL.'css/ahafs-style.css', false);
    	wp_enqueue_script('codemirror', AHAFS_URL.'codemirror/lib/codemirror.js',array('jquery'), true);
    	wp_enqueue_script('codemirror-javascript', AHAFS_URL.'codemirror/mode/javascript/javascript.js', array('jquery'), true);
	    wp_enqueue_script('ahafs-custom', AHAFS_URL.'js/ahafs-custom.js',array('jquery'), false);
	    wp_localize_script('ahafs-custom','ahafsURL_src',array('ahafs_url' => admin_url('admin-ajax.php')));

	}


// this contain main layout and containers of front page
	function ahafs_content(){

	$value_header = get_option('ahafs_editor_header')?get_option('ahafs_editor_header'):'';
	if($value_header !==''){$value_header = wp_unslash($value_header);}

	$value_body	= get_option('ahafs_editor_body')?get_option('ahafs_editor_body'):'';
	if($value_body !==''){$value_body = wp_unslash($value_body);}

	$value_footer = get_option('ahafs_editor_footer')?get_option('ahafs_editor_footer'):'';
	if($value_footer !==''){$value_footer = wp_unslash($value_footer);}

	echo '<section class="ahafs_script_main">
		<form id="ahafs_form_" method="post" action="">
			<div class="clr">
				<div class="ahafs-codemirrorSection">
					<div class="ahafs_main_heading"><h1>Add Headers and Footers Scripts</h1></div>
					<div class="ahafs-container">
						<h4 class="ahafs_sub_heading">Scripts in Header</h4>
						<textarea class="Editor_header" name="editor_header">'.$value_header.'</textarea>
						<p>'.__("This will be appear in <label class='code_tags'><span><</span><span>head></span></label> section","ahafs").'</p>
					</div>

					<div class="ahafs-container">
						<h4 class="ahafs_sub_heading">Scripts in Body</h4>
						<textarea class="Editor_body" name="editor_body">'.$value_body.'</textarea>
						<p>'.__("These scripts will be printed just below the opening of <label class='code_tags'><span><</span><span>body></span></label> tag.","ahafs").'</p>
						
					</div>

					<div class="ahafs-container">
						<h4 class="ahafs_sub_heading">Scripts in Footer</h4>
						<textarea class="Editor_footer" name="editor_footer">'.$value_footer.'</textarea>
						<p>'.__("These scripts will be printed above the closing of <label class='code_tags'><span><</span><span>/body></span></label> tag.","ahafs").'</p>
					</div>

					<div class="ahafs-container-button">
					<button class="ahafs_save_code">'.__("SAVE","ahafs").'</button>
					</div>

				</div>
		<div class="toaster_view" data-ahafsresult=""><p></p></div>';
		$this->ahafas_sidebar();
	}	



// contain side bar of the plugin
	function ahafas_sidebar(){
		echo '<div class="ahafs-cardSection">
					<div id="postbox-container-1" class="postbox-container">
					<!-- Improve Your Site -->
					<div class="postbox">
					<h3 class="hndle">
					<span>'.__("Wordpress Free Themes","ahafs").'</span>
					</h3>

					<div class="inside">
					<p>'.__("Want to take your site to the next level? Check out our WordPress themes on ","ahafs").'<a href="https://themehunk.com/free-themes/" target="_blank">'.__("click Here","ahafs").'</a>.</p>
					<p>
					'.__("Some of our popular themes :","ahafs").'</p>

					<ul>
					<li>
					<a href="https://themehunk.com/product/big-store/" target="_blank"> '.__("- Big Store Buissness Theme","ahafs").'</a>
					</li>
					<li>
					<a href="https://themehunk.com/product/open-mart/" target="_blank">'.__(" - Open Mart Buissness Theme","ahafs").'</a>
					</li>
					<li>
					<a href="https://themehunk.com/product/oneline-lite-theme/" target="_blank">'.__(" - One Line Multipurpose Theme","ahafs").'</a>
					</li>
					</ul>

					</div>
					</div>

					<!-- Donate -->
					<div class="postbox">
					<h3 class="hndle">
					<span>'.__("WordPress Free Plugins","ahafs").'</span>
					</h3>
					<div class="inside">
					<p>
					'.__("Like this plugin? Check out our other WordPress plugins:","ahafs").'</p>
					<p>
					<a href="https://wordpress.org/plugins/lead-form-builder/" target="_blank">'.__("Lead Form Builder","ahafs").'</a> '.__("- Drag &amp; Drop WordPress Form Builder").'</p>
					<p>
					<a href="https://themehunk.com/product/wp-popup-builder/" target="_blank">Popup Builder</a>'.__(" - Marketing Popup Biulder","ahafs").'</p>
					<p>
					<a href="https://themehunk.com/product/themehunk-megamenu/" target="_blank">Mega Menu</a>'.__(" -Advance Mega Menu","ahafs").'</p>
					</div>
					</div>
					</div>

				</div>
			</div>
		</form>
		</section>';
	}



}
