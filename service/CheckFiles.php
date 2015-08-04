<?php
namespace app\service;

use Suffra\Config as SuffraConfig;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\db\Connection;

class CheckFiles {
	
	public static function config() {
		if(!defined('PATH')) { define('PATH', SuffraConfig::getBasePath()); }
		require_once SuffraConfig::getBasePath() . '/core/classes/config.class.php';
        return [
            'suffraPath'      => SuffraConfig::getBasePath(),
            'storagePath'     => SuffraConfig::getBasePath() . '/upload/cron_temp',
            'queryLimit'      => 10000,
            'queryLimitDel'   => 10,
            'uGrpCount'       => 1000,
            'perPage'         => 200,
            'sec_before_exit' => 3
        ];
    }

    public static function funcNames() {
        return [
            'cms_user_files'  => 'moveToTempFiles',
            'cms_user_photos' => 'moveToTempPhoto',
            'cms_user_video'  => 'moveToTempVideo',
            'cms_goods'       => 'moveToTempGoods'
        ];
    }

    public static function getData() {
		$config = self::config();
		$path = $config['storagePath'] . '/check_files_data.php';
		if(file_exists($path)) {
			require $path;
		} else {
			$data = array(
				'files' => 0,
				'photo' => 0,
				'video' => 0,
				'goods' => 0,
				'ch_avatars' => 0
			);
		}
		return $data;
	}
	
	public static function saveData($data) {
		$config = self::config();
		$str = "<?php\n\$data = " . var_export($data, true) . ";\n?>";
		file_put_contents($config['storagePath'] . '/check_files_data.php', $str);
	}

	public static function getDir($userId) {
		$config = self::config();
		$grp = ((int) ($userId / $config['uGrpCount']) + 1) * $config['uGrpCount'];
		return "{$config['suffraPath']}/upload/users/{$grp}/{$userId}";
	}
	
	public static function createThumb($origPath, $thumbPath, $rules) {
		self::log("Создаю превью для файла {$origPath}");
        $types = [
            'jpeg' => 'jpg',
            'jpg'  => 'jpg',
            'png'  => 'png',
            'gif'  => 'gif'
        ];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
		$mime = explode(';', $finfo->file($origPath));
		$mimeParts = explode('/', $mime[0]);
		if(!(count($mimeParts) === 2 and $mimeParts[0] === 'image' and array_key_exists($mimeParts[1], $types))) {
			self::log("Файл не является изображением\nmime = " . var_export($mime, true));
			return false;
		}
		try {
			self::log('Пытаюсь прочитать оригинал...');
			switch($types[$mimeParts[1]]) {
				case 'jpg':
					@$orig = imagecreatefromjpeg($origPath);
					break;
				case 'png':
					@$orig = imagecreatefrompng($origPath);
					break;
				case 'gif':
					@$orig = imagecreatefromgif($origPath);
					break;
				default:
					$orig = false;
			}
		} catch(Exception $e) {
			$config = self::config();
			file_put_contents($config['storagePath'] . '/check.log', "\nНе удалось создать превью для изображения {$origPath}\n{$e}\n", FILE_APPEND);
			self::log("Не удалось прочитать оригинал, функция imagecreatefrom... сгенерировала exception: {$e}");
			return false;
		}
		if(!$orig) {
			self::log("Не удалось прочитать оригинал, функция imagecreatefrom... вернула false");
			return false;
		}
		$origWidth = imagesx($orig);
		$origHeight = imagesy($orig);
		if($rules['proportional']) {
			if($rules['by_sides'] === 1) {
				$biggest = max($origWidth, $origHeight);
				$k = $biggest > $rules['max_size'] ? ($rules['max_size'] / $biggest) : 1;
			} else {
				$kx = $origWidth > $rules['width'] ? ($rules['width'] / $origWidth) : 1;
				$ky = $origHeight > $rules['height'] ? ($rules['height'] / $origHeight) : 1;
				$k = min($kx, $ky);
			}
			$thumbWidth = (int) ($origWidth * $k);
			$thumbHeight = (int) ($origHeight * $k);
			$shiftX = $shiftY = 0;
		} else {
			$least = min($origWidth, $origHeight);
			$shiftX = (int) (($origWidth - $least) / 2);
			$shiftY = (int) (($origHeight - $least) / 2);
			if($rules['by_sides'] === 1) {
				$k = $least > $rules['max_size'] ? ($rules['max_size'] / $least) : 1;
			} else {
				$rulesSide = min($rules['width'], $rules['height']);
				$k = $least > $rulesSide ? ($rulesSide / $least) : 1;
			}
			$thumbWidth = $thumbHeight = (int) ($least * $k);
		}
		self::log("Оригинал: ширина = {$origWidth}, высота = {$origHeight}; MAX превью: " . ($rules['by_sides'] === 1 ? "сторона = {$rules['max_size']}" : "ширина = {$rules['width']}, высота = {$rules['height']}") . '; сохраняем пропорции: ' . ($rules['proportional'] ? 'Да' : "Нет, сдвиг X = {$shiftX}, сдвиг Y = {$shiftY}") . "; k = {$k}; рассчитанное превью: ширина = {$thumbWidth}, высота = {$thumbHeight}");
		if($k < 1 or $shiftX or $shiftY) {
			$thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
			self::log('Создаю пустое изображение для превью... ' . ($thumb ? 'Успешно' : 'Не получилось'));
			if($types[$mimeParts[1]] === 'png') {
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
			}
			$resampled = imagecopyresampled($thumb, $orig, 0, 0, $shiftX, $shiftY, $thumbWidth, $thumbHeight, $origWidth - $shiftX * 2, $origHeight - $shiftY * 2);
			self::log('Выполняю imagecopyresampled... ' . ($resampled ? 'Успешно' : 'не получилось'));
		} else {
			$thumb = $orig;
			if($types[$mimeParts[1]] === 'png') {
				imagealphablending($thumb, false);
				imagesavealpha($thumb, true);
			}
			self::log('Размер оригинала меньше или такой же, какой необходим для превью. Копирую идентификатор оригинала в идентификатор превью.');
		}
		switch($types[$mimeParts[1]]) {
			case 'jpg':
				$saved = imagejpeg($thumb, $thumbPath, 80);
				break;
			case 'png':
				$saved = imagepng($thumb, $thumbPath);
				break;
			case 'gif':
				$saved = imagegif($thumb, $thumbPath);
				break;
			default:
				$saved = false;
		}
		imagedestroy($orig);
		if($thumb and is_resource($thumb)) {
			@imagedestroy($thumb);
		}
		self::log("Сохраняю превью по пути {$thumbPath}... " . ($saved ? 'Успешно' : 'Не получилось'));
		return $saved;
	}
	
	public static function moveToTempFiles($files) {
		self::moveToTemp($files, 'cms_user_files');
		$actions = array('ACTION_ADD_FILE', 'ACTION_ADD_FILE_TO_MARKET', 'ACTION_ADD_LIKE_FILES', 'ACTION_ADD_COMMENT_FILES');
		foreach($actions as $action) {
			self::deleteAction(array('action' => $action, 'ids' => array_keys($files)));
		}
	}

