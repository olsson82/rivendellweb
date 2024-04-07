<?php
/*********************************************************************************************************
 *                                        RIVENDELL WEB BROADCAST                                        *
 *    A WEB SYSTEM TO USE WITH RIVENDELL RADIO AUTOMATION: HTTPS://GITHUB.COM/ELVISHARTISAN/RIVENDELL    *
 *              THIS SYSTEM IS NOT CREATED BY THE DEVELOPER OF RIVENDELL RADIO AUTOMATION.               *
 * IT'S CREATED AS AN HELP TOOL ONLINE BY ANDREAS OLSSON AFTER HE FIXED BUGS IN AN OLD SCRIPT CREATED BY *
 *             BRIAN P. MCGLYNN : HTTPS://GITHUB.COM/BPM1992/RIVENDELL/TREE/RDWEB/WEB/RDPHP              *
 *        USE THIS SYSTEM AT YOUR OWN RISK. IT DO DIRECT MODIFICATION ON THE RIVENDELL DATABASE.         *
 *                 YOU CAN NOT HOLD US RESPONISBLE IF SOMETHING HAPPENDS TO YOUR SYSTEM.                 *
 *                   THE DESIGN IS DEVELOP BY SAUGI: HTTPS://GITHUB.COM/ZURAMAI/MAZER                    *
 *                                              MIT LICENSE                                              *
 *                                   COPYRIGHT (C) 2024 ANDREAS OLSSON                                   *
 *             PERMISSION IS HEREBY GRANTED, FREE OF CHARGE, TO ANY PERSON OBTAINING A COPY              *
 *             OF THIS SOFTWARE AND ASSOCIATED DOCUMENTATION FILES (THE "SOFTWARE"), TO DEAL             *
 *             IN THE SOFTWARE WITHOUT RESTRICTION, INCLUDING WITHOUT LIMITATION THE RIGHTS              *
 *               TO USE, COPY, MODIFY, MERGE, PUBLISH, DISTRIBUTE, SUBLICENSE, AND/OR SELL               *
 *                 COPIES OF THE SOFTWARE, AND TO PERMIT PERSONS TO WHOM THE SOFTWARE IS                 *
 *                       FURNISHED TO DO SO, SUBJECT TO THE FOLLOWING CONDITIONS:                        *
 *            THE ABOVE COPYRIGHT NOTICE AND THIS PERMISSION NOTICE SHALL BE INCLUDED IN ALL             *
 *                            COPIES OR SUBSTANTIAL PORTIONS OF THE SOFTWARE.                            *
 *              THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR               *
 *               IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,                *
 *              FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE              *
 *                AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER                 *
 *             LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,             *
 *             OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE             *
 *                                               SOFTWARE.                                               *
 *********************************************************************************************************/

/*******************************************************************************
 *               THIS IS LANGUAGE CLASS FOR MULTILANGUAGE FILES                *
 *               TO USE ON PAGE USE <?=$ML->TR('LANGVARIABLE')?>               *
 *   TO INCLUDE VARIABLE USE <?=$ML->TR('LANGVARIABLE {{'.$VARIABLE.'}}')?>    *
 *                 TO USE SINGULAR OR PLURAL WITH NUMBERS USE:                 *
 *       <?=$ML->TRP('SINGULAR {{'.$NUMB.'}}', 'PLURAL {{'.$NUMB.'}}')?>       *
 *           WHERE SINGULAR IS THE SINGULAR VARIABLE IN TRANSLATION            *
 *                  AND PLURAL IS THE PLURAL IN TRANSLATION.                   *
 *       THE VARIABLE IN TRANSLATION FILE THAT HAVE {{VARIABLE}} IN IT,        *
 *                   NEED TO HAVE AN EMPTY SPACE AT THE END.                   *
 * AND IN THE TRANSLATION ADD {{1}} WHERE YOU WANT THE VARIABLE DO BE PRINTED. *
 *      IF MULTIPLE VARIABLES USES YOU HAVE {{1}} AND THEN COUNT UP {{2}}      *
 *******************************************************************************/

class MultiLang
{

