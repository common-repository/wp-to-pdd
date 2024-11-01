<div class="page-title">
	<h1>
		<?php _e( 'Подключение домена', 'pdd' ); ?>
	</h1>
</div>
<?php
	if ( get_option( 'pdd_pdd_token' ) ) {
		if ( $status ) {
			echo '<div class="updated"><p>' . $status . '</p></div>';
		}
		if ( $check_results ) {
			echo '<div class="updated"><p>' . $check_results . '</p></div>';
		}
		if ( $check_file ) {
			echo '<div class="updated"><p>' . sprintf( __( 'Для подтверждения домена в корневом каталоге сайта был создан файл <b>%s</b>, не удаляйте его', 'ymfdp' ), $check_file ) . '</p></div>';
		}
?>
<a href="/wp-admin/admin.php?page=<?php echo ( get_option( 'pdd_domain_added' ) ? 'all_boxes' : 'connect_domain' ); ?>">
	<p>
		<button class="button button-primary"><?php ( get_option( 'pdd_domain_added' ) ? _e( 'Перейти к списку ящиков', 'pdd' ) : _e( 'Обновить', 'pdd' ) ); ?></button>
	</p>
</a>
<?php
	} else {
		if ($error) {
			echo '<div class="error"><p>' . $error . '</p></div>';
		}
		echo '<p>' . sprintf( __( 'Для начала необходимо <a href="https://pdd.yandex.ru/?domain=%s&actionStatus=success#%s" target="_blank">подключить домен</a> к сервису Яндекс Почта для домена.', 'pdd' ), get_option( 'pdd_domain' ), get_option( 'pdd_domain' ) ) . '<br />';
		echo sprintf( __( 'Затем, после того, как подтвердитедомен, необходимо <a href="https://pddimp.yandex.ru/api2/admin/get_token?domain=%s" target="_blank">получить токен</a> и вставить его в поле ниже.', 'pdd' ), get_option( 'pdd_domain' ) ) . '</p>';
?>
<form action="" method="post">
	<table width="70%" cellpadding="10">
		<tr valign="top">
			<td colspan="2">
				<input style="vertical-align: middle;" size="50" type="text" name="pdd_token" placeholder="<?php _e( 'Вставьте полученный токен', 'pdd' ); ?>" />
				<input style="vertical-align: middle;" class="button button-primary" type="submit" name="connect_domain" value="<?php _e( 'Сохранить', 'pdd' ); ?>"/>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<?php _e( 'Автоматическая регистрация', 'pdd' ); ?>
			</td>
			<td>
				<label>
					<input <?php echo ('automatically' == get_option( 'pdd_create_boxes' ) ? 'checked' : '' ); ?> type="checkbox" name="create_boxes" value="1" />
					 <?php _e( 'Автоматически создавать ящики для новых пользователей', 'pdd' ); ?>
				</label>
				<div id="tab-panel-overview" class="help-tab-content active">
					<p>
						<?php _e( 'Почтовые ящики будут автоматически создаваться при регистрации пользователя, изменяться при обновлении профиля и удаляться при удалении пользователя', 'pdd' ); ?>
					</p>
				</div>
			</td>
		<tr>
		<tr valign="top">
			<td>
				<?php _e( 'Подтверждение домена', 'pdd' ); ?>
			</td>
			<td>
				<label>
					<input <?php echo ( isset( $_POST['create_check_file'] ) ? 'checked' : '' ); ?> type="checkbox" name="create_check_file" value="1" />
					 <?php _e( 'Создать html-файл в корне сайта для подтверждения домена', 'pdd' ); ?>
				</label>
				<div id="tab-panel-overview" class="help-tab-content active">
					<p>
						<?php _e( 'Если домен уже подтвержден, то нет смысла отмечать эту опцию', 'pdd' ); ?>
					</p>
				</div>
			</td>
		<tr>
	</table>
</form>
<?php
	}
?>