	public static function moveToTempPhoto($photos) {
		self::moveToTemp($photos, 'cms_user_photos');
		$actions = array('ACTION_ADD_PHOTO', 'ACTION_ADD_LIKE_PHOTO', 'ACTION_ADD_COMMENT_PHOTO');
		foreach($actions as $action) {
			self::deleteAction(array('action' => $action, 'ids' => array_keys($photos)));
		}
	}

	public static function moveToTempVideo($videos) {
		self::moveToTemp($videos, 'cms_user_video');
		$actions = array('ACTION_ADD_VIDEO', 'ACTION_ADD_LIKE_VIDEO', 'ACTION_ADD_COMMENT_VIDEO');
		foreach($actions as $action) {
			self::deleteAction(array('action' => $action, 'ids' => array_keys($videos)));
		}
	}

	public static function moveToTempGoods($goods) {
		self::moveToTemp($goods, 'cms_goods');
		$actions = array('ACTION_ADD_VOTING', 'ACTION_ADD_ANSWER', 'ACTION_VOTING_FEE');
		foreach($actions as $action) {
			self::deleteAction(array('action' => $action, 'ids' => array_keys($goods)));
		}
	}

	public static function moveToTemp($data, $table) {
		$config = self::config();
		$fields = self::getTableFields($table);
		$conn = \Yii::$app->getDb();
		$rows = array();
		foreach($data as $item) {
			$rowData = array();
			foreach($fields as $title => $type) {
				switch($type) {
					case 1:
						$rowData[] = isset($item[$title]) ? ((int) $item[$title]) : 0;
						break;
					case 2:
						$rowData[] = isset($item[$title]) ? (is_null($item[$title]) ? 'NULL' : $conn->quoteValue($item[$title])) : "''";
						break;
					case 3:
						$rowData[] = isset($item[$title]) ? (is_null($item[$title]) ? 'NULL' : $conn->quoteValue($item[$title])) : 'NOW()';
						break;
					case 4:
						$rowData[] = isset($item[$title]) ? ((float) str_replace(',', '.', $item[$title])) : 0;
						break;
					default:
						$rowData[] = "''";
				}
			}
			$rows[] = implode(', ', $rowData);
		}
		if($rows) {
			self::createRemoved($table);
			$fieldsNames = array_keys($fields);
			$tableName = $conn->quoteTableName($table . '_removed');
			foreach($fieldsNames as $key => $val) {
				$fieldsNames[$key] = $conn->quoteColumnName($val);
			}
			file_put_contents($config['storagePath'] . '/check_files.sql.log', "REPLACE. fields:\n" . var_export($fieldsNames, true) . "\nrows:\n" . var_export($rows, true) . "\n\n", FILE_APPEND);
			(new Query)->createCommand()->setSql("REPLACE INTO {$tableName} (" . implode(', ', $fieldsNames) . ') VALUES (' . implode('), (', $rows) . ')')->execute();
			$dataIds = array_keys($data);
			file_put_contents($config['storagePath'] . '/check_files.sql.log', "DELETE. dataIds:\n" . var_export($dataIds, true) . "\n\n", FILE_APPEND);
			(new Query)->createCommand()->delete($table, '`id` IN (' . implode(', ', $dataIds) . ')')->execute();
		}
	}
	
	/**
	 * Получает информацию о проверенных папках
	 * @return array - список проверенных папок
	 */
	public static function getDelData() {
		$config = self::config();
		$path = $config['storagePath'] . '/del_files_data.php';
		if(file_exists($path)) {
			require $path;
		} else {
			$data = array(
				'checked' => array()
			);
		}
		return $data;
	}
	
	/**
	 * Сохранить информацию о проверенных папках
	 * @param array $data - список проверенных папок для сохранения
	 */
	public static function saveDelData($data) {
		$config = self::config();
		$str = "<?php\n\$data = " . var_export($data, true) . ";\n?>";
		file_put_contents($config['storagePath'] . '/del_files_data.php', $str);
	}
	
	/**
	 * deleteAction - удаляет события из ленты. Проверяет, есть ли другие события в группах с удаляемыми событиями. Если есть -
	 * проверяет, какая будет видимость группы после удаления и при необходимости обновляет. Если нет - удаляет группы. После
	 * чего удаляет записи о самих событиях.
	 * @param array $data - тип и id удаляемых событий: {action: ТИП_СОБЫТИЯ, ids: [id1, id2, ...]}
	 * @return void
	 */
	public static function deleteAction($data) {
		$config = self::config();
		if(!isset($data['action']) or ($type = self::getActionType($data['action'])) === false or !($ids = self::getIds($data))) {
			return;
		}
		$res = (new Query)->createCommand()->setSql("SELECT `group_id`, `allow_who` FROM `cms_user_actions` WHERE `type` = {$type} AND `id_obj` IN (" . implode(', ', $ids) . ")")->query();
		$groups = array();
		if($res and $res->rowCount > 0) {
			while($row = $res->read()) {
				$allow = (int) $row['allow_who'];
				if(!array_key_exists($row['group_id'], $groups)) {
					$groups[$row['group_id']] = $allow;
				} elseif($allow < $groups[$row['group_id']]) {
					$groups[$row['group_id']] = $allow;
				}
			}
		}
		$groupsAllow = self::getGroupsMinAllow(array_keys($groups), $ids);
		foreach($groupsAllow as $groupId => $groupAllow) {
			if($groups[$groupId] < $groupAllow) {
				(new Query)->createCommand()->setSql("UPDATE `cms_user_actions_groups` SET `min_allow` = {$groupAllow} WHERE `id` = {$groupId}")->execute();
			}
			unset($groups[$groupId]);
		}
		if($groups) {
			$sql = "DELETE FROM `cms_user_actions_groups` WHERE `id` IN (" . implode(', ', array_keys($groups)) . ")";
			(new Query)->createCommand()->setSql($sql)->execute();
			file_put_contents($config['storagePath'] . '/check_files.sql.log', $sql . "\n\n", FILE_APPEND);
		}
		$sql = "DELETE FROM `cms_user_actions` WHERE `type` = {$type} AND `id_obj` IN (" . implode(', ', $ids) . ')';
		(new Query)->createCommand()->setSql($sql)->execute();
		file_put_contents($config['storagePath'] . '/check_files.sql.log', $sql . "\n\n", FILE_APPEND);
	}

	/**
	 * getIds - Проверяет, есть ли в массиве элемент ids и, если есть, преобразует все его "подэлементы" к int и возвращает их
	 * @param array $data - массив, в котором искать элемент ids со списком id
	 * @return mixed - массив id, преобразованных к int или false, если такого элемента нет
	 */
	public static function getIds($data) {
		if(!isset($data['ids']) or !is_array($data['ids']) or count($data['ids']) < 1) {
			return false;
		}
		$ids = array();
		foreach($data['ids'] as $val) {
			$ids[] = (int) $val;
		}
		return $ids;
	}