	private $lang,
	$USE_COOKIES,
	$lang_file,
	$dictionary,
	$languages_dir = '/var/www/html/languages/',
	$DEFAULT_LANGUAGE = 'en_US',
	$untranslated_logging = true,
	$last_translated = false,
	$fallbackdictionary, //If a word don't is translated yet
	$lang_fallbackfile;

	//The function takes two optional parameters.

	public function __construct($use_cookies = true, $untranslated_logging = false)
	{
		$this->USE_COOKIES = $use_cookies;
		$this->untranslated_logging = $untranslated_logging;

		if ($this->USE_COOKIES) {
			$this->lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : '';

		} else {
			$this->lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : '';
		}

		if (empty($this->lang)) {

			$this->setLanguage($this->DEFAULT_LANGUAGE);
		} else {
			$this->setLanguage($this->lang);
		}

	}
	/******************************************************
	 *     THIS IS A FALLBACK TRANSLATION TO ENGLISH.     *
	 * IF TRANSLATION STRING IS MISSING IN LANGUAGE FILE, *
	 *    IT WILL FALLBACK AND READ FROM ENGLISH FILE.    *
	 ******************************************************/
	public function fallbacktr($word)
	{

		$lookup_word = $word;
		$lookup_word = preg_replace('/{{.*}}/', '', $lookup_word);

		if (isset($this->fallbackdictionary) & isset($this->fallbackdictionary[$lookup_word])) {
			$trWord = $this->fallbackdictionary[$lookup_word];

			$arr = [];
			$arr2 = [];

			preg_match_all("/{{([0-9]+)}}/", $trWord, $arr);

			preg_match_all("/{{(.*?)}}/", $word, $arr2);

			foreach ($arr[1] as $key => $value) {

				$val = intval($value) - 1;

				if (isset($arr2[1][$val])) {

					$trWord = str_replace('{{' . $value . '}}', $arr2[1][$val], $trWord);
				}
			}

			$this->last_translated = false;
			return $trWord;
		}
	}

	/********************************************************************************
	 *       THIS IS USED IF NUMBERS ARE TRANSLATE TO GET SINGULAR OR PLURAL        *
	 * IT WILL PASS TWO STRINGS ONE IF THE WORD IS IN SINGULAR, THE OTHER IN PLURAL *
	 * To use:  <?=$ml->trp('SINGULAR {{'.$numb.'}}', 'PLURAL {{'.$numb.'}}')?>     *
	 * In translation add {{1}} and variable add empty space last.                  *
	 ********************************************************************************/
	public function trp($singular, $plural)
	{
		$isplural = 0;
		$lookup_words = $singular;
		$lookup_wordp = $plural;
		$lookup_words = preg_replace('/{{.*}}/', '', $lookup_words);
		$lookup_wordp = preg_replace('/{{.*}}/', '', $lookup_wordp);

		if (isset($this->dictionary) & isset($this->dictionary[$lookup_words]) & isset($this->dictionary[$lookup_wordp])) {
			$trWords = $this->dictionary[$lookup_words];
			$trWordp = $this->dictionary[$lookup_wordp];
			$arr = [];
			$arr1 = [];
			$arr2 = [];
			$arr3 = [];
			preg_match_all("/{{([0-9]+)}}/", $trWords, $arr);
			preg_match_all("/{{([0-9]+)}}/", $trWordp, $arr1);

			preg_match_all("/{{(.*?)}}/", $singular, $arr2);
			preg_match_all("/{{(.*?)}}/", $plural, $arr3);

			foreach ($arr[1] as $key => $value) {

				$val = intval($value) - 1;

				if (isset($arr2[1][$val])) {

					$trWords = str_replace('{{' . $value . '}}', $arr2[1][$val], $trWords);
					if ($arr2[1][$val] < 2) {
						$isplural = 0;
					}
				}
			}

			foreach ($arr1[1] as $key => $value) {

				$val = intval($value) - 1;

				if (isset($arr3[1][$val])) {

					$trWordp = str_replace('{{' . $value . '}}', $arr3[1][$val], $trWordp);
					if ($arr3[1][$val] > 1) {
						$isplural = 1;
					}
				}
			}

			$this->last_translated = true;
			if ($isplural == 1) {
				return $trWordp;
			} else {
				return $trWords;
			}

		} else {

			$this->not_yet_translated($lookup_words);

			$singular = str_replace("{{", '', $singular);
			$singular = str_replace("}}", '', $singular);

			$this->last_translated = false;

			//return $word;
			return $this->fallbacktr($singular); //Get the english translation if missing in other.
		}
	}


