<div class="page-title">
	<h1>
		<?php _e( 'Настройки', 'pdd' ); ?>
	</h1>
</div>


<form action="" method="post">
	<table width="80%" cellpadding="5">
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
	</table>
	<p>
		<input style="vertical-align: middle;" class="button button-primary" type="submit" name="save_settings" value="<?php _e( 'Сохранить', 'pdd' ); ?>"/>
	</p>
</form>
