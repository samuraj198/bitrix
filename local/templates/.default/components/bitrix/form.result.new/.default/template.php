<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** 
 * @var array $arResult 
 */

echo $arResult["FORM_NOTE"] ?? '';

if ($arResult["isFormNote"] != "Y") {
	echo $arResult["FORM_HEADER"];
?>
	<div class="contact-form">
		<?if ($arResult["isFormTitle"] || $arResult["isFormDescription"]):?>
			<div class="contact-form__head">
				<?if ($arResult["isFormTitle"]):?>
					<div class="contact-form__head-title"><?=$arResult["FORM_TITLE"]?></div>
				<?endif;?>
				<?if ($arResult["isFormDescription"]):?>
					<div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
				<?endif;?>
			</div>
		<?endif;?>

		<div class="contact-form__form">
			<div class="contact-form__form-inputs">
				<?foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
					$arFieldStructure = current($arQuestion['STRUCTURE'] ?? []);
					if (($arFieldStructure["FIELD_TYPE"] ?? '') == "hidden") {
						echo $arQuestion["HTML_CODE"];
					}
				}
				?>

				<div class="input contact-form__input">
					<label class="input__label" for="medicine_name">
						<div class="input__label-text">
							<?=$arResult["QUESTIONS"]["NAME"]["CAPTION"];?>
							<?=$arResult["QUESTIONS"]["NAME"]["REQUIRED"] == "Y" ? "*" : "";?>
						</div>
						<?=str_replace(
							['class="inputtext"', 'class="inputtextarea"'],
							'class="input__input" id="medicine_name" required',
							$arResult["QUESTIONS"]["NAME"]["HTML_CODE"]
						);?>
						<?if (!empty($arResult["FORM_ERRORS"]) && array_key_exists("NAME", $arResult["FORM_ERRORS"])):?>
							<div class="input__notification" style="display: block;">
								<?=$arResult["FORM_ERRORS"]["NAME"];?>
							</div>
						<?endif;?>
					</label>
				</div>

				<div class="input contact-form__input">
					<label class="input__label" for="medicine_company">
						<div class="input__label-text">
							<?=$arResult["QUESTIONS"]["COMPANY"]["CAPTION"];?>
							<?=$arResult["QUESTIONS"]["COMPANY"]["REQUIRED"] == "Y" ? "*" : "";?>
						</div>
						<?=str_replace(
							['class="inputtext"', 'class="inputtextarea"'],
							'class="input__input" id="medicine_company" required',
							$arResult["QUESTIONS"]["COMPANY"]["HTML_CODE"]
						);?>
						<?if (!empty($arResult["FORM_ERRORS"]) && array_key_exists("COMPANY", $arResult["FORM_ERRORS"])):?>
							<div class="input__notification" style="display: block;">
								<?=$arResult["FORM_ERRORS"]["COMPANY"];?>
							</div>
						<?endif;?>
					</label>
				</div>

				<div class="input contact-form__input">
					<label class="input__label" for="medicine_email">
						<div class="input__label-text">
							<?=$arResult["QUESTIONS"]["EMAIL"]["CAPTION"];?>
							<?=$arResult["QUESTIONS"]["EMAIL"]["REQUIRED"] == "Y" ? "*" : "";?>
						</div>
						<?=str_replace(
							['type="text"', 'class="inputtext"'],
							['type="email"', 'class="input__input" id="medicine_email" required'],
							$arResult["QUESTIONS"]["EMAIL"]["HTML_CODE"]
						);?>
						<?if (!empty($arResult["FORM_ERRORS"]) && array_key_exists("EMAIL", $arResult["FORM_ERRORS"])):?>
							<div class="input__notification" style="display: block;">
								<?=$arResult["FORM_ERRORS"]["EMAIL"];?>
							</div>
						<?endif;?>
					</label>
				</div>

				<div class="input contact-form__input">
					<label class="input__label" for="medicine_phone">
						<div class="input__label-text">
							<?=$arResult["QUESTIONS"]["PHONE"]["CAPTION"];?>
							<?=$arResult["QUESTIONS"]["PHONE"]["REQUIRED"] == "Y" ? "*" : "";?>
						</div>
						<?=str_replace(
							['type="text"', 'class="inputtext"'],
							['type="tel"', 'class="input__input" id="medicine_phone" data-inputmask="\'mask\': \'+79999999999\', \'clearIncomplete\': \'true\'" maxlength="12" x-autocompletetype="phone-full" required'],
							$arResult["QUESTIONS"]["PHONE"]["HTML_CODE"]
						);?>
						<?if (!empty($arResult["FORM_ERRORS"]) && array_key_exists("PHONE", $arResult["FORM_ERRORS"])):?>
							<div class="input__notification" style="display: block;">
								<?=$arResult["FORM_ERRORS"]["PHONE"];?>
							</div>
						<?endif;?>
					</label>
				</div>

				<div class="input contact-form__input">
					<label class="input__label" for="medicine_message">
						<div class="input__label-text">
							<?=$arResult["QUESTIONS"]["MESSAGE"]["CAPTION"];?>
							<?=$arResult["QUESTIONS"]["MESSAGE"]["REQUIRED"] == "Y" ? "*" : "";?>
						</div>
						<?=str_replace(
							['class="inputtext"', 'class="inputtextarea"'],
							'class="input__input" id="medicine_message" required',
							$arResult["QUESTIONS"]["MESSAGE"]["HTML_CODE"]
						);?>
						<?if (!empty($arResult["FORM_ERRORS"]) && array_key_exists("MESSAGE", $arResult["FORM_ERRORS"])):?>
							<div class="input__notification" style="display: block;">
								<?=$arResult["FORM_ERRORS"]["MESSAGE"];?>
							</div>
						<?endif;?>
					</label>
				</div>

			</div>

			<div class="contact-form__bottom">
				<div class="contact-form__bottom-policy">
					Нажимая &laquo; Отправить &raquo; , Вы &nbsp; подтверждаете, что ознакомлены, полностью согласны и &nbsp; 
					принимаете условия &laquo; Согласия на &nbsp; обработку персональных данных &raquo; .
				</div>
				<button type="submit" name="web_form_submit" value="Y" class="form-button contact-form__bottom-button" data-success="Отправлено" data-error="Ошибка отправки">
					<div class="form-button__title">Оставить заявку</div>
				</button>
			</div>
		</div>
	</div>
<?
	echo $arResult["FORM_FOOTER"];
} 
?>