	//The Main Translate Function.
	//Parameters for the translation string are nested in double brackets e.g. 'Hello {{World}}!' can be represented in the language dictionary as '{{1}} Hello !', which would produce the string 'World Hello!';
	public function tr($word)
	{

		$lookup_word = $word;
		$lookup_word = preg_replace('/{{.*}}/', '', $lookup_word);


		if (isset($this->dictionary) & isset($this->dictionary[$lookup_word])) {

			$trWord = $this->dictionary[$lookup_word];

			$arr = [];
			$arr2 = [];

			preg_match_all("/{{([0-9]+)}}/", $trWord, $arr);

			preg_match_all("/{{(.*?)}}/", $word, $arr2);

			foreach ($arr[1] as $key => $value) {

				$val = intval($value) - 1;

				if (isset($arr2[1][$val])) {

					$trWord = str_replace('{{' . $value . '}}', $arr2[1][$val], $trWord);
				}
			}

			$this->last_translated = true;

			return $trWord;

		} else {

			$this->not_yet_translated($lookup_word);

			$word = str_replace("{{", '', $word);
			$word = str_replace("}}", '', $word);

			$this->last_translated = false;

			//return $word;
			return $this->fallbacktr($word); //Get the english translation if missing in other.
		}

	}

	public function set_directory($path)
	{

		return ($this->languages_dir = $path);
	}

	public function set_untranslated_logging($bool = false)
	{

		return ($this->untranslated_logging = $bool);
	}

	private function not_yet_translated($lookup_word)
	{

		if (!file_exists($this->languages_dir) & $this->untranslated_logging) {

			mkdir($this->languages_dir, 0777, true);
		}

		if (!$this->USE_COOKIES & !file_exists($this->lang_file)) {

			$example_contents = "<?php\n\n\n$" . $this->lang . " = array();\n\n$" . $this->lang . "['EXAMPLE'] = 'Example';";

			if ($this->untranslated_logging) {

				$example_contents .= "\n\n\n/** Not Yet Translated **/\n\n// " . $lookup_word;

				file_put_contents($this->lang_file, $example_contents);
			}

			return;
		}

		//!$this->USE_COOKIES&
		if ($this->untranslated_logging) {

			$contents = file_get_contents($this->lang_file);

			if (strpos($contents, '// ' . $lookup_word) === false) {

				file_put_contents($this->lang_file, "\n// $" . $this->lang . "['" . $lookup_word . "'] = ''; ", FILE_APPEND);
			}
		}
	}

	public function setLanguage($language_code, $duration = 604800)
	{

		// Cookie Duration defaults to 1 week.

		$language_code = '' . $language_code;

		if (strlen($language_code) < 2) {

			//Only two-character language codes are accepted.
			$language_code = $this->DEFAULT_LANGUAGE;
		}

		$this->lang = $language_code;

		if ($this->USE_COOKIES) {
			$expire = time() + (3600 * 24 * 7);
			setcookie('lang', $this->lang, $expire, '/');
			//setcookie('lang', $this->lang, $duration, '/');

		} else {

			if (!isset($_SESSION)) {

				session_start();
			}

			$_SESSION['lang'] = $this->lang;
		}

		$this->lang_file = $this->languages_dir . $this->lang . '.php';
		$this->lang_fallbackfile = $this->languages_dir . 'en_US.php'; //If a string not translate fallback to english!

		if (file_exists($this->lang_file)) {

			require $this->lang_file;

			$this->dictionary = ${$this->lang};
			//$this->fallbackdictionary = ${$this->DEFAULT_LANGUAGE};
		}

		if (file_exists($this->lang_fallbackfile)) {

			require $this->lang_fallbackfile;

			$this->fallbackdictionary = ${'en_US'};
			//$this->fallbackdictionary = ${$this->DEFAULT_LANGUAGE};
		}
	}

	public function translated()
	{

		return $this->last_translated;
	}

	public function language()
	{

		return $this->lang;
	}

	public function setDefaultLang($langcode)
	{

		$this->DEFAULT_LANGUAGE = $langcode;
	}

}