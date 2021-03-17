<?php

require_once './function.php';

define('ROOT_PATH', str_replace("/", "\\", $_SERVER["DOCUMENT_ROOT"]));

if (basename($_SERVER['SCRIPT_NAME']) == 'explorer.php') {
    header('Location: http://localhost/admin/index.php');
}

$dir = $_GET['dir'] ?? '';
$dir = realpath($dir);
chdir($dir);
$curDir = getcwd();
$arHere = scandir($curDir);

$formats = 'html|txt|css|js';


if ($fileToRename = $_GET['rename'] ?? false) {

    $arrName = explode(".", $fileToRename);

    $fileToRename = $dir . '\\' .$fileToRename;

    if ($newName = $_POST['newName'] ?? false) :

        if (preg_match('/^[-a-zA-Zа-яА-ЯёЁ0-9 _]+?$/ui', $arrName[0])) {
            rename($fileToRename, $dir . '\\' . $newName . '.' . $arrName[1]);
        }
        reloadPage($dir);;
    
    else : ?>
    <div id="greyback">
        <div id="window">
        <form action="/admin/?dir=<?= $dir; ?>&rename=<?= $_GET['rename']; ?>" method="POST">
            <h3>Переименование</h3>
            <input type="text" name="newName" value="<?= $arrName[0]; ?>">
            <div>
                <button>OK</button>
                <a href="#" class="close">Отмена</a>
            </div>
            
        </form>
        </div>
    </div>

    <?php endif;
}

if (($fileToRemove = $_GET['remove'] ?? false) && ($type = $_GET['type'] ?? false)) {
    $fileToRemove = $dir . '\\' . $fileToRemove;
    if (file_exists($fileToRemove)) {
        if ($type == 'file') {
            unlink($fileToRemove);
            header("location: /admin/?dir=$dir");
        } elseif ($type == 'dir') {
            removeDir($fileToRemove);
            header("location: /admin/?dir=$dir");
        }
    }
}

if ($newFile = $_POST['newFile'] ?? false) {

    if (preg_match('/^[-a-zA-Zа-яА-ЯёЁ0-9 _]+?$/ui', $newFile) && $_POST['typeFile'] ?? false) {

        $newFile = $dir . '\\' . $newFile . '.' . $_POST['typeFile'];

        while (file_exists($newFile)) {
            $index = strripos($newFile, '.');
            if ($index !== false) {
                $newFile = substr($newFile, 0, $index) . '_copy' . substr($newFile, $index);
            } else {
                $newFile .= '_copy';
            }
        }
        $fb = fopen($newFile, "w");
        fclose($fb);
        reloadPage($dir);;
    }
}   

if ($newFile = $_POST['newDir'] ?? false) {
    if (preg_match('/^[-a-zA-Zа-яА-ЯёЁ0-9 _\.]+$/ui', $newFile)) {

        $newFile = $dir . '\\' . $newFile;

        while (file_exists($newFile)) {
            $newFile .= '_copy';
        }

        mkdir($newFile);
        reloadPage($dir);;
    }
}

if ($fileToEdit = $_GET['edit'] ?? false) : ?>
    <div id="greyback">
        <div id="window" class="file-edit">
            <form action="/admin/?dir=<?= $dir; ?>&edit=<?= $_GET['edit']; ?>" method="POST">
            <h3>Редактирование файла <?= $_GET['edit'] ?></h3>
                <textarea cols="120" rows="30" name="pole"><?= htmlspecialchars(file_get_contents($fileToEdit)) ?></textarea>
                <div><button>OK</button><a href="#" class="close">Отмена</a></div>
            </form>
        </div>
    </div>

    <?php if (!empty($_POST['pole'])) {
            $t = $_POST['pole'];
            file_put_contents($fileToEdit, $t);
            reloadPage($dir);;
        }

    endif;

