<?php

namespace Iubar\Crypt;

interface AesInterface {
    function encrypt(string $plaintext, string $iv) : string|false;
    
    function decrypt(string $crypted, string $iv) : string|false;
    
}
