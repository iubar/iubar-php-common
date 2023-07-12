<?php

namespace Iubar\Net;

use Symfony\Component\Mime\Address;

interface IEmailProvider {
	public function send() : int;	
	public function setSmtpPort(int $port) : void;
	public function setSmtpUser(string $user) : void;
	public function setSmtpPassword(string $password) : void;
 	public function setFrom(string $from_email, string $from_name): void;	
 	public function addTo(string $email, string $name = '') : void;
	public function setSubject(string $subject): void;
	public function setBodyHtml(string $html): void;
	public function getFromAddress(): Address;		
}