	/**
	 * getActionType - Получить код события по его названию
	 * @param string $action - название события
	 * @return mixed - код события или false, если такого нет
	 */
	public static function getActionType($action) {
		$types = self::getActionsTypes();
		if(isset($types[$action][0])) {
			return $types[$action][0];
		} else {
			return false;
		}
	}

	/**
	 * getActionTypes - возвращает массив соответствий между названиями событий и их числовыми кодами
	 * @return array - массив соответствий между названиями и кодами событий
	 */
	public static function getActionsTypes() {
		return array(
			'ACTION_ADD_FILE' => array(1),
			'ACTION_ADD_PHOTO' => array(2),
			'ACTION_ADD_VIDEO' => array(3),
			'ACTION_ADD_FRIEND' => array(4),
			'ACTION_ADD_FILE_TO_MARKET' => array(5),
			'ACTION_ADD_LIKE' => array(6),
			'ACTION_ADD_MUSIC_IN_PLAYER' => array(7),
			'ACTION_ADD_VOTING' => array(8),
			'ACTION_ADD_ANSWER' => array(9),
			'ACTION_CHANGE_STATUS' => array(10),
			'ACTION_VOTING_FEE' => array(11),
			'ACTION_CHANGE_PROFILE_PHOTO' => array(12),
			'ACTION_GIFT_RECEIPT' => array(13),
			'ACTION_GIFT_GIVING' => array(14),
			'ACTION_ADD_COMMENT' => array(18),
			'ACTION_ADD_LIKE_FILES' => array(101),
			'ACTION_ADD_LIKE_PHOTO' => array(102),
			'ACTION_ADD_LIKE_VIDEO' => array(103),
			'ACTION_ADD_LIKE_MUSIC' => array(107),
			'ACTION_ADD_LIKE_NEWS' => array(115),
			'ACTION_ADD_LIKE_NEWS_ARCHIVE' => array(116),
			'ACTION_ADD_LIKE_HUMOR' => array(117),
			'ACTION_ADD_COMMENT_FILES' => array(201),
			'ACTION_ADD_COMMENT_PHOTO' => array(202),
			'ACTION_ADD_COMMENT_VIDEO' => array(203),
			'ACTION_ADD_COMMENT_MUSIC' => array(207),
			'ACTION_ADD_COMMENT_NEWS' => array(215),
			'ACTION_ADD_COMMENT_NEWS_ARCHIVE' => array(216),
			'ACTION_ADD_COMMENT_HUMOR' => array(217)
		);
	}

	/**
	 * getGroupsMinAllow - определение минимального значения видимости групп событий по событиям группы
	 * @param array $groupsIds - ID групп, которым нужно определить видимость
	 * @param array $without - ID объектов, которые не нужно учитывать при определении минимального значения видимости группы
	 * @return array - минимальное значение видимости для каждой запрошенной группы
	 */
	public static function getGroupsMinAllow($groupsIds, $without = array()) {
		if(!$groupsIds) { return array(); }
		$sql = 'SELECT `group_id`, MIN(`allow_who`) AS `min_allow` FROM `cms_user_actions` WHERE `group_id` IN (' . implode(', ', $groupsIds) . ')';
		if($without) {
			$sql .= ' AND `id_obj` NOT IN (' . implode(', ', $without) . ')';
		}
		$sql .= ' GROUP BY `group_id`';
		$groupsAllow = array();
		$res = (new Query)->createCommand()->setSql($sql)->query();
		if($res and $res->rowCount) {
			while($row = $res->read()) {
				$groupsAllow[$row['group_id']] = (int) $row['min_allow'];
			}
		}
		return $groupsAllow;
	}

	/**
	 * getPagesLinks - генерирует ссылки на страницы
	 * @param integer $page - текущая страница, на которой находимся
	 * @param integer $pages - сколько всего страниц
	 * @param string $add - произвольные параметры, которые добавить к ссылке
	 * @return string - html-код ссылок на страницы
	 */
	public static function getPagesLinks($page, $pages, $add = '') {
		$res = '';
		for($i = 1; $i <= $pages; ++$i) {
			if($i === $page) {
				$res .= ' <span>' . $i . '</span>';
			} else {
				$res .= ' <a href="?page=' . $i . ($add ? ('&amp;' . $add) : '') . '">' . $i . '</a>';
			}
		}
		return $res;
	}

	/**
	 * checkPhotoExists - Проверяет наличие изображения, если не находит, то отбрасывает расширение файла, если оно
	 * имеется или приписывает, если его нет, после чего ещё раз проверяет наличие изображения
	 * @param string $path - путь к папке, в которой должно находиться проверяемое изображение
	 * @param string $image - название файла проверямого изображения
	 * @return array {exists: bool, path: string, upd: string}
	 */
	public static function checkPhotoExists($path, $image) {
		$fullPath = $path . '/' . $image;
		self::log("Проверяю файл {$fullPath}");
		$checked = array($fullPath);
		$pos = strrpos($image, '.');
		$extExists = $pos > 0;
		$ext = strtolower(substr($image, $pos + 1));
		if(file_exists($fullPath)) {
			if($extExists and $ext === 'jpg') {
				$upd = false;
				self::log('Файл найден. Расширение jpg есть.');
			} else {
				$image = $extExists ? substr($image, 0, $pos) : $image;
				$renamed = rename($fullPath, $path . '/' . $image . '.jpg');
				if($renamed) {
					$image .= '.jpg';
					$fullPath = $path . '/' . $image;
					$upd = array('imageurl' => $image);
				}
				self::log('Файл найден. Расширения нет или != jpg. Переименовываем... ' . ($renamed ? 'Успешно' : 'Не получилось'));
			}
			return array('exists' => true, 'path' => $fullPath, 'upd' => $upd, 'checked' => $checked);
		}
		if($extExists) {
			$image = substr($image, 0, $pos);
			self::log("Файл не найден. В б.д. хранится с расширением. Проверяем, есть ли без расширения ({$image})");
			$withExt = false;
		} else {
			$image .= '.jpg';
			self::log("Файл не найден. В б.д. хранится без расширения. Проверяем, есть ли с расширением ({$image})");
			$withExt = true;
		}
		$fullPath = $path . '/' . $image;
		$checked[] = $fullPath;
		self::log("Проверяю файл {$fullPath}");
		if(file_exists($fullPath)) {
			if($withExt) {
				self::log("Файл найден. На диске файл с расширением jpg - обновим запись в б.д.");
			} else {
				$renamed = rename($fullPath, $fullPath . '.jpg');
				self::log("Файл найден. На диске файл без расширения. Пытаемся переименовать файл...");
				if($renamed) {
					$image .= '.jpg';
					$fullPath .= '.jpg';
				}
				self::log('Переименование ' . ($renamed ? 'успешно' : 'не удалось') . '. Обновим запись в б.д.');
			}
			return array('exists' => true, 'path' => $fullPath, 'upd' => array('imageurl' => $image), 'checked' => $checked);
		} else {
			self::log("И такой файл не найден");
			return array('exists' => false, 'path' => $fullPath . ($withExt ? '' : '.jpg'), 'upd' => false, 'checked' => $checked);
		}
	}
	
