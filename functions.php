<?php

function pdd_connect_domain() {
	global $message_codes;
	$error		= false;
	$status		= false;
	$check_results	= false;
	$domain_check	= false;
	$check_file	= false;

	if ( isset( $_POST['connect_domain'] ) ) {
		if ( isset( $_POST['create_boxes'] ) ) {
			update_option( 'pdd_create_boxes', 'automatically' );
		}
		$json = pdd_exec_command( 'GET', 'domain/registration_status', array(), $_POST['pdd_token'] );
		$domain_check = json_decode( $json, true );
		if (  'ok' == $domain_check['success'] ) {
			update_option( 'pdd_pdd_token', $_POST['pdd_token'] );
		}
	} else if ( get_option( 'pdd_pdd_token' ) ) {
		$json = pdd_exec_command( 'GET', 'domain/registration_status' );
		$domain_check = json_decode( $json, true );
	}
	if ( isset( $domain_check['secrets'] ) && isset( $_POST['create_check_file'] ) ) {
		if ( !file_exists( $_SERVER['DOCUMENT_ROOT'] . '/' . $domain_check['secrets']['name'] . '.html' ) ) {
			file_put_contents( $_SERVER['DOCUMENT_ROOT'] . '/' . $domain_check['secrets']['name'] . '.html', $domain_check['secrets']['content'] );
		}
		$check_file = $domain_check['secrets']['name'] . '.html';
	}
	if ( $domain_check ) {
		if ( 'error' == $domain_check['success'] ) {
			if ( isset( $message_codes[$domain_check['error']] ) ) {
				$error = $message_codes[$domain_check['error']];
			} else {
				$error = __( 'Неизвестная ошибка', 'pdd' );
			}
		} else if (  'ok' == $domain_check['success'] ) {
			switch( $domain_check['status'] ) {
				case 'domain-activate':
					$status = __( 'Домен добавлен в Почту для домена, но не подтвержден.', 'pdd' );
				break;
				case 'mx-activate':
					$status = __( 'Домен подтвержден, но MX-запись не настроена (почта не работает).', 'pdd' );
				break;
				case 'added':
					update_option( 'pdd_domain_added', true );
				break;
			}
			switch( $domain_check['check_results'] ) {
				case 'ok':
					$check_results = __( 'Домен подтвержден, MX-запись настроена (почта работает).', 'pdd' );
				break;
				case 'no-cname,no-file':
					$check_results = __( 'CNAME-запись и секретный файл не найдены.', 'pdd' );
				break;
				case 'bad-cname,bad-file':
					$check_results = __( 'CNAME-запись и секретный файл неверные.', 'pdd' );
				break;
				case 'no-cname,bad-file':
					$check_results = __( 'CNAME-запись не найдена, секретный файл неверный.', 'pdd' );
				break;
				case 'bad-cname,no-file':
					$check_results = __( 'CNAME-запись неверная, секретный файл не найден.', 'pdd' );
				break;
				case 'domain-not-found':
					$check_results = __( 'Домен не найден.', 'pdd' );
				break;
				case 'occupied':
					$check_results = __( 'Имя домена уже используется другим пользователем.', 'pdd' );
				break;
				case 'mx-wrong':
					$check_results = __( 'MX-запись неверная.', 'pdd' );
				break;
				case 'mx-not-found':
					$check_results = __( 'MX-запись не найдена.', 'pdd' );
				break;
			}
		}
	}
	require_once PDD_TMPL_DIR . '/connect-tmpl.php';
}

function pdd_add_pages() {
	if ( get_option( 'pdd_domain_added' ) ) {
		add_menu_page( __( 'Управление почтовыми ящиками', 'pdd' ), __( 'Почтовые ящики', 'pdd' ), 'manage_options', 'all_boxes', 'pdd_all_boxes', 'dashicons-email-alt' );
		add_submenu_page( 'all_boxes', __( 'Все ящики', 'pdd' ), __( 'Все ящики', 'pdd' ), 'manage_options', 'all_boxes', 'pdd_all_boxes' );
		add_submenu_page( 'all_boxes', __( 'Настройки pdd', 'pdd' ), __( 'Настройки', 'pdd' ), 'manage_options', 'settings', 'pdd_settings' );
	} else {
		add_menu_page( __( 'Управление почтовыми ящиками', 'pdd' ), __( 'Почтовые ящики', 'pdd' ), 'manage_options', 'connect_domain', 'pdd_connect_domain' );
	}
}

