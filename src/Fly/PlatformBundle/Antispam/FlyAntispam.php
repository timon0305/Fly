<?php

namespace Fly\PlatformBundle\Antispam;

class FlyAntispam
{
  private $mailer;
  private $locale;
  private $minLength;

  public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
  {
    $this->mailer    = $mailer;
    $this->locale    = $locale;
    $this->minLength = (int) $minLength;
  }
	
	public function isSpam($text)
	{
		return strlen($text) < $this->minLenght;
	}
}