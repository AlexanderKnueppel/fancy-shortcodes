<?php

class FSH_Autoupdate
{
    /**
     * The plugin current version
     * @var string
     */
    public $current_version;

    /**
     * The plugin remote update path
     * @var string
     */
    public $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    public $plugin_slug;

    /**
     * Plugin name (plugin_file)
     * @var string
     */
    public $slug;
	/**
     * License (plugin_file)
     * @var string
     */
	public $license;
    /**
     * Initialize a new instance of the WordPress Auto-Update class
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     */
    function __construct($current_version, $update_path, $plugin_slug, $license = '')
    {
        // Set the class public variables
        $this->license = $license;
	    $this->current_version = $current_version;
        $this->update_path = $update_path;
        $this->plugin_slug = $plugin_slug;
        list ($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t2);
        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));
        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'check_info'), 10, 3);
		//add_filter('plugins_api_result', array($this,'aaa_result'), 10, 3);
    }
	
	
	function aaa_result($res, $action, $args) {
		print_r($res);
		return $res;
	}

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update($transient)
    {
		global $wp_version;
		
        if (empty($transient->checked)) {
            return $transient;
        }
		
		$args = array(
			'slug' => $this->slug,
			'version' => $transient->checked[$this->plugin_slug],
		);
		
		$request_string = array(
			'body' => array(
				'action' => 'basic_check', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
		
		// Start checking for an update
		$raw_response = wp_remote_post($this->update_path, $request_string);
		
		$response = null;

		//var_dump($raw_response);
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
			$response = unserialize($raw_response['body']);
		
		if (is_object($response) && !empty($response)) // Feed the update data into WP updater
			$transient->response[$this->plugin_slug] = $response;
			
        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array $action
     * @param object $arg
     * @return bool|object
     */
    public function check_info($def, $action, $args)
    {
		global $wp_version;
		//var_dump($args);
	   	if (!isset($args->slug) || ($args->slug != $this->slug))
			return false;
		
		$plugin_info = get_site_transient('update_plugins');
		$current_version = $plugin_info->checked[$this->plugin_slug];
		$args->version = $current_version;
		
		$request_string = array(
			'body' => array(
				'action' => 'plugin_information', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
		
		$request = wp_remote_post($this->update_path, $request_string);
		//var_dump($request);
		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return 						
														  false;">Try again</a>'), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);
			
			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}
			
		return $res;
    }

    /**
     * Return the remote version
     * @return string $remote_version
     */
   /* public function getRemote_version()
    {

	    global $wp_version;
	    $args = array(
		    'slug' => $this->slug,
		    'url'  => home_url()
	    );
		
		if(true == $this->license) {
			$args['key'] = $this->license;	
		}

        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'version','request' => serialize($args)),'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()));
	    if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			//var_dump($request['body']);
		    return $request['body'];
        }
        return false;
    }*/

    /**
     * Get information about the remote version
     * @return bool|object
     */
   /* public function getRemote_information()
    {
	    global $wp_version;
	    $args = array(
		    'slug' => $this->slug,
		    'url'  => home_url()
	    );
		
		if(true == $this->license) {
			$args['key'] = $this->license;	
		}

	    $request = wp_remote_post($this->update_path, array('body' => array('action' => 'info','request' => serialize($args)),'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()));
	    if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
	        return unserialize($request['body']);
        }
        return false;
    }*/
}