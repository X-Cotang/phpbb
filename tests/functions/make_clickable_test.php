<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

class phpbb_functions_make_clickable_test extends phpbb_test_case
{
	/**
	* Tags:
	* 'm' - full URL like xxxx://aaaaa.bbb.cccc.
	* 'l' - local relative board URL like http://domain.tld/path/to/board/index.php
	* 'w' - URL without http/https protocol like www.xxxx.yyyy[/zzzz] aka 'lazy' URLs
	* 'e' - email@domain type address
	*
	* Classes:
	* "postlink-local" for 'l' URLs
	* "postlink" for the rest of URLs
	* empty for email addresses
	**/
	public function data_test_make_clickable_url_positive()
	{
		return [
			[
				'http://www.phpbb.com/community/',
				'<!-- m --><a class="postlink" href="http://www.phpbb.com/community/">http://www.phpbb.com/community/</a><!-- m -->'
			],
			[
				'http://www.phpbb.com/path/file.ext#section',
				'<!-- m --><a class="postlink" href="http://www.phpbb.com/path/file.ext#section">http://www.phpbb.com/path/file.ext#section</a><!-- m -->'
			],
			[
				'ftp://ftp.phpbb.com/',
				'<!-- m --><a class="postlink" href="ftp://ftp.phpbb.com/">ftp://ftp.phpbb.com/</a><!-- m -->'
			],
			[
				'sip://bantu@phpbb.com',
				'<!-- m --><a class="postlink" href="sip://bantu@phpbb.com">sip://bantu@phpbb.com</a><!-- m -->'
			],
			[
				'www.phpbb.com/community/',
				'<!-- w --><a class="postlink" href="http://www.phpbb.com/community/">www.phpbb.com/community/</a><!-- w -->'
			],
			[
				'http://testhost/viewtopic.php?t=1',
				'<!-- l --><a class="postlink-local" href="http://testhost/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->'
			],
			[
				'javascript://testhost/viewtopic.php?t=1',
				'javascript://testhost/viewtopic.php?t=1'
			],
			[
				"java\nscri\npt://testhost/viewtopic.php?t=1",
				"java\nscri\n<!-- m --><a class=\"postlink\" href=\"pt://testhost/viewtopic.php?t=1\">pt://testhost/viewtopic.php?t=1</a><!-- m -->"
			],
			[
				'email@domain.com',
				'<!-- e --><a href="mailto:email@domain.com">email@domain.com</a><!-- e -->'
			],
			// Test appending punctuation mark to the URL
			[
				'http://testhost/viewtopic.php?t=1!',
				'<!-- l --><a class="postlink-local" href="http://testhost/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->!'
			],
			[
				'www.phpbb.com/community/?',
				'<!-- w --><a class="postlink" href="http://www.phpbb.com/community/">www.phpbb.com/community/</a><!-- w -->?'
			],
			// Test shortened text for URL > 55 characters long
			// URL text should be turned into: first 39 chars + ' ... ' + last 10 chars
			[
				'http://www.phpbb.com/community/path/to/long/url/file.ext#section',
				'<!-- m --><a class="postlink" href="http://www.phpbb.com/community/path/to/long/url/file.ext#section">http://www.phpbb.com/community/path/to/ ... xt#section</a><!-- m -->'
			],
		];
	}

