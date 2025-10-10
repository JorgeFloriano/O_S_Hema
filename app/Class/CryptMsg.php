<?php

namespace App\Class;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Class\Logger;

class CryptMsg {
    public function tryDecrypt($encrypted) {

        $logger = new Logger();

        // Decrypt the order id
        try {
            $id = Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            $logger->log('error', 'Decryption error ('.$e->getMessage().' - '.$e->getFile().' - '.$e->getLine().').');
            return false;
        }

        return $id;
    }
}
