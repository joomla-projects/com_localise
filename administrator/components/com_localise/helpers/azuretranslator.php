<?php
/*------------------------------------------------------------------------
# com_localise - Localise
# ------------------------------------------------------------------------
# author  author Yoshiki Kozaki <info@joomler.net>
# copyright Copyright (C) 2012 http://joomlacode.org/gf/project/com_localise/. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomlacode.org/gf/project/com_localise/
# Technical Support:  Forum - http://joomlacode.org/gf/project/com_localise/forum/
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * Class:HTTPTranslator
 * Please see the following link
 * http://msdn.microsoft.com/en-us/library/hh454950.aspx
 *
 * Processing the translator request.
 */
Class HTTPTranslator {

  protected $authheader = null;

  /*
   * Create and execute the HTTP CURL request.
   *
   * @param string $url    HTTP Url.
   * @param string $authHeader Authorization Header string.
   * @param string $postData   Data to post.
   *
   * @return string.
   *
   */
  public function curlRequest($url, $authHeader, $postData=''){
    //Initialize the Curl Session.
    $ch = curl_init();
    //Set the Curl url.
    curl_setopt ($ch, CURLOPT_URL, $url);
    //Set the HTTP HEADER Fields.
    curl_setopt ($ch, CURLOPT_HTTPHEADER, array($authHeader,"Content-Type: text/xml"));
    //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
    if($postData) {
      //Set HTTP POST Request.
      curl_setopt($ch, CURLOPT_POST, TRUE);
      //Set data to POST in HTTP "POST" Operation.
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }
    //Execute the  cURL session.
    $curlResponse = curl_exec($ch);
    //Get the Error Code returned by Curl.
    $curlErrno = curl_errno($ch);
    if ($curlErrno) {
      $curlError = curl_error($ch);
      throw new Exception($curlError);
    }
    //Close a cURL session.
    curl_close($ch);
    return $curlResponse;
  }

  protected function getAuthHeader($clientID, $clientSecret)
  {
    if(!is_null($this->authheader))
    {
    return $this->authheader;
    }

    try {
      require __DIR__. '/azuretoken.php';
      //OAuth Url.
      $authUrl    = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
      //Application Scope Url
      $scopeUrl   = "http://api.microsofttranslator.com";
      //Application grant type
      $grantType  = "client_credentials";

      //Create the AccessTokenAuthentication object.
      $authObj    = new AccessTokenAuthentication();
      //Get the Access token.
      $accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
      //Create the authorization Header string.
      $this->authHeader = "Authorization: Bearer ". $accessToken;

      return $this->authHeader;
    } catch(Exception $e) {
    JError::raiseWarning('SOME_ERROR_CODE', $e->getMessage());
    return false;
    }
  }

  /**
   *
   * @param string $clientID Client ID of the application.
   * @param string $clientSecret Client Secret key of the application.
   * @param string $from locale
   * @param string $to locale
   * @param string $string target text
   *
   * @return string
   */
  public function translate($clientID, $clientSecret, $to, $string, $from=null)
  {
    $string = trim($string);
    if(JString::strlen($string) < 1){
    return '';
    }

    try {
      $authHeader = $this->getAuthHeader($clientID, $clientSecret);

       if(empty($from)){
      //HTTP Detect Method URL.
      $detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($string);
      //Call the curlRequest.
      $strResponse = $this->curlRequest($detectMethodUrl, $authHeader);
      //Interprets a string of XML into an object.
      $xmlObj = simplexml_load_string($strResponse);
      foreach((array)$xmlObj[0] as $val){
        $from = $val;
      }
      }

      $getTranslateurl = "http://api.microsofttranslator.com/V2/Http.svc/Translate?From=$from"
      . "&To=$to&Text=". urlencode($string);
      $curlResponse = $this->curlRequest($getTranslateurl, $authHeader);

      //Interprets a string of XML into an object.
      $xmlObj = simplexml_load_string($curlResponse);
      return (string)$xmlObj;

    } catch (Exception $e) {
      JError::raiseWarning('SOME_ERROR_CODE', $e->getMessage());
      return '';
    }
  }
}