	public static function getRow($sql) {
		$res = (new Query)->createCommand()->setSql($sql)->query();
		if($res and $res->rowCount) {
			return $res->read();
		}
	}
	
	public static function createRemoved($table) {
		$data = self::getRow('SHOW CREATE TABLE `' . $table . '`');
		$create = substr($data['Create Table'], 0, 12) . ' IF NOT EXISTS' . str_replace("`{$table}`", "`{$table}_removed`", substr($data['Create Table'], 12));
		(new Query)->createCommand()->setSql($create)->execute();
	}
	
	public static function getTableFields($table) {
		$res = (new Query)->createCommand()->setSql('DESCRIBE `' . $table . '`')->query();
		$fields = array();
		if($res and $res->rowCount) {
			while($item = $res->read()) {
				$fields[$item['Field']] = self::determineFieldType($item['Type']);
			}
		}
		return $fields;
	}
	
	public static function determineFieldType($type) {
		if(stripos($type, 'int') !== false or stripos($type, 'bit') !== false) {
			// Целые числа
			$determinedType = 1;
		} elseif(stripos($type, 'char') !== false or stripos($type, 'text') !== false or stripos($type, 'blob') !== false or stripos($type, 'enum') !== false or stripos($type, 'set') !== false) {
			// Строки текста
			$determinedType = 2;
		} elseif(stripos($type, 'date') !== false or stripos($type, 'time') !== false or stripos($type, 'year') !== false) {
			// Даты в виде строк текста
			$determinedType = 3;
		} elseif(stripos($type, 'float') !== false or stripos($type, 'double') !== false or stripos($type, 'real') !== false or stripos($type, 'dec') !== false or stripos($type, 'numeric') !== false) {
			// Числа с точкой
			$determinedType = 4;
		}
		return $determinedType;
	}
	
	public static function log($text) {
		$config = self::config();
		file_put_contents($config['suffraPath'] . '/cache/logs/check_files.log', $text . "\n", FILE_APPEND);
	}
	
	public static function getUpdStr($upd) {
		$data = array();
		foreach($upd as $key => $val) {
			$data[] = "{$key} = '{$val}'";
		}
		return implode(', ', $data);
	}
	
