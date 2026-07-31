<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>
<h3>Объявление!</h3>
<hr>
<br>
<table cellpadding="10" cellspacing="1" align="center" style="width: 500px;">
<tbody>
<tr>
    <td>
          <img width="131" src="/upload/warning.png" height="157">
    </td>
    <td>
         Внимание! Важная информация о [<i>внесите нужную информацию</i>].<br>
         ...
    </td>
</tr>
</tbody>
</table>
 <br>
<hr>
 <span style="color: #555555;"><i>Напишите нам, что Вы думаете об этом объявлении. Для этого воспользуйтесь формой обратной связи. Спасибо!</i></span><br>
 <br>
<?$APPLICATION->IncludeComponent(
    "bitrix:main.feedback",
    "",
    Array(
        "EMAIL_TO" => "sale@192.168.100.177",
        "EVENT_MESSAGE_ID" => array("7"),
        "OK_TEXT" => "Спасибо за Ваше мнение!",
        "REQUIRED_FIELDS" => array("NAME","EMAIL"),
        "USE_CAPTCHA" => "Y"
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>