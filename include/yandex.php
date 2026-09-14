<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
use Bitrix\Main\Page\Asset;

include_once($_SERVER['DOCUMENT_ROOT'] . "/yandex/crud.php");
Asset::getInstance()->addCss("/yandex/style.css");
?>

<div class="container-yandex">
    <h3 class="container-yandex-title">Яндекс диск</h3>
    <form method="POST" enctype="multipart/form-data" 
    class="container-yandex-form" action="<?= $APPLICATION->GetCurPage();?>">
        <?=bitrix_sessid_post()?>
        <input type="text" name="type" value="upload" hidden>
        <input type="file" name="files[]" multiple required>
        <button class="form-button" type="submit">Сохранить файлы</button>
    </form>
    <?if($errorMessage):?>
        <p class="container-yandex-error"><?= $errorMessage; ?></p>
    <?else:?>
        <h3 class="container-yandex-title">Ваши файлы</h3>
        <ul class="container-yandex-files-ul">
            <? /** @var Arhitector\Yandex\Disk\Resource\Collection $userFiles */ ?>
            <?foreach($userFiles as $file):?>
                <li class="container-yandex-files-ul-li">
                    <span class="container-yandex-files-ul-li-name"><?= $file->get('name') ?></span>
                    <form method="POST" 
                    class="container-yandex-files-form-delte" action="<?= $APPLICATION->GetCurPage();?>">
                        <?=bitrix_sessid_post()?>
                        <input type="text" name="type" value="delete" hidden>
                        <input type="text" name="fileName" value="<?= $file->get('name'); ?>" hidden>
                        <button class="form-button" type="submit">Удалить</button>
                    </form>
                </li>
            <?endforeach;?>
        </ul>
    <?endif;?>
</div>