	public static function CheckFiles($queryLimit) {
		$timeStart = microtime(true);
		ini_set('max_execution_time', 300);
		ini_set('memory_limit', '1024M');
		$cfg = self::config();
		$execTime = (int) ini_get('max_execution_time');
		if($queryLimit < 1) {
			$queryLimit = $cfg['queryLimit'];
		}
		if(!file_exists($cfg['storagePath']) or !is_dir($cfg['storagePath'])) {
			mkdir($cfg['storagePath'], 0777, true);
		}
		$lockFile = $cfg['storagePath'] . '/check.lock';
		if(!(file_exists($lockFile) or file_put_contents($lockFile, '1'))) {
			return 'Отсутствует файл блокировки параллельного запуска скрипта и создать файл не удалось';
		}
		$fLock = fopen($lockFile, 'r+');
		if(!flock($fLock, LOCK_EX | LOCK_NB)) {
			return 'Не удалось установить блокировку на файл ' . $lockFile . ' - видимо, копия скрипта уже запущена';
		}
		self::log("Проверяю наличие файлов на диске, о которых есть записи в б.д. Запуск: " . date('d.m.Y, H:i:s'));
		// Правила генерации превью/обложек файлов
		$filesPrevsRules = array(
			'thumb' => array(
				'by_sides' => 1,
				'max_size' => 150,
				'proportional' => true
			),
			'prev' => array(
				'by_sides' => 1,
				'max_size' => 600,
				'proportional' => true
			)
		);
		// Последовательность типов фото от самого большого к самому меньшему
		$photoSequence = array('large', 'medium', 'full', 'small', 'tiny');
		$log = date('d.m.Y, H:i:s') . " check_files.php\n==============================";
		if(!defined('VALID_CMS')) {
			define('VALID_CMS', true);
		}
		require $cfg['suffraPath'] . '/plugins/p_upload/model.php';
		$data = self::getData();
		$config = \cms_model_userchfiles::getDefaultConfig();
		$processed = 0;
		$moveToTemp = array('files' => array(), 'photo' => array(), 'video' => array(), 'goods' => array());
		$recordsForDelete = array();
		// Проверяем загруженные файлы
		$maxId = (int) (new Query)->select('MAX(`id`)')->from('cms_user_files')->where('`isFolder` = 0')->scalar();
		$stopOn = $execTime - $cfg['sec_before_exit'];
		if($data['files'] < $maxId) {
			self::log("Проверяю файлы.");
			$files = ArrayHelper::index((new Query)->select('*')->from('cms_user_files')->where("`id` > {$data['files']} AND `isFolder` = 0")->orderBy('id')->limit($queryLimit)->all(), 'id');
			$log .= "\nПроверяю файлы. Последний проверенный: {$data['files']}; максимальный имеющийся: {$maxId}; загрузил: " . count($files);
			$createdThumbs = $createdPrevs = $droppedPrevs = $createdThumbsCovers = $copiedPrevsCovers = 0;
			foreach($files as $file) {
				if($processed >= $queryLimit or (microtime(true) - $timeStart) >= $stopOn) {
					break;
				}
				++$processed;
				$data['files'] = (int) $file['id'];
				$fileUserId = (int) ($file['owner'] ? $file['owner'] : $file['user_id']);
				$dir = self::getDir($fileUserId);
				$filePath = $dir . '/files/' . $file['rand'];
				self::log("Проверяю файл {$filePath} (user_id: {$file['user_id']}, owner: {$file['owner']}, rand: {$file['rand']})");
				if(!file_exists($filePath)) {
					$moveToTemp['files'][$file['id']] = $file;
					$recordsForDelete[] = array(
						'table' => 'cms_user_files',
						'id' => (int) $file['id'],
						'hash_field' => 'rand',
						'hash_value' => $file['rand'],
						'file' => $filePath,
						'reason' => 'При проверке данного файла функция file_exists() вернула false'
					);
					self::log('Функция file_exists() вернула false, добавляю файл в список на удаление.');
					continue;
				}
				$upd = array();
				$fileSize = filesize($filePath);
				$file['filesize'] = (int) $file['filesize'];
				self::log('Файл найден.');
				if($fileSize !== $file['filesize']) {
					$upd['filesize'] = $fileSize;
					self::log(" Размер файла на диске ({$fileSize}) отличается от размера файла в б.д. ({$file['filesize']})");
				}
				$cat = (int) $file['category_file'];
				if($cat === 3 or $file['prev']) {
					$smallPath = $dir . '/files/small';
					if(!file_exists($smallPath) or !is_dir($smallPath)) {
						$opres = mkdir($smallPath, 0777, true);
						self::log("Файл является картинкой или имеет обложку, но папки для превью ({$smallPath}) нет. Пытаюсь создать... " . ($opres ? 'Успешно' : 'mkdir вернула false'));
					}
				}
				if($cat === 3) {
					// Картинка
					if($file['prev']) {
						$thumbPath = "{$smallPath}/thumb_{$file['prev']}";
						$prevPath = "{$smallPath}/prev_{$file['prev']}";
						self::log("Файл является картинкой, в базе есть название превью (prev: {$file['prev']})");
					} else {
						$prev = md5($file['rand'] . rand(10e16, 10e20) . time());
						$upd['prev'] = $prev;
						$thumbPath = "{$smallPath}/thumb_{$prev}";
						$prevPath = "{$smallPath}/prev_{$prev}";
						self::log("Файл является картинкой, но в базе нет названия превью. Генерирую: prev = {$prev}");
					}
					if(!$file['prev'] or !file_exists($thumbPath)) {
						self::log("Файл превью {$thumbPath} отсутствует на диске. Пытаюсь создать");
						self::createThumb($filePath, $thumbPath, $filesPrevsRules['thumb']);
						++$createdThumbs;
					}
					if(!$file['prev'] or !file_exists($prevPath)) {
						self::log("Файл превью {$prevPath} отсутствует на диске. Пытаюсь создать");
						self::createThumb($filePath, $prevPath, $filesPrevsRules['prev']);
						++$createdPrevs;
					}
				} elseif($file['prev']) {
					// Обложка
					$thumbPath = "{$smallPath}/thumb_{$file['prev']}";
					$prevPath = "{$smallPath}/prev_{$file['prev']}";
					$thumbExists = file_exists($thumbPath);
					$prevExists = file_exists($prevPath);
					self::log("Файл имеет обложку (prev: {$file['prev']})");
					if(!$prevExists and !$thumbExists) {
						self::log("Отсутствуют оба размера превью: {$thumbPath}, {$prevPath}; убираю оба размера превью");
						$upd['prev'] = '';
						++$droppedPrevs;
					} elseif($prevExists and !$thumbExists) {
						self::log("Присутствует prev_, нет thumb_; пытаюсь создать превью");
						self::createThumb($prevPath, $thumbPath, $filesPrevsRules['thumb']);
						++$createdThumbsCovers;
					} elseif($thumbExists and !$prevExists) {
						$copied = copy($thumbPath, $prevPath);
						self::log("Присутствует thumb_, нет prev_; копирую thumb в prev... " . ($copied ? 'Успешно' : 'Не получилось'));
						++$copiedPrevsCovers;
					}
				}
				if($upd) {
					$updated = (new Query)->createCommand()->update('cms_user_files', $upd, "`id` = {$file['id']}")->execute();
					self::log("Обновляю запись о файле в базе: " . self::getUpdStr($upd) . '; обновлено: ' . $updated);
				}
			}
			$log .= "\nПроверил файлов: {$processed}; добавил в список на удаление: " . count($moveToTemp['files']) . "; создал превью: {$createdPrevs}; создал миниатюр: {$createdThumbs}; удалил обложек: {$droppedPrevs}; создал миниатюр обложек: {$createdThumbsCovers}; копировал превью обложек: {$copiedPrevsCovers}";
		}
		// Проверяем загруженные фотографии пользователей
		if($processed < $queryLimit and (microtime(true) - $timeStart) < $stopOn) {
			$maxId = (int) (new Query)->select('MAX(`id`)')->from('cms_user_photos')->where('`isFolder` = 0')->scalar();
			if($data['photo'] < $maxId) {
				$photosRules = array();
				foreach($config['photo'] as $type => $params) {
					$photosRules[$type] = array(
						'by_sides' => (isset($params['width']) and isset($params['height'])) ? 2 : 1,
						'proportional' => (isset($params['mode']) and $params['mode'] === 0) ? true : false
					);
					if($photosRules[$type]['by_sides'] === 1) {
						$photosRules[$type]['max_size'] = isset($params['width']) ? $params['width'] : (isset($params['height']) ? $params['height'] : 0);
					} else {
						$photosRules[$type]['width'] = $params['width'];
						$photosRules[$type]['height'] = $params['height'];
					}
				}
				$photos = ArrayHelper::index((new Query)->select('*')->from('cms_user_photos')->where("`id` > {$data['photo']} AND `isFolder` = 0")->orderBy('id')->limit($queryLimit)->all(), 'id');
				$log .= "\nПроверяю фото. Последний проверенный: {$data['photo']}; максимальный имеющийся: {$maxId}; загрузил: " . count($photos);
				$checkedPhotos = $createdDirs = $copiedMissing = $createdThumbs = $comPhotosFileExists = 0;
				foreach($photos as $photo) {
					if($processed >= $queryLimit or (microtime(true) - $timeStart) >= $stopOn) {
						break;
					}
					self::log("Проверяю фото, imageurl = {$photo['imageurl']}");
					++$processed;
					++$checkedPhotos;
					$upd = array();
					$data['photo'] = (int) $photo['id'];
					$photoUserId = (int) $photo['user_id'];
					$dir = self::getDir($photoUserId);
					$photosPath = $dir . '/photos';
					$photosFiles = array();
					$needSave = true;
					$fullName = '';
					foreach($photoSequence as $type) {
						$photosTypePath = $photosPath . '/' . $type;
						if(!file_exists($photosTypePath) or !is_dir($photosTypePath)) {
							$created = mkdir($photosTypePath, 0777, true);
							self::log("Директория {$photosTypePath} отсутствует. Пытаемся создать... " . ($created ? 'Успешно' : 'Не удалось'));
							++$createdDirs;
						}
						$photoPath = $photosTypePath . '/' . $photo['imageurl'];
						if($needSave) {
							$fullName = $photoPath;
							$needSave = false;
						}
						$photoExists = self::checkPhotoExists($photosTypePath, $photo['imageurl']);
						$photosFiles[$type] = array('path' => $photoExists['path'], 'exists' => $photoExists['exists'], 'checked' => implode(', ', $photoExists['checked']));
						if($photoExists['upd']) {
							foreach($photoExists['upd'] as $key => $val) {
								$upd[$key] = $val;
							}
						}
						++$comPhotosFileExists;
					}
					// Перебираем фотографии от самой большой до самой меньшей, смотрим, все ли присутствуют и пересоздаём отсутствующие при возможности
					$exists = false;
					$outer = 0;
					foreach($photosFiles as $type => $photoData) {
						if($photoData['exists']) {
							self::log("Фото {$photoData['path']} есть. outer = {$outer}. Запускаем внутренний цикл для проверки и восстановления других размеров фото");
							$exists = true;
							$inner = 0;
							foreach($photosFiles as $innerType => $innerData) {
								if($innerData['exists']) {
									self::log("Фото {$innerData['path']} есть. inner = {$inner}");
								} else {
									// Если отсутствует фото, которое должно быть больше, чем самое большое найденное
									if($inner < $outer) {
										// то копируем найденное на место отсутствующего
										$copied = copy($photoData['path'], $innerData['path']);
										self::log("Фото {$innerData['path']} отсутствует. Оно должно быть больше, чем самое большое найденное (inner = {$inner}). Копирую... " . ($copied ? 'Успешно' : 'Не получилось'));
										++$copiedMissing;
									// Если отсутствует фото, которое должно быть меньше, чем найденное
									} else {
										// то создаём отсутствующее превью
										self::createThumb($photoData['path'], $innerData['path'], $photosRules[$innerType]);
										self::log("Фото {$innerData['path']} отсутствует. Оно должно быть меньше, чем самое большое найденное (inner = {$inner}). Создаю превью");
										++$createdThumbs;
									}
								}
								++$inner;
							}
							break;
						} else {
							self::log("Фото {$photoData['path']} отсутствует. outer = {$outer}");
						}
						++$outer;
					}
					if($upd) {
						$updated = (new Query)->createCommand()->update('cms_user_photos', $upd, "`id` = {$photo['id']}")->execute();
						self::log("Обновляю запись о фото в базе: " . self::getUpdStr($upd) . '; обновлено: ' . $updated);
					}
					if(!$exists) {
						self::log("Не нашлась копия фото ни одного из размеров. Добавляю в список на удаление");
						$moveToTemp['photo'][$photo['id']] = $photo;
						$reason = array();
						foreach($photosFiles as $type => $photoData) {
							$reason[] = $type . ': ' . $photoData['checked'];
						}
						$recordsForDelete[] = array(
							'table' => 'cms_user_photos',
							'id' => (int) $photo['id'],
							'hash_field' => 'imageurl',
							'hash_value' => $photo['imageurl'],
							'file' => $fullName,
							'reason' => 'При проверке данной фотографии функция file_exists() вернула false для каждого из размеров изображения (' . htmlspecialchars(implode('; ', $reason)) . ')'
						);
						continue;
					}
					self::log("==========");
				}
				$log .= "\nПроверил фото: {$checkedPhotos}; создал папок: {$createdDirs}; скопировал недостающих размеров: {$copiedMissing}; создал превью: {$createdThumbs}; добавил в список на удаление: " . count($moveToTemp['photo']) . ", выполнено file_exists(): {$comPhotosFileExists}";
			}
		}
		// Проверяем загруженное видео пользователей
		if($processed < $queryLimit and (microtime(true) - $timeStart) < $stopOn) {
			$maxId = (int) (new Query)->select('MAX(`id`)')->from('cms_user_video')->scalar();
			if($data['video'] < $maxId) {
				self::log('Проверяю видео');
				$videos = ArrayHelper::index((new Query)->select('*')->from('cms_user_video')->where("`id` > {$data['video']}")->orderBy('id')->limit($queryLimit)->all(), 'id');
				$log .= "\nПроверяю видео. Последнее проверенное: {$data['video']}; максимальное имеющееся: {$maxId}; загрузил: " . count($videos);
				$videosPath = $cfg['suffraPath'] . '/upload/video';
				$needLoad = true;
				$checkedVideo = $withoutImages = $loadingErrors = $loadedImages = $updatedVideo = $noImageLoaded = 0;
				foreach($videos as $video) {
					if($processed >= $queryLimit or (microtime(true) - $timeStart) >= $stopOn) {
						break;
					}
					++$processed;
					++$checkedVideo;
					$data['video'] = (int) $video['id'];
					$file = $videosPath . '/' . $video['video_img'];
					self::log("Проверяю файл превью к видео {$file} (video_img = \"{$video['video_img']}\")");
					if(empty($video['video_img']) or $video['video_img'] === 'no_image.png' or !file_exists($file)) {
						++$withoutImages;
						self::log('Файл не найден или no_image.png');
						if($needLoad) {
							if(!defined('PATH')) { define('PATH', $cfg['suffraPath']); }
							require_once PATH . '/core/classes/config.class.php';
							require_once PATH . '/core/classes/db.class.php';
							require_once PATH . '/core/classes/video.class.php';
							require_once PATH . '/core/cms.php';
							require_once PATH . '/app/Suffra/Core/Event/EventDispatcher.php';
							require_once PATH . '/app/Suffra/App.php';
							require_once PATH . '/core/classes/memcache.class.php';
							require_once PATH . '/core/classes/logger.class.php';
							$videoProcessor = \cmsVideo::getInstance();
							self::log('Подключил класс cmsVideo');
							$needLoad = false;
						}
						$videoData = $videoProcessor->process($video['video_url']);
						self::log("Запрашиваю обработку видео \"{$video['video_url']}\"... " . ($videoData ? 'Ответ получен' : 'Пустой ответ') . ":\n" . var_export($videoData, true));
						if($videoData['error']) {
							self::log("cmsVideo->process() не смог обработать видео (ошибка: {$videoData['error']}). Добавляю в список на удаление");
							++$loadingErrors;
							$moveToTemp['video'][$video['id']] = $video;
							$recordsForDelete[] = array(
								'table' => 'cms_user_video',
								'id' => (int) $video['id'],
								'hash_field' => 'video_img',
								'hash_value' => $video['video_img'],
								'file' => $file,
								'reason' => 'При проверке превью данного видео функция file_exists вернула false или video_img = no_image.png. При попытке получить новое превью с видеохостинга функция cmsVideo::process вернула ошибку: ' . htmlspecialchars($videoData['error']) . ' ; <a href="' . str_replace('"', '&quot;', $video['video_url']) . '" target="_blank">' . htmlspecialchars($video['video_url']) . '</a>'
							);
						} elseif(isset($videoData['filename']) and $videoData['filename'] === 'no_image.png') {
							self::log("cmsVideo->process() вернул {$videoData['filename']}. Добавляю в список на удаление");
							++$noImageLoaded;
							$moveToTemp['video'][$video['id']] = $video;
							$recordsForDelete[] = array(
								'table' => 'cms_user_video',
								'id' => (int) $video['id'],
								'hash_field' => 'video_img',
								'hash_value' => $video['video_img'],
								'file' => $file,
								'reason' => 'При проверке превью данного видео функция file_exists вернула false или video_img = no_image.png. При попытке получить новое превью с видеохостинга функция cmsVideo::process вернула filename = ' . htmlspecialchars($videoData['filename']) . ' ; <a href="' . str_replace('"', '&quot;', $video['video_url']) . '" target="_blank">' . htmlspecialchars($video['video_url']) . '</a>'
							);
						} else {
							if($videoData['filename']) {
								++$loadedImages;
								self::log("cmsVideo->process() загрузил изображение {$videoData['filename']}");
							}
							$upd = array('video_img' => $videoData['filename']);
							self::log('Обновляю запись о видео в б.д. (' . self::getUpdStr($upd) . ')...');
							if(($updated = (new Query)->createCommand()->update('cms_user_video', $upd, '`id` = ' . ((int) $video['id']))->execute())) {
								$updatedVideo += $updated;
							}
							self::log(($updated ? 'Успешно' : 'Не удалось') . ' (' . var_export($updated, true) . ')');
						}
					}
				}
				$log .= "\nПроверил видео: {$checkedVideo}; нашёл без картинок: {$withoutImages}; загрузил картинок: {$loadedImages}; ошибок загрузки: {$loadingErrors}; обновил видео: {$updatedVideo}, добавил в список на удаление: " . count($moveToTemp['video']);
			}
		}
		if($processed < $queryLimit and (microtime(true) - $timeStart) < $stopOn) {
			$maxId = (int) (new Query)->select('MAX(`id`)')->from('cms_goods')->scalar();
			if($data['goods'] < $maxId) {
				$goods = ArrayHelper::index((new Query)->select('*')->from('cms_goods')->where("`id` > {$data['goods']}")->orderBy('id')->limit($queryLimit)->all(), 'id');
				$goodsPath = $cfg['suffraPath'] . '/upload/goods';
				$log .= "\nПроверяю опросы. Последний проверенный: {$data['goods']}; максимальный имеющийся: {$maxId}; загрузил: " . count($goods);
				$checkedGoods = $withoutImages = 0;
				foreach($goods as $good) {
					if($processed >= $queryLimit or (microtime(true) - $timeStart) >= $stopOn) {
						break;
					}
					++$processed;
					++$checkedGoods;
					$data['goods'] = (int) $good['id'];
					$file = $goodsPath . '/' . $good['img'];
					if(empty($good['img']) or !file_exists($file)) {
						++$withoutImages;
						$moveToTemp['goods'][$good['id']] = $good;
						$recordsForDelete[] = array(
							'table' => 'cms_goods',
							'id' => (int) $good['id'],
							'hash_field' => 'img',
							'hash_value' => $good['img'],
							'file' => $file,
							'reason' => 'При проверке изображения к данному опросу функция file_exists вернула false'
						);
					}
				}
				$log .= "\nПроверил опросов: {$checkedGoods}; нашёл без картинок: {$withoutImages}; добавил в список на удаление: " . count($moveToTemp['goods']);
			}
		}
		if($processed < $queryLimit and (microtime(true) - $timeStart) < $stopOn) {
			$maxId = (int) (new Query)->select('MAX(`id`)')->from('cms_user_actions_groups')->where('`type` = 12')->scalar();
			if($data['ch_avatars'] < $maxId) {
				$avatars = ArrayHelper::index((new Query)->select('*')->from('cms_user_actions')->where("`type` = 12 AND `group_id` > {$data['ch_avatars']}")->orderBy('group_id')->limit($queryLimit)->all(), 'id');
				$log .= "\nПроверяю события смены аватары. Последнее проверенное: {$data['ch_avatars']}; максимальное имеющееся: {$maxId}; загрузил: " . count($avatars);
				$checkedAvatars = $withoutImages = $deletedActions = $deletedGroups = 0;
				foreach($avatars as $avatar) {
					if($processed >= $queryLimit or (microtime(true) - $timeStart) >= $stopOn) {
						break;
					}
					++$processed;
					++$checkedAvatars;
					$data['ch_avatars'] = (int) $avatar['group_id'];
					$fData = json_decode($avatar['data'], true);
					$groupId = (int) $avatar['group_id'];
					if(!isset($fData['photo']) or !file_exists($cfg['suffraPath'] . $fData['photo'])) {
						++$withoutImages;
						$d1 = (new Query)->createCommand()->delete('cms_user_actions', "`user_id` = {$avatar['user_id']} AND `type` = {$avatar['type']} AND `id_obj` = {$avatar['id_obj']} AND `additional_id` = {$avatar['additional_id']}")->execute();
						$d2 = (new Query)->createCommand()->delete('cms_user_actions_groups', "`id` = {$groupId}")->execute();
						$deletedActions += $d1;
						$deletedGroups += $d2;
					}
				}
				$log .= "\nПроверил событий смены аватары: {$checkedAvatars}; нашёл без картинок: {$withoutImages}; удалил событий: {$deletedActions}; удалил групп: {$deletedGroups}";
			}
		}
		// Записываем в таблицу найденных пустых строк
		$groupping = 100;
		$counter = 0;
		$sqlParts = array();
		$fields = array('added', 'table_name', 'record_id', 'hash_field', 'hash_value', 'file_path', 'reason');
		foreach($recordsForDelete as $recordData) {
			$sqlParts[] = array(
				new \yii\db\Expression('NOW()'),
				$recordData['table'],
				$recordData['id'],
				$recordData['hash_field'],
				$recordData['hash_value'],
				$recordData['file'],
				$recordData['reason']
			);
			++$counter;
			if($counter >= $groupping) {
				(new Query)->createCommand()->batchInsert('records_for_delete', $fields, $sqlParts)->execute();
				$counter = 0;
				$sqlParts = array();
			}
		}
		if($counter > 0) {
			(new Query)->createCommand()->batchInsert('records_for_delete', $fields, $sqlParts)->execute();
		}
		self::saveData($data);
		if(!flock($fLock, LOCK_UN)) {
			return 'Не удалось снять блокировку с файла ' . $lockFile;
		}
		fclose($fLock);
		$log .= "\nВремя выполнения: " . round(microtime(true) - $timeStart, 3) . " сек.\n";
		file_put_contents($cfg['storagePath'] . '/check.log', $log . "\n\n", FILE_APPEND);
		self::log("\n\n");
		header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Проверено записей: <?= $processed; ?></title>
		<script type="text/javascript">
			function showHideLog() {
				var div = document.getElementById('logdiv');
				div.style.display = div.style.display === 'none' ? '' : 'none';
			}
		</script>
	</head>
	<body>
		<b>Проверено записей: <?= $processed; ?></b> &nbsp;
		<a href="/check_files/db">К таблице записей для удаления</a> &nbsp;
		<div style="white-space: pre-wrap;" id="logdiv">
<?= $log; ?>
		</div>
	</body>
</html>
<?php
	}
	