function pdd_js(){
	switch( $_GET['page'] ) {
		case "all_boxes":
			$file = "all_boxes";
		break;
		case "settings":
			$file = "settings";
		break;
		case "connect_domain":
			$file = "connect_domain";
		break;
		default:
			return;
		break;
	}
	if ( file_exists( PDD_DIR . '/js/' . $file . '.js' ) ) {
		wp_register_script( 'pdd_jquery_tmpl', plugins_url( '/js/jquery.tmpl.js', __FILE__ ) );
		wp_enqueue_script( 'pdd_jquery_tmpl' );
		wp_register_script( 'pdd_' . $file, plugins_url( '/js/' . $file . '.js', __FILE__ ) );
		wp_enqueue_script( 'pdd_' . $file );
	}
}

function pdd_css() {
	switch( $_GET['page'] ) {
		case "all_boxes":
			$file = "all_boxes";
		break;
		case "settings":
			$file = "settings";
		break;
		case "connect_domain":
			$file = "connect_domain";
		break;
		default:
			return;
		break;
	}
	wp_register_style( 'pdd_main_css', plugins_url( '/css/main.css', __FILE__ ) );
	wp_enqueue_style( 'pdd_main_css' );
	wp_register_style( 'pdd_font-awesome_css', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'pdd_font-awesome_css' );
	wp_register_style( 'pdd_css', plugins_url( '/css/' . $file . '.css', __FILE__ ) );
	wp_enqueue_style( 'pdd_css' );
}

function pdd_all_boxes() {
	global $message_codes;

	if ( isset( $_POST['box_uid'] ) && is_array( $_POST['box_uid'] ) && isset( $_POST['pdd_action'] ) ) {
		check_admin_referer( 'mass_action' );
		foreach( $_POST['box_uid'] as $uid ) {
			$filds = array(
				"uid" => $uid,
			);
			switch( $_POST['pdd_action'] ) {
				case"block":
					$filds['enabled'] = "no";
					pdd_exec_command( 'POST', 'email/edit', $filds );
				break;
				case"unblock":
					$filds['enabled'] = "yes";
					pdd_exec_command( 'POST', 'email/edit', $filds );
				break;
				case"delete":
					pdd_exec_command( 'POST', 'email/del', $filds );
				break;
			}
		}
	}

	$userboxes = get_option( 'pdd_userboxes' );
	$page_num = 1;
	if ( isset( $_GET['page_num'] ) ) {
		$page_num = ( (int)$_GET['page_num'] ? (int)$_GET['page_num'] : 1 );
	}
	$json = pdd_exec_command( 'GET', 'email/list', array(
		'page' => $page_num,
		'on_page' => 20
	) );
	$all_boxes = json_decode( $json, true );
	require_once PDD_TMPL_DIR . '/all-boxes-tmpl.php';
}

