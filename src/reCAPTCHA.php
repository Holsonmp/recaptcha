<?php

namespace Holduix\Component;

/**
 * reCAPTCHA class for v2 and v3
 *
 * @author Holsonmp
 * @link https://github.com/holsonmp/recaptcha
 * @license GNU GPL 2.0
 */
class reCAPTCHA
{
    /**
     * ReCAPTCHA URL verifying
     *
     * @var string
     */
    const VERIFY_BASE_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Public key
     *
     * @var string
     */
    private $siteKey;

    /**
     * Private key
     *
     * @var string
     */
    private $secretKey;

    /**
     * Remote IP address
     *
     * @var string
     */
    protected $remoteIp = null;

    /**
     * Supported themes
     *
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $themes = array('light', 'dark');

    /**
     * Captcha theme. Default : light
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $theme = null;

    /**
     * Supported types
     *
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $types = array('image', 'audio');

    /**
     * Captcha type. Default : image
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $type = null;

    /**
     * Captcha language. Default : auto-detect
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/language
     */
    protected $language = null;

    /**
     * CURL timeout (in seconds) to verify response
     *
     * @var int
     */
    private $verifyTimeout = 1;

    /**
     * Captcha size. Default : normal
     *
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#render_param
     */
    protected $size = null;

    /**
     * List of errors
     *
     * @var array
     */
    protected $errorCodes = array();

    /**
     * reCAPTCHA version (v2 or v3)
     *
     * @var string
     */
    protected $version = 'v2';

    /**
     * reCAPTCHA v3 score threshold
     *
     * @var float
     */
    protected $scoreThreshold = 0.5;

    /**
     * Initialize site and secret keys
     *
     * @param string $siteKey Site key from ReCaptcha dashboard
     * @param string $secretKey Secret key from ReCaptcha dashboard
     * @param string $version reCAPTCHA version (v2 or v3)
     * @return void
     */
    public function __construct($siteKey = null, $secretKey = null, $version = 'v2')
    {
        $this->setSiteKey($siteKey);
        $this->setSecretKey($secretKey);
        $this->setVersion($version);
    }

    /**
     * Set site key
     *
     * @param string $key
     * @return object
     */
    public function setSiteKey($key)
    {
        $this->siteKey = $key;

        return $this;
    }

    /**
     * Set secret key
     *
     * @param string $key
     * @return object
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;

        return $this;
    }

    /**
     * Set remote IP address
     *
     * @param string $ip
     * @return object
     */
    public function setRemoteIp($ip = null)
    {
        if (!is_null($ip))
            $this->remoteIp = $ip;
        else
            $this->remoteIp = $_SERVER['REMOTE_ADDR'];

        return $this;
    }

    /**
     * Set theme
     *
     * @param string $theme (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setTheme($theme = 'light')
    {
        if (in_array($theme, self::$themes))
            $this->theme = $theme;
        else
            throw new \Exception('Theme "'.$theme.'"" is not supported. Available themes : '.join(', ', self::$themes));

        return $this;
    }

    /**
     * Set type
     *
     * @param  string $type (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setType($type = 'image')
    {
        if (in_array($type, self::$types))
            $this->type = $type;

        return $this;
    }

    /**
     * Set language
     *
     * @param  string $language (see https://developers.google.com/recaptcha/docs/language)
     * @return object
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set timeout
     *
     * @param  int $timeout
     * @return object
     */
    public function setVerifyTimeout($timeout)
    {
        $this->verifyTimeout = $timeout;

        return $this;
    }

    /**
     * Set size
     *
     * @param  string $size (see https://developers.google.com/recaptcha/docs/display#render_param)
     * @return object
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Set reCAPTCHA version
     *
     * @param  string $version (v2 or v3)
     * @return object
     */
    public function setVersion($version)
    {
        if (in_array($version, ['v2', 'v3']))
            $this->version = $version;
        else
            throw new \Exception('Version "'.$version.'"" is not supported. Available versions : v2, v3');

        return $this;
    }

