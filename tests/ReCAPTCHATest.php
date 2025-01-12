<?php

use PHPUnit\Framework\TestCase;
use Holduix\Component\reCAPTCHA;

class ReCAPTCHATest extends TestCase
{
    public function testExample()
    {
        $recaptcha = new reCAPTCHA('site-key', 'secret-key');
        $this->assertInstanceOf(reCAPTCHA::class, $recaptcha);
    }
}