<?php

namespace App\Services\Language;

class ChangeLanguageService {

	public const LANG_CODE_RU = 'ru';
	public const LANG_CODE_EN = 'en';

	public const LANG_TEXT_RU = '🇷🇺 русский';
	public const LANG_TEXT_EN = '🇺🇸 english';

	public static $locales = [
		self::LANG_TEXT_RU => self::LANG_CODE_RU, self::LANG_TEXT_EN => self::LANG_CODE_EN,
	];

}