	public static function DelFiles($queryLimit) {
		$timeStart = microtime(true);
		ini_set('max_execution_time', 300);
		ini_set('memory_limit', '1024M');
		$cfg = self::config();
		$execTime = (int) ini_get('max_execution_time');
		if($queryLimit < 1) {
			$queryLimit = $cfg['queryLimit'];
		}
		if(!file_exists($cfg['storagePath']) or !is_dir($cfg['storagePath'])) {
			mkdir($cfg['storagePath'], 0777, true);
		}
		$lockFile = $cfg['storagePath'] . '/del.lock';
		if(!(file_exists($lockFile) or file_put_contents($lockFile, '1'))) {
			return 'Отсутствует файл блокировки параллельного запуска скрипта и создать файл не удалось';
		}
		$fLock = fopen($lockFile, 'r+');
		if(!flock($fLock, LOCK_EX | LOCK_NB)) {
			return 'Не удалось установить блокировку на файл ' . $lockFile . ' - видимо, копия скрипта уже запущена';
		}
		if(!defined('VALID_CMS')) {
			define('VALID_CMS', true);
		}
		require $cfg['suffraPath'] . '/plugins/p_upload/model.php';
		$data = self::getDelData();
		$config = \cms_model_userchfiles::getDefaultConfig();
		$usersPath = $cfg['suffraPath'] . '/upload/users';
		$files = array_slice(scandir($usersPath), 2);
		if(!$files) {
			return('Не найдено ни одной пользовательской папки!');
		}
		$checkedUsersDirs = 0;
		$filesForDelete = array();
		$stopOn = $execTime - $cfg['sec_before_exit'];
		foreach($files as $file) {
			if((microtime(true) - $timeStart) >= $stopOn) {
				break;
			}
			$dir = $usersPath . '/' . $file;
			if(is_dir($dir) and !in_array($file, $data['checked'])) {
				$usersDirs = array_slice(scandir($dir), 2);
				foreach($usersDirs as $usrDir) {
					$userId = (int) $usrDir;
					$userDir = $dir . '/' . $usrDir;
					$userFiles = $userDir . '/files';
					$userPhotos = $userDir . '/photos';
					if(!(file_exists($userFiles) and is_dir($userFiles))) {
						mkdir($userFiles, 0777, true);
					}
					if(!(file_exists($userFiles . '/small') and is_dir($userFiles . '/small'))) {
						mkdir($userFiles . '/small', 0777);
					}
					if(!(file_exists($userPhotos) and is_dir($userPhotos))) {
						mkdir($userPhotos, 0777, true);
					}
					foreach($config['photo'] as $type => $photoTypeData) {
						$userPhotosType = $userPhotos . '/' . $type;
						if(!(file_exists($userPhotosType) and is_dir($userPhotosType))) {
							mkdir($userPhotosType, 0777);
						}
					}
					$userFilesDB = ArrayHelper::index((new Query)->select('*')->from('cms_user_files')->where("`user_id` = {$userId} AND `isFolder` = 0")->orderBy('id')->all(), 'id');
					$userFileTitlesDB = $userFilePrevsDB = array();
					foreach($userFilesDB as $id => $userFileDBData) {
						if(!empty($userFileDBData['rand'])) {
							$userFileTitlesDB[] = $userFileDBData['rand'];
						}
						if(!empty($userFileDBData['prev'])) {
							$userFilePrevsDB[] = $userFileDBData['prev'];
						}
					}
					$userPhotosDB = ArrayHelper::index((new Query)->select('*')->from('cms_user_photos')->where("`user_id` = {$userId} AND `isFolder` = 0")->orderBy('id')->all(), 'id');
					$userPhotoTitlesDB = array();
					foreach($userPhotosDB as $id => $userPhotoDBData) {
						if(!empty($userPhotoDBData['imageurl'])) {
							$userPhotoTitlesDB[] = $userPhotoDBData['imageurl'];
						}
					}
					$userFilesFS = array_slice(scandir($userFiles), 2);
					foreach($userFilesFS as $userFileFS) {
						$userFileFSPath = $userFiles . '/' . $userFileFS;
						if(is_file($userFileFSPath) and !in_array($userFileFS, $userFileTitlesDB)) {
							$filesForDelete[] = array('type' => 'file', 'file' => $userFileFSPath, 'reason' => 'Файл "' . htmlspecialchars($userFileFS) . '" находится в папке файлов пользователя #' . $userId . ', но в б.д. среди файлов пользователя #' . $userId . ' нет соответствующей записи');
						}
					}
					$userFilesPrevsFS = array_slice(scandir($userFiles . '/small'), 2);
					foreach($userFilesPrevsFS as $userFilePrevFS) {
						$userFilePrevFSPath = $userFiles . '/small/' . $userFilePrevFS;
						$fileDBPart = str_replace(array('thumb_', 'prev_'), '', $userFilePrevFS);
						if(is_file($userFilePrevFSPath) and !in_array($fileDBPart, $userFilePrevsDB)) {
							$filesForDelete[] = array('type' => 'file_prev', 'file' => $userFilePrevFSPath, 'reason' => 'Файл "' . htmlspecialchars($userFilePrevFS) . '" находится в папке для превью к файлам пользователя #' . $userId . ', но в б.д. среди превью к файлам пользователя #' . $userId . ' нет записи "' . htmlspecialchars($fileDBPart) . '"');
						}
					}
					foreach($config['photo'] as $type => $photoTypeData) {
						$userPhotosType = $userPhotos . '/' . $type;
						$userPhotosTypeFS = array_slice(scandir($userPhotosType), 2);
						foreach($userPhotosTypeFS as $userPhotoTypeFS) {
							$userPhotoTypeFSPath = $userPhotosType . '/' . $userPhotoTypeFS;
							if(is_file($userPhotoTypeFSPath) and !in_array($userPhotoTypeFS, $userPhotoTitlesDB)) {
								$filesForDelete[] = array('type' => 'photo', 'file' => $userPhotoTypeFSPath, 'reason' => 'Файл "' . htmlspecialchars($userPhotoTypeFS) . '" находится в папке ' . htmlspecialchars($type) . ' фотографий пользователя #' . $userId . ', но в б.д. среди фотографий пользователя #' . $userId . ' нет соответствующей записи');
							}
						}
					}
				}
				$data['checked'][] = $file;
				++$checkedUsersDirs;
				if($checkedUsersDirs >= $queryLimit) {
					break;
				}
			}
		}
		$groupping = 1000;
		$counter = 0;
		$sqlParts = array();
		$fields = array('added', 'file_type', 'file_path', 'reason');
		foreach($filesForDelete as $fileData) {
			$sqlParts[] = array(
				new \yii\db\Expression('NOW()'),
				$fileData['type'],
				$fileData['file'],
				$fileData['reason']
			);
			++$counter;
			if($counter >= $groupping) {
				(new Query)->createCommand()->batchInsert('files_for_delete', $fields, $sqlParts)->execute();
				$counter = 0;
				$sqlParts = array();
			}
		}
		if($counter > 0) {
			(new Query)->createCommand()->batchInsert('files_for_delete', $fields, $sqlParts)->execute();
		}
		self::saveDelData($data);
		$log = '';
		if(!flock($fLock, LOCK_UN)) {
			$log .= 'Не удалось снять блокировку с файла ' . $lockFile;
		}
		fclose($fLock);
		$log .= "Проверено папок: {$checkedUsersDirs}; Найдено файлов для удаления:\n" . var_export($filesForDelete, true) . "\nВремя выполнения: " . round(microtime(true) - $timeStart, 5) . " сек\n";
		file_put_contents($cfg['storagePath'] . '/del.log', $log . "\n\n", FILE_APPEND);
		header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Проверено папок: <?= $checkedUsersDirs; ?></title>
		<script type="text/javascript">
			function showHideLog() {
				var div = document.getElementById('logdiv');
				div.style.display = div.style.display === 'none' ? '' : 'none';
			}
		</script>
	</head>
	<body>
		<b>Проверено папок: <?= $checkedUsersDirs; ?></b> &nbsp;
		<a href="/check_files/files">К таблице файлок для удаления</a> &nbsp;
		<a href="#" onclick="showHideLog(); return false;">Открыть/закрыть лог</a>
		<div style="display: none; white-space: pre-wrap;" id="logdiv">
<?= $log; ?>
		</div>
	</body>
</html>
<?php
	}
}