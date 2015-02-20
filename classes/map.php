<?php
/*
* * row
* * column
* * dropcat
* * 
*
*
*
*
*
*/
if( !class_exists('FSH_Shortcode_Map') ) {
class FSH_Shortcode_Map {
	
	public $map = array();
	
	//protected $prefix;
	
	public function __construct($prefix = '') {
		$this->map['dropcap'] = array(
						'tag_name'       => 'dropcap',
						'category'       => 'Typographie',
				      	'add_to_editor'  => true,
			          	'dependency'     => null,
				      	'content'        => '[dropcap]...[/dropcap]', 
						'description' => '',
						'help' => '',
					    );
		$this->map['dummy2'] = array(
						'tag_name'       => 'dummy2',
						'category'       => 'Dummy',
				      	'add_to_editor'  => true,
			          	'dependency'     => null,
				      	'content'        => '[dummy2 text="Im a dummy, the second"][/dummy2]', 
						'description' => '',
						'help' => '',
					    );
	
	}
	
	public function shortcode_dropcap( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'id'      => '',
    		'class'   => '',
    		'style'   => '',
			// extras
		), $atts ) );
		
		$id    = ($id != '')    ? 'id = "'.esc_attr($id).'"' : '';
		$class = ($class != '') ? 'class = "'.esc_attr($class).' dropcap"' : '';
		$style = ($style != '') ? 'style = "'.$style.'"' : '';
		
		return sprintf(
			'<span %s %s %s>%s</span>',
			$id, $class, $style,
			do_shortcode( $content )
		);
	}
	
	public function shortcode_dummy2( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'id'      => '',
    		'class'   => '',
    		'style'   => '',
			// extras
			'text' 	  => ''
		), $atts ) );
		
		return sprintf(
			'<div class="dummy2">%s! %s</div>',
			$text,
			do_shortcode( $content )
		);
	}
}
}
?>