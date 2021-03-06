<?php

namespace App\Reader;

use App\Service\ReaderCacheService;

class FacebookReader  extends BaseReader implements ReaderInterface
{
    const PHANTOM_JS_CLOUD_API_URL = 'http://PhantomJScloud.com/api/browser/v2/%s/';

    /**
     * @var array
     */
    private $config = [];

    public function __construct(ReaderCacheService $cacheService, array $config)
    {
        $this->config = $config;
        parent::__construct($cacheService);
    }

    public function getContent(string $url): string
    {
        $key = $this->getKeyByUrl($url);

        if ($cachedContent = $this->cacheService->getContent($key) and $this->cacheService->isEnabled()) {
            return $cachedContent;
        }

        $apiUrl = sprintf(self::PHANTOM_JS_CLOUD_API_URL, $this->config['key']);

        $payload = (object)[
            'url' => $url,
            'ignoreImages' => true,
            'renderType' => 'html',
            'scripts' => (object)[
                'domReady' => [
                    sprintf('document.getElementsByName("email")[0].value = "%s"', $this->config['email']),
                    sprintf('document.getElementsByName("pass")[0].value = "%s"', $this->config['password']),
                    'document.getElementById("login_form").submit()',
                    'setTimeout(function(){window.scrollBy(0,10000);},2000)',
                ]
            ]
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($payload)
            ]
        ];

        $context = stream_context_create($options);
        $content = file_get_contents($apiUrl, false, $context);

        $this->cacheService->putContent($key, $content);
        return $content;
    }
}