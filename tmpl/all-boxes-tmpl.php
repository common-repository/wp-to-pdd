<div class="page-title">
	<h1>
		<?php _e( 'Почтовые ящики', 'pdd' ); ?>
		 <a target="_blank" href="http://mail.yandex.ru/for/<?php echo get_option( 'pdd_domain' ); ?>" class="page-title-action">
			<?php _e( 'Войти в почту', 'pdd' ); ?>
		</a>
	</h1>
</div>
<script type="text/javascript">
	var message_codes = <?php echo json_encode($message_codes); ?>;
	var all_boxes = <?php echo json_encode($all_boxes); ?>;
	var userboxes = <?php echo json_encode($userboxes); ?>;
</script>

<div id="msgs">
</div>

<div class="mail-boxes">
	<div class="mail-boxes-form">
	</div>
	<div class="mail-boxes-list">
		<form action="" method="post">
			<?php wp_nonce_field( 'mass_action' ) ?>
			<div class="mail-boxes-list-actions">
				<div class="mail-box-actions">
					<select name="pdd_action" id="pdd_action">
						<option value="-1"> <?php _e( 'Действия', 'pdd' ); ?> </option>
						<option value="block"> <?php _e( 'Заблокировать', 'pdd' ); ?></option>
						<option value="unblock"> <?php _e( 'Разблокировть', 'pdd' ); ?></option>
						<option value="delete"> <?php _e( 'Удалить', 'pdd' ); ?> </option>
					</select>
					<input type="submit" id="doaction" class="button action" value="<?php _e( 'Применить', 'pdd' ); ?>" />
				</div>
				<div class="mail-box-pagenav">
					<?php _e( sprintf( 'Показано %d из %d', $all_boxes['found'], $all_boxes['total']), 'pdd' ); ?>
					<?php if ( $all_boxes['page'] > 1 ) { ?>
						<a class="fa-angle-double-left" href="/wp-admin/admin.php?page=all_boxes"></a>
						<a class="fa-angle-left" href="/wp-admin/admin.php?page=all_boxes&page_num=<?php echo ($page_num - 1); ?>"></a>
					<?php } else { ?>
						<span class="fa-angle-double-left"></span>
						<span class="fa-angle-left"></span>
					<?php } ?>
					<span style="color: #000;"><?php echo $page_num; ?></span>
					<?php if ( $all_boxes['pages'] > $page_num ) { ?>
						<a class="fa-angle-right" href="/wp-admin/admin.php?page=all_boxes&page_num=<?php echo ($page_num + 1); ?>"></a>
						<a class="fa-angle-double-right" href="/wp-admin/admin.php?page=all_boxes&page_num=<?php echo $all_boxes['pages']; ?>"></a>
					<?php } else { ?>
						<span class="fa-angle-right"></span>
						<span class="fa-angle-double-right"></span>
					<?php } ?>
				</div>
			</div>
			<table class="box-list-table">
				<thead>
					<tr>
						<td>
							<input class="select-all-boxes" type="checkbox" />
						</td>
						<th>
							<?php _e( 'Адрес', 'pdd' ); ?>
						</th>
						<th>
							<?php _e( 'Имя', 'pdd' ); ?>
						</th>
						<th>
							<span class="fa fa-wrench head"></span>
						</th>
						<th width="10%">
							<span title="<?php _e( 'Готовность к работе почтового ящика.', 'pdd' ); ?>" class="fa fa-heart head"></span>
						</th>
						<th>
							<?php _e( 'Состояние', 'pdd' ); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td>
							<input class="select-all-boxes" type="checkbox" />
						</td>
						<th>
							<?php _e( 'Адрес', 'pdd' ); ?>
						</th>
						<th>
							<?php _e( 'Имя', 'pdd' ); ?>
						</th>
						<th>
							<span class="fa fa-wrench head"></span>
						</th>
						<th>
							<span title="<?php _e( 'Готовность к работе почтового ящика.', 'pdd' ); ?>" class="fa fa-heart head"></span>
						</th>
						<th>
							<?php _e( 'Состояние', 'pdd' ); ?>
						</th>
					</tr>
				</tfoot>
				<tbody id="the-list">
				</tbody>
			</table>
		</form>
	</div>
