<?php

/*----------------------------------------------------------------------------------------------------------------------
Plugin Name: WP to PDD
Description: Автоматическая регистрация почтовых ящиков для пользователей вашего сайта, а так же управление ящиками пользователей сайта.
Version: 0.9.4
Author: Денис Бидюков 
Author URI: http://www.dampi.ru/
Plugin URI: http://www.dampi.ru/plagin-wordpress-wp-to-pdd-versiya-0-9-1
----------------------------------------------------------------------------------------------------------------------*/

define( 'PDD_DIR', dirname( __FILE__ ) );
define( 'PDD_TMPL_DIR', PDD_DIR . '/tmpl' );

require_once PDD_DIR . '/functions.php';

$message_codes = array(
	"ok_add"		=> __( 'Почтовый ящик успешно создан', 'pdd' ),
	"ok_delete"		=> __( 'Почтовый ящик успешно удален', 'pdd' ),
	"ok_update"		=> __( 'Почтовый ящик успешно обновлен', 'pdd' ),
	"box_delete"		=> __( 'Удалить почтовый ящик?', 'pdd' ),
	"mass_box_delete"	=> __( 'Удалить выбранные почтовые ящики?', 'pdd' ),
	"occupied"		=> __( 'Ящик с таким логином уже зарегистрирован', 'pdd' ),
	"no_uid_or_login"	=> __( 'Не указан идентификатор или логин', 'pdd' ),
	"bad_domain"		=> __( 'Имя домена не указано или не соответствует RFC', 'pdd' ),
	"badlogin"		=> __( 'Некорректный логин', 'pdd' ),
	"blocked"		=> __( 'Домен заблокирован (например, за спам и т.п.)', 'pdd' ),
	"passwd-empty"		=> __( 'Необходимо указать пароль два раза', 'pdd' ),
	"no_such_domain"	=> __( 'Домен не добавлен', 'pdd' ),
	"unknown"		=> __( 'Произошел временный сбой или ошибка работы API (повторите запрос позже).', 'pdd' ),
	"no_login"		=> __( 'Не передан обязательный параметр.', 'pdd' ),
	"no_token"		=> __( 'Не передан обязательный параметр.', 'pdd' ),
	"no_domain"		=> __( 'Не передан обязательный параметр.', 'pdd' ),
	"no_ip"			=> __( 'Не передан обязательный параметр.', 'pdd' ),
	"prohibited"		=> __( 'Запрещенное имя домена.', 'pdd' ),
	"bad_token"		=> __( 'Передан неверный ПДД-токен.', 'pdd' ),
	"bad_login"		=> __( 'Передан неверный логин.', 'pdd' ),
	"bad_passwd"		=> __( 'Передан неверный пароль.', 'pdd' ),
	"no_password"		=> __( 'Необходимо указать пароль два раза.', 'pdd' ),
	"no_auth"		=> __( 'Не передан заголовок PddToken.', 'pdd' ),
	"not_allowed"		=> __( 'Вам недоступна данная операция (Вы не являетесь администратором этого домена).', 'pdd' ),
	"blocked"		=> __( 'Домен заблокирован (например, за спам и т.п.).', 'pdd' ),
	"occupied"		=> __( 'Имя домена используется другим пользователем.', 'pdd' ),
	"domain_limit_reached"	=> __( 'Превышено допустимое количество подключенных доменов (50).', 'pdd' ),
	"no_reply"		=> __( 'Яндекс.Почта для домена не может установить соединение с сервером-источником для импорта.', 'pdd' ),
);

if ( get_option( 'pdd_pdd_token' ) && 'automatically' == get_option( 'pdd_create_boxes' ) ) {
	add_action( 'user_register', 'pdd_create_box', 10, 1 );
	add_action( 'profile_update', 'pdd_update_box', 10, 2 );
	add_action( 'delete_user', 'pdd_delete_box' );
}

if (isset( $_GET['page'] ) && $_GET['page'] == "all_boxes" ) {
	add_action( 'admin_init', 'pdd_ajax' );
}

add_action( 'admin_print_styles', 'pdd_css' );
add_action( 'admin_print_scripts', 'pdd_js' );
add_action( 'admin_menu', 'pdd_add_pages' );
add_action( 'plugins_loaded', 'pdd_load_textdomain' );
register_activation_hook( __FILE__, 'pdd_activate' );
add_action( 'deactivated_plugin', 'pdd_deactivation', 10, 2 );
