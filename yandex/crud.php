<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
use Arhitector\Yandex\Client\Exception\UnauthorizedException;
use Arhitector\Yandex\Client\Exception\NotFoundException;
use Arhitector\Yandex\Client\Exception\ServiceException;
use Arhitector\Yandex\Disk;
use Arhitector\Yandex\Disk\Resource\Closed;
use Bitrix\Main\Engine\CurrentUser;


$errorMessage = "";
$token = "ТОКЕН СКИНУ В ЛС ПРИ НЕОБХОДИМОСТИ";
$disk = new Disk("$token");
$userId = CurrentUser::get()->getId();

if (!$userId) {
    $errorMessage = "Вы не авторизованы";
    return;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!check_bitrix_sessid()) {
        $errorMessage = "Сессия истекла. Обновите страницу";
        return;
    }
}

switch ($_POST["type"]) {
    case "upload":
        $uploadErrors = uploadFiles($_FILES["files"], $disk, $userId);

        if (strlen($uploadErrors) > 0) {
            $errorMessage = $uploadErrors;
            return;
        }

        LocalRedirect($APPLICATION->GetCurPage());
        break;
    case "delete":
        try {
            deleteFiles(basename($_POST["fileName"]), $disk, $userId);
            LocalRedirect($APPLICATION->GetCurPage());
        } catch (NotFoundException $e) {
            $errorMessage = $e->getMessage();
            return;
        } catch (ServiceException | UnauthorizedException  $e) {
            $errorMessage = "Диск недоступен. Попробуйте позже";
            return;
        } catch (\Exception $e) {
            $errorMessage = "Непредвиденная ошибка. Обновите страницу или попробуйте позже.";
            return;
        }
        break;
}

try {
    $userFolder = $disk->getResource("app:/$userId/");

    if (!$userFolder->has()) {
        $userFolder->create();
    }

    $userFiles = $userFolder->get('items');

    if (!$userFiles || $userFiles->count() < 1) {
        $errorMessage = "У вас нет файлов на диске";
        return;
    }
} catch (ServiceException | UnauthorizedException  $e) {
    $errorMessage = "Диск недоступен. Попробуйте позже";
    return;
} catch (\Exception $e) {
    $errorMessage = "Непредвиденная ошибка. Обновите страницу или попробуйте позже.";
    return;
}

function uploadFiles(array $files, Disk $disk, int $userId): string {
    $errors = "";
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === 0) {
            $fileName = basename($files['name'][$i]);
            $fileTmp = $files['tmp_name'][$i];

            try {
                $newFile = getFilePath($fileName, $disk, $userId);
                $newFile->upload($fileTmp, true);
            } catch (\Exception $e) {
                $errors .= "<li class='container-yandex-errors-ul-li'>Не удалось загрузить файл: $fileName</li>";
            }
        }
    }

    if (strlen($errors) > 0) {
        $errors = "<ul class='container-yandex-errors-ul'>$errors</ul>";
    }
    return $errors;
}

function deleteFiles(string $fileName, Disk $disk, int $userId): void {
    $deleteFile = getFilePath($fileName, $disk, $userId);
    if (!$deleteFile->has()) {
        throw new NotFoundException("Не удалось найти удаляемый файл на диске");
    }
    $deleteFile->delete(true);
}

function getFilePath(string $fileName, Disk $disk, int $userId): Closed {
    return $disk->getResource("app:/$userId/$fileName");
}