function pdd_load_textdomain() {
	load_plugin_textdomain( 'pdd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function pdd_activate() {
	add_option( 'pdd_domain', str_replace( 'www.', '', $_SERVER['HTTP_HOST']) );
	add_option( 'pdd_domain_added', false );
	add_option( 'pdd_pdd_token', '' );
	add_option( 'pdd_create_boxes', 'manually' );
	add_option( 'pdd_userboxes', array() );
}

function pdd_deactivation(  $plugin, $network_activation ) {
	delete_option( 'pdd_domain' );
	delete_option( 'pdd_domain_added' );
	delete_option( 'pdd_pdd_token' );
	delete_option( 'pdd_create_boxes' );
	delete_option( 'pdd_userboxes' );
}

function pdd_create_box( $user_id ) {
	$user = get_userdata( $user_id );
	if ( isset( $_POST['pass1'] ) ) {
		$userboxes = get_option( 'pdd_userboxes' );
		$userboxes[] = $user->data->user_login. '@' .get_option( 'pdd_domain' );
		update_option( 'pdd_userboxes', $userboxes );
		pdd_exec_command( 'POST', array(
			'login' => $user->data->user_login,
			'password' => $_POST['pass1']
		) );
	}
}

function pdd_update_box( $user_id, $old_user_data ) {
	check_admin_referer('update-user_' . $user_id);
	$user = get_userdata( $user_id );
	$filds = array(
		"login"		=>$user->data->user_login,
		"enabled"	=>"yes",
	);
	if ( isset( $_POST['first_name'] ) && !empty( $_POST['first_name'] ) ) {
		$filds['iname'] = $_POST['first_name'];
	}
	if ( isset( $_POST['last_name'] ) && !empty( $_POST['last_name'] ) ) {
		$filds['fname'] = $_POST['last_name'];
	}

	if ( isset( $_POST['pass1-text'] ) && !empty( $_POST['pass1-text'] ) ) {
		$filds['password'] = $_POST['pass1-text'];
	}
	pdd_exec_command( 'POST', 'email/edit', $filds );
}

function pdd_delete_box( $user_id ) {
        $user = get_userdata( $user_id );
	$userboxes = get_option( 'pdd_userboxes' );

	$key = array_search( $user->data->user_login. '@' .get_option( 'pdd_domain' ), $userboxes );
	if ( false !== $key && null !== $key ) {
		unset( $userboxes[$key] );
		update_option( 'pdd_userboxes', $userboxes );
		pdd_exec_command( 'POST', 'email/del', array(
			'login' => $user->data->user_login
		) );
	}
}

function pdd_settings() {
	if ( isset( $_POST['save_settings'] ) ) {
		if ( isset( $_POST['create_boxes'] ) ) {
			update_option( 'pdd_create_boxes', 'automatically' );
		} else {
			update_option( 'pdd_create_boxes', 'manually' );
		}
	}
	require_once PDD_TMPL_DIR . '/settings-tmpl.php';
}

function pdd_ajax() {
	if ( current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['delete_box'] ) ) {
			echo pdd_exec_command( 'POST', 'email/del', array(
				'uid' => $_POST['delete_box']
			) );
			exit;
		} else if ( isset( $_POST['update_box'] ) ) {
			check_admin_referer( 'update_box' );
			$box = $_POST['update_box'];
			$filds = array(
				"domain"	=> get_option( 'pdd_domain' ),
				"uid"		=> $box['uid'],
				"enabled"	=> "yes",
			);

			if ( !empty( $box['iname'] ) ) {
				$filds['iname'] = $box['iname'];
			}

			if ( !empty( $box['fname'] ) ) {
				$filds['fname'] = $box['fname'];
			}

			if ( !empty( $box['pass'] ) && !empty( $box['repass'] ) ) {
				$filds['password'] = ( $box['pass'] == $box['repass'] ? $box['pass'] : '');
			}

			if ( isset( $box['blocked'] ) ) {
				$filds['enabled'] = 'no';
			}

			echo pdd_exec_command( 'POST', 'email/edit', $filds );
			exit;
		} else if ( isset( $_POST['add_box'] ) ) {
			check_admin_referer( 'add_box' );
			$login	= ( isset( $_POST['add_box']['login'] ) ? $_POST['add_box']['login'] : "" );
			$pass	= ( isset( $_POST['add_box']['pass'] ) ? $_POST['add_box']['pass'] : "" );
			$repass = ( isset( $_POST['add_box']['repass'] ) ? $_POST['add_box']['repass'] : "" );
			echo pdd_exec_command( 'POST', 'email/add', array(
				'login' => $login,
				'password' => ( $pass == $repass ? $pass : '' )
			) );
			exit;
		}
	}
}

function pdd_exec_command( $type, $command, $params=array(), $pdd_token=false, $timeout=30 ) {
	$params['domain'] = get_option( 'pdd_domain' );

	$args = array(
		'domain'	=> FILTER_SANITIZE_STRING,
		'iname'		=> FILTER_SANITIZE_STRING,
		'fname'		=> FILTER_SANITIZE_STRING,
		'login'		=> FILTER_SANITIZE_STRING,
		'page'		=> FILTER_SANITIZE_NUMBER_INT,
		'on_page'	=> FILTER_SANITIZE_NUMBER_INT,
		'uid'		=> FILTER_SANITIZE_NUMBER_INT,
		'enabled'	=> FILTER_SANITIZE_STRING,
		'password'	=> '',
	);

	if ($pdd_token) {
		$pdd_token = filter_var( $pdd_token, FILTER_SANITIZE_STRING );
	}

	$params = filter_var_array($params, $args);

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_HEADER, false );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'PddToken: ' . ( $pdd_token ? $pdd_token : get_option( 'pdd_pdd_token' ) ) ) );
	if ( $type == "POST" ) {
		curl_setopt( $ch, CURLOPT_URL, 'https://pddimp.yandex.ru/api2/admin/' . $command );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) );
	} else if ( $type == "GET" ) {
		curl_setopt( $ch, CURLOPT_URL, 'https://pddimp.yandex.ru/api2/admin/' . $command . '?' . http_build_query( $params ) );
	}
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	$data = curl_exec( $ch );
	curl_close( $ch );
	return $data;
}























