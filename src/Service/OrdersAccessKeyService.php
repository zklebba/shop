<?php
namespace App\Service;

use Zend\Crypt\BlockCipher;

class OrdersAccessKeyService
{
    private $blockCipher;
    private $accessKeyTimeOut = 0;

    public function __construct(BlockCipher $blockCipher, $config = [])
    {
        $this->blockCipher = $blockCipher;
        $blockCipher->setKey($config['aes_key']);
        $this->accessKeyTimeOut = $config['access_key_time_out'];
    }

    public function generateAccessKey($email)
    {
        $exp = time() + $this->accessKeyTimeOut;
        $data = "$email|$exp";

        return strtr($this->blockCipher->encrypt($data), '+/=', '._-');
    }

    public function decodeAccessKey($key)
    {
        $data = $this->blockCipher->decrypt(strtr($key, '._-', '+/='));

        if ($data) {
            $data = explode('|', $data);

            if (intval($data[1]) < time() + $this->accessKeyTimeOut) {
                return $data[0];
            }
        }

        return false;
    }
}