</div>
<script type="text/html" id="box-item">
		<tr class="box-item" data-login="${login}">
			<th>
				<input id="select-box-${uid}" type="checkbox" name="box_uid[]" value="${uid}" />
			</th>
			<td>
				${login}
				<div class="row-actions">
					<span class='edit'>
						<a class="submitedit" href="#" data-login="${login}" data-uid="${uid}" data-iname="${iname}" data-enabled="${enabled}" data-fname="${fname}" title="<?php _e( 'Редактировать этот ящик', 'pdd' ); ?>">
							<?php _e( 'Изменить', 'pdd' ); ?>
						</a> | 
					</span>
					<span class='trash'>
						<a class="submitdelete" data-uid="${uid}" title="<?php _e( 'Переместить этот ящик в корзину', 'pdd' ); ?>" href="#">
							<?php _e( 'Удалить', 'pdd' ); ?>
						</a> | 
					</span>
				</div>
			</td>
			<td>
				${iname} ${fname}
			</td>
			<td>
				<span class="{%if userboxes.indexOf(login) >= 0%}fa-cogs{%else%}fa-hand-stop-o{%/if%}" title="{%if userboxes.indexOf(login) >= 0%}<?php _e( 'Ящик добавлен автоматически', 'pdd' ); ?>{%else%}<?php _e( 'Ящик добавлен вручную', 'pdd' ); ?>{%/if%}"></span>
			</td>
			<td>
				<span title="{%if ready == "no"%}<?php _e( 'Пользовательское соглашение не принято, ящик не используется.', 'pdd' ); ?>{%else%}<?php _e( 'Пользовательское соглашение принято, ящик используется.', 'pdd' ); ?>{%/if%}" class="fa {%if ready == "no"%}fa-heart-o{%else%}fa-heartbeat{%/if%} red"></span>
			</td>
			<td>
				{%if enabled == "yes"%}
					<?php _e( 'Работает', 'pdd' ); ?>
				{%else%}
					<?php _e( 'Заблокирован', 'pdd' ); ?>
				{%/if%}
			</td>
		</tr>
</script>

<script type="text/html" id="box-item-default">
		<tr class="box-item-default">
			<td colspan="4" align="center">
				<h2><?php _e( 'Нет почтовых ящиков', 'pdd' ); ?></h2>
			</td>
		</tr>
</script>

<script type="text/html" id="form-add-box">
	<p>
		<h2><?php _e( 'Новый почтовый ящик', 'pdd' ); ?></h2>
	</p>
	<form action="" id="box-form" method="post">
		<?php wp_nonce_field( 'add_box' ) ?>
		<p>
			<input type="text" name="add_box[login]" placeholder="<?php _e( 'Логин', 'pdd' ); ?>"/>@<?php echo get_option( 'pdd_domain' ); ?>
		</p>
		<p>
			<input type="password" name="add_box[pass]" placeholder="<?php _e( 'Пароль', 'pdd' ); ?>"/>
			<input type="password" name="add_box[repass]" placeholder="<?php _e( 'Пароль ещё раз', 'pdd' ); ?>"/>
		</p>
		<p>
			<input type="submit" class="button button-primary" name="add" value="<?php _e( 'Добавить', 'pdd' ); ?>"/>
			<input type="reset" value="<?php _e( 'Отмена', 'pdd' ); ?>"/>
		</p>
	</form>
</script>

<script type="text/html" id="form-update-box">
	<p>
		<h2>${login}</h2>
	</p>
	<form action="" id="box-form" method="post">
		<?php wp_nonce_field( 'update_box' ) ?>
		<input type="hidden" name="update_box[uid]" value="${uid}"/>
		<p>
			<input type="text" name="update_box[iname]" placeholder="<?php _e( 'Имя', 'pdd' ); ?>" value="${iname}"/>
		</p>
		<p>
			<input type="text" name="update_box[fname]" placeholder="<?php _e( 'Фамилия', 'pdd' ); ?>" value="${fname}"/>
		</p>
		<p>
			<input type="password" name="update_box[pass]" placeholder="<?php _e( 'Пароль', 'pdd' ); ?>"/>
			<input type="password" name="update_box[repass]" placeholder="<?php _e( 'Пароль ещё раз', 'pdd' ); ?>"/>
		</p>
		<p>
			<label>
				<input {%if enabled == "no"%}checked{%/if%} type="checkbox" name="update_box[blocked]" value="1" />
				 <?php _e( 'Заблокировать ящик', 'pdd' ); ?>
			</label>
		</p>
		<p>
			<input type="submit" class="button button-primary" name="update" value="<?php _e( 'Сохранить', 'pdd' ); ?>"/>
			<input type="reset" class="resetedit" value="<?php _e( 'Отмена', 'pdd' ); ?>"/>
		</p>
	</form>
</script>
