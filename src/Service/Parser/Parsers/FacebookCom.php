<?php

namespace App\Service\Parser\Parsers;

use App\Service\Parser\ParserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use App\Service\Parser\BaseParser;

class FacebookCom extends BaseParser implements ParserInterface
{
    const PHANTOM_JS_CLOUD_URL = 'http://PhantomJScloud.com/api/browser/v2/%s/';

    /**
     * @var string
     */
    private $key = 'a-demo-key-with-low-quota-per-ip-address';

    /**
     * @return ArrayCollection
     * @throws \Exception
     */
    public function getItems(): ArrayCollection
    {
        try {

            echo $this->getContent($this->source->getUrl());

        } catch (\Exception $exception) {
            $this->hasErrors = true;
//            throw $exception;
        }

        return $this->items;
    }

    private function getContent(string $url): string
    {
        $url = sprintf(self::PHANTOM_JS_CLOUD_URL, $this->key);

        $payload = (object)[
            'url' => $url,
            'renderType' => 'html'
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($payload)
            ]
        ];

        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}