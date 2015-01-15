<?php
/**
 * @package     Com_Localise
 * @subpackage  helper
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Automatic translation class.
 *
 * @see    http://msdn.microsoft.com/en-us/library/hh454950.aspx
 * @since  1.0
 */
Class HTTPTranslator
{
	/**
	 * @var null
	 * @Todo: add description to this property
	 */
	protected $authheader = null;

	/**
	 * Create and execute the HTTP CURL request.
	 *
	 * @param   string  $url         HTTP Url
	 * @param   string  $authHeader  Authorisation Header string
	 * @param   string  $postData    Data to post
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function curlRequest($url, $authHeader, $postData = '')
	{
		// Initialize the Curl Session.
		$ch = curl_init();

		// Set the Curl url.
		curl_setopt($ch, CURLOPT_URL, $url);

		// Set the HTTP HEADER Fields.
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader, "Content-Type: text/xml"));

		// CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if ($postData)
		{
			// Set HTTP POST Request.
			curl_setopt($ch, CURLOPT_POST, true);

			// Set data to POST in HTTP "POST" Operation.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}

		// Execute the  cURL session.
		$curlResponse = curl_exec($ch);

		// Get the Error Code returned by Curl.
		$curlErrno = curl_errno($ch);

		if ($curlErrno)
		{
			$curlError = curl_error($ch);
			throw new Exception($curlError);
		}

		// Close a cURL session.
		curl_close($ch);

		return $curlResponse;
	}

	/**
	 * Get authentication header
	 *
	 * @param   string  $clientID      Client id
	 * @param   string  $clientSecret  Client pass
	 *
	 * @return bool|null|string
	 */
	protected function getAuthHeader($clientID, $clientSecret)
	{
		if (!is_null($this->authheader))
		{
			return $this->authheader;
		}

		try
		{
			require __DIR__ . '/azuretoken.php';

			// OAuth Url.
			$authUrl = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";

			// Application Scope Url
			$scopeUrl = "http://api.microsofttranslator.com";

			// Application grant type
			$grantType = "client_credentials";

			// Create the AccessTokenAuthentication object.
			$authObj = new AccessTokenAuthentication;

			// Get the Access token.
			$accessToken = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);

			// Create the authorization Header string.
			$this->authHeader = "Authorization: Bearer " . $accessToken;

			return $this->authHeader;
		}
		catch (Exception $e)
		{
			JError::raiseWarning('SOME_ERROR_CODE', $e->getMessage());

			return false;
		}
	}

	/**
	 * Translate a string
	 *
	 * @param   string  $clientID      Client ID of the application.
	 * @param   string  $clientSecret  Client Secret key of the application.
	 * @param   string  $to            Language into witch the string will be translated
	 * @param   string  $string        Text to be translated
	 * @param   string  $from          Original language of the passed string
	 *
	 * @return string
	 */
	public function translate($clientID, $clientSecret, $to, $string, $from = null)
	{
		$string = trim($string);

		if (JString::strlen($string) < 1)
		{
			return '';
		}

		try
		{
			$authHeader = $this->getAuthHeader($clientID, $clientSecret);

			if (empty($from))
			{
				// HTTP Detect Method URL.
				$detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=" . urlencode($string);

				// Call the curlRequest.
				$strResponse = $this->curlRequest($detectMethodUrl, $authHeader);

				// Interprets a string of XML into an object.
				$xmlObj = simplexml_load_string($strResponse);

				foreach ((array) $xmlObj[0] as $val)
				{
					$from = $val;
				}
			}

			$getTranslateurl = "http://api.microsofttranslator.com/V2/Http.svc/Translate?From=$from"
				. "&To=$to&Text=" . urlencode($string);
			$curlResponse    = $this->curlRequest($getTranslateurl, $authHeader);

			// Interprets a string of XML into an object.
			$xmlObj = simplexml_load_string($curlResponse);

			return (string) $xmlObj;
		}
		catch (Exception $e)
		{
			JError::raiseWarning('SOME_ERROR_CODE', $e->getMessage());

			return '';
		}
	}
}