	public function data_test_make_clickable_url_idn()
	{
		return [
			[
				'http://www.t??st.de/community/',
				'<!-- m --><a class="postlink" href="http://www.t??st.de/community/">http://www.t??st.de/community/</a><!-- m -->'
			],
			[
				'http://www.t??st.de/path/file.ext#section',
				'<!-- m --><a class="postlink" href="http://www.t??st.de/path/file.ext#section">http://www.t??st.de/path/file.ext#section</a><!-- m -->'
			],
			[
				'ftp://ftp.t??st.de/',
				'<!-- m --><a class="postlink" href="ftp://ftp.t??st.de/">ftp://ftp.t??st.de/</a><!-- m -->'
			],
			[
				'javascript://t??st.de/',
				'javascript://t??st.de/'
			],
			[
				'sip://bantu@t??st.de',
				'<!-- m --><a class="postlink" href="sip://bantu@t??st.de">sip://bantu@t??st.de</a><!-- m -->'
			],
			[
				'www.t??st.de/community/',
				'<!-- w --><a class="postlink" href="http://www.t??st.de/community/">www.t??st.de/community/</a><!-- w -->'
			],
			// Test appending punctuation mark to the URL
			[
				'http://??????????.????/viewtopic.php?t=1!',
				'<!-- m --><a class="postlink" href="http://??????????.????/viewtopic.php?t=1">http://??????????.????/viewtopic.php?t=1</a><!-- m -->!'
			],
			[
				'www.??????????.????/????????????????????/?',
				'<!-- w --><a class="postlink" href="http://www.??????????.????/????????????????????/">www.??????????.????/????????????????????/</a><!-- w -->?'
			],
			// Test shortened text for URL > 55 characters long
			// URL text should be turned into: first 39 chars + ' ... ' + last 10 chars
			[
				'http://www.??????????.????/????????????????????/????????/????/??????????????/????????????/file.ext#section',
				'<!-- m --><a class="postlink" href="http://www.??????????.????/????????????????????/????????/????/??????????????/????????????/file.ext#section">http://www.??????????.????/????????????????????/????????/????/ ... xt#section</a><!-- m -->'
			],

			// IDN with invalid characters shouldn't be parsed correctly (only 'valid' part)
			[
				'http://www.t??st???.de',
				'<!-- m --><a class="postlink" href="http://www.t??st">http://www.t??st</a><!-- m -->???.de'
			],
			// IDN in emails is unsupported yet
			['??????????@??????????.????', '??????????@??????????.????'],
		];
	}

	public function data_test_make_clickable_local_url_idn()
	{
		return [
			[
				'http://www.??????????.????/viewtopic.php?t=1',
				'<!-- l --><a class="postlink-local" href="http://www.??????????.????/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->'
			],
			// Test appending punctuation mark to the URL
			[
				'http://www.??????????.????/viewtopic.php?t=1!',
				'<!-- l --><a class="postlink-local" href="http://www.??????????.????/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->!'
			],
			[
				'http://www.??????????.????/????????????????????/?',
				'<!-- l --><a class="postlink-local" href="http://www.??????????.????/????????????????????/">????????????????????/</a><!-- l -->?'
			],
		];
	}

	public function data_test_make_clickable_custom_classes()
	{
		return [
			[
				'http://www.??????????.????/viewtopic.php?t=1',
				'http://www.??????????.????',
				'class1',
				'<!-- l --><a class="class1-local" href="http://www.??????????.????/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->'
			],
			[
				'http://www.??????????.????/viewtopic.php?t=1!',
				false,
				'class2',
				'<!-- m --><a class="class2" href="http://www.??????????.????/viewtopic.php?t=1">http://www.??????????.????/viewtopic.php?t=1</a><!-- m -->!'
			],
			[
				'http://www.??????????.????/????????????????????/?',
				false,
				'class3',
				'<!-- m --><a class="class3" href="http://www.??????????.????/????????????????????/">http://www.??????????.????/????????????????????/</a><!-- m -->?'
			],
			[
				'www.phpbb.com/community/',
				false,
				'class2',
				'<!-- w --><a class="class2" href="http://www.phpbb.com/community/">www.phpbb.com/community/</a><!-- w -->'
			],
			[
				'http://testhost/viewtopic.php?t=1',
				false,
				'class1',
				'<!-- l --><a class="class1-local" href="http://testhost/viewtopic.php?t=1">viewtopic.php?t=1</a><!-- l -->'
			],
			[
				'email@domain.com',
				false,
				'class-email',
				'<!-- e --><a href="mailto:email@domain.com">email@domain.com</a><!-- e -->'
			],
		];
	}

	protected function setUp(): void
	{
		parent::setUp();

		global $user, $request, $symfony_request;
		$user = new phpbb_mock_user();
		$request = new phpbb_mock_request();
		$symfony_request = new \phpbb\symfony_request($request);
	}

	/**
	 * @dataProvider data_test_make_clickable_url_positive
	 * @dataProvider data_test_make_clickable_url_idn
	 */
	public function test_urls_matching_positive($url, $expected)
	{
		$this->assertSame($expected, make_clickable($url));
	}

	/**
	 * @dataProvider data_test_make_clickable_local_url_idn
	 */
	public function test_local_urls_matching_idn($url, $expected)
	{
		$this->assertSame($expected, make_clickable($url, "http://www.??????????.????"));
	}

	/**
	 * @dataProvider data_test_make_clickable_custom_classes
	 */
	public function test_make_clickable_custom_classes($url, $server_url, $class, $expected)
	{
		$this->assertSame($expected, make_clickable($url, $server_url, $class));
	}
}
