<?php
use yii\helpers\Html;
$this->title = 'Файлы, о которых есть записи в б.д., но которые не нашлись на диске';
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
	function showHideReason(id) {
		$('#row_' + id).toggle();
	}
	function selectAll(from, to) {
		var inputs = document.getElementsByTagName('input');
		var checked = from.checked;
		for(var i in inputs) {
			if(inputs[i].name && inputs[i].name === to) {
				inputs[i].checked = checked;
			}
		}
	}
	function findRecords() {
		var quantity = $('#records_qty').val();
		document.location.href = '/check_files/find_records/' + quantity;
	}
</script>
<style type="text/css">
	.folders {
		padding: 1px 2px;
		border: solid 1px #8F8F8F;
		border-radius: 3px;
	}
	#records_qty {
		height: 20px;
		width: 70px;
		padding: 0px;
		position: relative;
		top: 2px;
	}
	.db_table tr:hover {
		background-color: #AABDE6;
	}
	.site-checkfiles .pages {
		margin: 5px 0px;
		line-height: 1.6;
	}
	.site-checkfiles .pages a {
		border: solid 1px #337ab7;
		border-radius: 3px;
		padding: 0px 2px;
	}
	.site-checkfiles .pages a:hover {
		border-color: #BB0000;
		color: #BB0000;
	}
	.site-checkfiles .pages span {
		border: solid 1px #8F8F8F;
		border-radius: 3px;
		padding: 0px 2px;
	}
	.db_table td, .db_table th {
		padding: 2px;
	}
</style>
<div class="site-checkfiles">
	[ <b><a href="/check_files/db">Неактуальное в базе</a></b> ] &nbsp;
	<a href="/check_files/files">Неактуальное в файловой системе</a>
	<h3><?= Html::encode($this->title) ?></h3>
	<form name="db_actions" method="post" action="/check_files/db">
		<div class="folders_list">
			<b>Проверено записей до:</b>
<?php
foreach($dData as $key => $val) {
?>
			<span class="folders"><?= $key; ?>: <?= $val; ?> <a href="/check_files/recheck_records/<?= $key; ?>">x</a></span>
<?php
}
?>
			<span class="folders"><a href="/check_files/recheck_all_records" onclick="return confirm('Подтвердите');">Сбросить все</a></span>
			<span class="folders">
				<input type="number" id="records_qty" value="<?= $queryLimit; ?>" />
				<a href="/check_files/find_records/<?= $queryLimit; ?>" onclick="findRecords(); return false;">Запуск проверки</a>
			</span>
		</div>
		<a href="/check_files/del_all_db_rows" onclick="return confirm('Удалить все строки из базы, перечисленные в списке?'); return false;">Удалить все найденные строки</a> &nbsp;
		<a href="/check_files/del_all_founded_records" onclick="return confirm('Очистить список?'); return false;">Очистить список</a>
		<div class="pages">
			Страницы: <?= $pager; ?>
		</div>
		<table border="1" style="border-collapse: collapse; margin: 5px 0px;" class="db_table">
			<tr style="background-color: #AABDE6;">
				<th><input type="checkbox" onclick="selectAll(this, 'records_ids[]');" /></th>
				<th>Добавлено</th>
				<th>Удалено</th>
				<th>Таблица</th>
				<th>ID</th>
				<th>Поле</th>
				<th>Значение</th>
				<th>Путь к файлу</th>
				<th>&nbsp;</th>
			</tr>
<?php
foreach($records as $record) {
?>
			<tr>
				<td><input type="checkbox" name="records_ids[]" value="<?= $record['id']; ?>" /></td>
				<td><?= $record['added']; ?></td>
				<td><?= $record['deleted']; ?></td>
				<td><?= $record['table_name']; ?></td>
				<td><?= $record['record_id']; ?></td>
				<td><?= $record['hash_field']; ?></td>
				<td><?= htmlspecialchars($record['hash_value']); ?></td>
				<td><?= htmlspecialchars($record['file_path']); ?></td>
				<td>
					<a href="/check_files/del_db_row/<?= $record['id']; ?>">Удалить запись</a> &nbsp;
					<a href="/check_files/del_founded_row/<?= $record['id']; ?>">Удалить информацию о записи</a> &nbsp;
					<a href="#" onclick="showHideReason(<?= $record['id']; ?>); return false;">Причина</a>
				</td>
			</tr>
			<tr style="display: none;" id="row_<?= $record['id']; ?>">
				<td colspan="9"><?= $record['reason']; ?></td>
			</tr>
<?php
}
?>
		</table>
		<select name="action" onchange="this.form.action = '/check_files/' + this.value;">
			<option value="db" selected="selected">Действие...</option>
			<option value="del_db_rows">Удалить записи из базы</option>
			<option value="del_founded_rows">Удалить информацию о записях</option>
		</select> &nbsp;
		<input type="submit" value="Вперёд!" />
	</form>
	<div class="pages">
		Страницы: <?= $pager; ?>
	</div>
</div>