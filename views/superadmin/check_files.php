<?php
use yii\helpers\Html;
$this->title = 'Записи в б.д., которым нет соответствующих файлов в файловой системе';
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
	function findFiles() {
		var quantity = $('#folders_qty').val();
		document.location.href = '/check_files/find_files/' + quantity;
	}
</script>
<style type="text/css">
	.folders_list {
		line-height: 1.7;
	}
	.folders {
		padding: 1px 2px;
		border: solid 1px #8F8F8F;
		border-radius: 3px;
		white-space: nowrap;
	}
	#folders_qty {
		height: 20px;
		width: 70px;
		padding: 0px;
		position: relative;
		top: 2px;
	}
	.files_table tr:hover {
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
	.files_table td, .files_table th {
		padding: 2px;
	}
</style>
<div class="site-checkfiles">
	<a href="/check_files/db">Неактуальное в базе</a> &nbsp;
	[ <b><a href="/check_files/files">Неактуальное в файловой системе</a></b> ]
	<h3><?= Html::encode($this->title); ?></h3>
	<form name="files_actions" method="post" action="/check_files/files">
		<div class="folders_list"><b>Проверенные папки</b>:
<?php
foreach($checked as $f) {
?>
			<span class="folders"><?= Html::encode($f); ?> <a href="/check_files/recheck_folder/<?= str_replace('"', '&quot;', $f); ?>">x</a></span>
<?php
}
?>
			<span class="folders"><a href="/check_files/recheck_all_folders" onclick="return confirm('Подтвердите');">Сбросить все</a></span>
			<br />
			<span class="folders">
				<input type="number" id="folders_qty" value="<?= $queryLimitDel; ?>" />
				<a href="/check_files/find_files/<?= $queryLimitDel; ?>" onclick="findFiles(); return false;">Запуск проверки</a> (непроверенных: <?= count($unchecked); ?>)
			</span>
		</div>
		<a href="/check_files/del_all_files" onclick="return confirm('Удалить все файлы из списка?');">Удалить все файлы</a> &nbsp;
		<a href="/check_files/del_all_records" onclick="return confirm('Очистить список?');">Очистить список</a>
		<div class="pages">
			Страницы: <?= $pager; ?>
		</div>
		<table border="1" style="border-collapse: collapse; margin: 5px 0px;" class="files_table">
			<tr style="background-color: #AABDE6;">
				<th><input type="checkbox" onclick="selectAll(this, 'files_ids[]');" /></th>
				<th>Добавлено</th>
				<th>Удалено</th>
				<th>Тип</th>
				<th>Путь к файлу</th>
				<th>&nbsp;</th>
			</tr>
<?php
foreach($files as $file) {
?>
			<tr>
				<td><input type="checkbox" name="files_ids[]" value="<?= $file['id']; ?>" /></td>
				<td><?= $file['added']; ?></td>
				<td><?= $file['deleted']; ?></td>
				<td><?= Html::encode($file['file_type']); ?></td>
				<td><?= Html::encode($file['file_path']); ?></td>
				<td>
					<a href="/check_files/del_file/<?= $file['id']; ?>">Удалить файл</a> &nbsp;
					<a href="/check_files/del_record/<?= $file['id']; ?>">Удалить запись</a>
					<a href="#" onclick="showHideReason(<?= $file['id']; ?>); return false;">Причина</a>
				</td>
			</tr>
			<tr style="display: none;" id="row_<?= $file['id']; ?>">
				<td colspan="6"><?= empty($file['reason']) ? 'Нет данных' : Html::encode($file['reason']); ?></td>
			</tr>
<?php
}
?>
		</table>
		<select name="action" onchange="this.form.action = '/check_files/' + this.value;">
			<option value="files">Действие...</option>
			<option value="del_files">Удалить файлы</option>
			<option value="del_records">Удалить записи</option>
		</select> &nbsp;
		<input type="submit" value="Вперёд!" />
	</form>
	<div class="pages">
		Страницы: <?= $pager; ?>
	</div>
</div>