if ($_FILES['files']['tmp_name'] ?? false) {

    $destPath = $_SERVER['DOCUMENT_ROOT'] . '/admin/uploads';
    if (!file_exists($destPath)) {
        mkdir($destPath);
    }

    echo $destPath;

    $allFiles = scandir($destPath);

    foreach ($_FILES['files']['tmp_name'] as $index => $path) {
        if (file_exists($path)) {
            $fileInfo = pathinfo($_FILES['files']['name'][$index]);

            $newNamefile = translit($fileInfo['filename']);
            $findFiles = preg_grep("/^" . $newNamefile . "(.+)?\." . $fileInfo['extension'] . "$/", $allFiles);
            $filename =  $newNamefile . (count($findFiles) > 0 ? '_' . (count($findFiles) + 1) : '') . '.' . $fileInfo['extension'];

            move_uploaded_file($path, $destPath . '/' . $filename);
        }
    }

    reloadPage($dir);;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>File manager</title>
</head>

<body>

<div class = "container">

    <div class = "action">
        <a href="/admin/" title="Домой"><img src="img/home.png" alt="Home" /></a>
        <a href="/admin/?dir=<?= $dir; ?>&action=addfile#greyback" title="Создать файл"><img src="img/addfile.png" alt="Add file" /></a>
        <a href="/admin/?dir=<?= $dir; ?>&action=addfolder#greyback"  title="Добавить папку"><img src="img/addfolder.png" alt="Add folder" /></a>
        <a href="/admin/?dir=<?= $dir; ?>&action=uplfile#greyback" title="Загрузить файл"><img src="img/uplfile.png" alt="Upload file" /></a>
    </div>

    <table>
        <tr>
            <th>Имя</th>
            <th class="w_size">Размер</th>
            <th class="w_date">Дата</th>
            <th class="w_action">Управление</th>
        </tr>
        <?php foreach ($arHere as $index => $path) {
            if ($index == 0) continue;

            if ($index == 1 && $path == '..' && $dir != ROOT_PATH) : ?>
            
            <tr><td colspan="4"><a href="/admin/?dir=<?= $dir . '\\' . $path; ?>" title="Подняться вверх"><img src="img/levelup.png" alt="folder" /><?= $path; ?></a></td></tr>
            <?php endif; 

            if (is_dir($dir . '\\' . $path) && $path != '..') : ?>
                <tr>    
                    <td>
                        <a href="/admin/?dir=<?= $dir . '\\' . $path; ?>"><img src="img/folder.png" alt="folder" /><?= $path; ?></a>
                    </td>
                    <td>Папка</td>
                    <td><?= getFiledate($path); ?></td>
                    <td>
                        <a href="/admin/?dir=<?= $dir; ?>&remove=<?= $path; ?>&type=dir" title="Удалить" onClick="return window.confirm('Удалить папку <?= $path; ?>?');"><img src="img/del.png" alt="Delete" /></a>
                        <a href="/admin/?dir=<?= $dir; ?>&rename=<?= $path; ?>#greyback" title="Переименовать"><img src="img/rename.png" alt="Rename" /></a>
                    </td>
                </tr>
            <?php endif;
        } ?>

        <?php foreach ($arHere as $index => $path) {
            if (!is_dir($dir . '\\' . $path)) : ?>
                <tr>
                    <td><img src="img/file.png" alt="file" /><?= $path; ?></td>
                    <td><?= getFilesize($path); ?></td>
                    <td><?= getFiledate($path); ?></td>
                    <td>
                        <a href="/admin/?dir=<?= $dir; ?>&remove=<?= $path; ?>&type=file" title="Удалить" onClick="return window.confirm('Удалить файл <?= $path; ?>?');"><img src="img/del.png" alt="Delete" /></a>
                        <a href="/admin/?dir=<?= $dir; ?>&rename=<?= $path; ?>#greyback" title="Переименовать"><img src="img/rename.png" alt="Rename" /></a>
                        <?php if (checkExten($path)) : ?>
                            <a href="/admin/?dir=<?= $dir; ?>&edit=<?= $path; ?>#greyback" title="Редактировать"><img src="img/edit.png" alt="Редактировать" /></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif;
        } ?>

    </table>

    <div id="greyback">
        <div id="window">
            <form action="/admin/?dir=<?= $dir; ?>" method="POST" enctype="multipart/form-data"><br />
                <?php if ($fileToEdit = $_GET['action'] ?? false) {
                    if ($fileToEdit == 'addfile') : ?>
                        <h3>Создать файл</h3>
                        <input type="text" name="newFile" placeholder="имя файла">
                        <select name="typeFile"> 
                            <?php foreach (explode('|', $formats) as $exten) : ?>
                                <option value="<?=$exten?>">.<?=$exten?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif;

                    if ($fileToEdit == 'addfolder') : ?>
                        <h3>Создать папку</h3>
                        <input type="text" name="newDir" placeholder="имя папки">
                    <?php endif;

                    if ($fileToEdit == 'uplfile') : ?>
                        <h3>Загрузить файл</h3>
                        <input type="file" multiple name="files[]">
                    <?php endif;
                } ?>

                <div>
                    <button>ОК</button>
                    <a href="#" class="close">Отмена</a>
                </div>
            </form>
        </div>
    </div>

</div>

</body>
</html>