    /**
     * Set reCAPTCHA v3 score threshold
     *
     * @param  float $threshold
     * @return object
     */
    public function setScoreThreshold($threshold)
    {
        if ($threshold >= 0.0 && $threshold <= 1.0)
            $this->scoreThreshold = $threshold;
        else
            throw new \Exception('Score threshold must be between 0.0 and 1.0');

        return $this;
    }

    /**
     * Generate the JS code of the captcha
     *
     * @return string
     */
    public function getScript()
    {
        $data = array();
        if (!is_null($this->language))
            $data = array('hl' => $this->language);

        if ($this->version === 'v3') {
            $data['render'] = $this->siteKey;
        }

        return '<script src="https://www.google.com/recaptcha/api.js?'.http_build_query($data).'"></script>';
    }

    /**
     * Generate the HTML code block for the captcha
     *
     * @return string
     */
    public function getHtml()
    {
        if (!empty($this->siteKey))
        {
            if ($this->version === 'v2') {
                $data = 'data-sitekey="'.$this->siteKey.'"';

                if (!is_null($this->theme))
                    $data .= ' data-theme="'.$this->theme.'"';

                if (!is_null($this->type))
                    $data .= ' data-type="'.$this->type.'"';

                if (!is_null($this->size))
                    $data .= ' data-size="'.$this->size.'"';

                return '<div class="g-recaptcha" '.$data.'></div>';
            } else if ($this->version === 'v3') {
                return '<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">';
            }
        }
    }

    /**
     * Checks the code given by the captcha
     *
     * @param string $response Response code after submitting form (usually $_POST['g-recaptcha-response'])
     * @return bool
     */
    public function isValid($response)
    {
        if (is_null($this->secretKey))
            throw new \Exception('You must set your secret key');

        if (empty($response)) {
            $this->errorCodes = array('internal-empty-response');
            return false;
        }

        $params = array(
            'secret'   => $this->secretKey,
            'response' => $response,
            'remoteip' => $this->remoteIp,
        );

        $url = self::VERIFY_BASE_URL.'?'.http_build_query($params);

        if (function_exists('curl_version'))
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->verifyTimeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }
        else
        {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response) || !$response)
        {
            return false;
        }

        $json = json_decode($response, true);

        if (isset($json['error-codes']))
        {
            $this->errorCodes = $json['error-codes'];
        }

        if ($this->version === 'v3') {
            if (!isset($json['score']) || $json['score'] < $this->scoreThreshold) {
                $this->errorCodes[] = 'score-below-threshold';
                return false;
            }
        }

        return $json['success'];
    }

    /**
     * Returns the errors encountered
     *
     * @return array Errors code and name
     */
    public function getErrorCodes()
    {
        $errors = array();

        if (count($this->errorCodes) > 0)
        {
            foreach ($this->errorCodes as $error)
            {
                switch ($error)
                {
                    case 'timeout-or-duplicate':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'Timeout or duplicate.',
                        );
                    break;

                    case 'missing-input-secret':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The secret parameter is missing.',
                        );
                    break;

                    case 'invalid-input-secret':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The secret parameter is invalid or malformed.',
                        );
                    break;

                    case 'missing-input-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The response parameter is missing.',
                        );
                    break;

                    case 'invalid-input-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The response parameter is invalid or malformed.',
                        );
                    break;

                    case 'bad-request':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The request is invalid or malformed.',
                        );
                    break;

                    case 'internal-empty-response':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The recaptcha response is required.',
                        );
                    break;

                    case 'score-below-threshold':
                        $errors[] = array(
                            'code' => $error,
                            'name' => 'The score is below the threshold.',
                        );
                    break;

                    default:
                        $errors[] = array(
                            'code' => $error,
                            'name' => $error,
                        );
                }
            }
        }

        return $errors;
    }
}