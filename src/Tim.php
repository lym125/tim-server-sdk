<?php

namespace Lym125\Tim;

use GuzzleHttp\Client;
use Tencent\TLSSigAPIv2;
use Illuminate\Support\Str;
use Lym125\Tim\Exceptions\Exception;
use Psr\Http\Message\ResponseInterface;

class Tim
{
    const ENDPOINT_TEMPLATE = 'https://console.tim.qq.com/v4/%s/%s?%s';

    const ENDPOINT_VERSION = 'v4';

    const ENDPOINT_FORMAT = 'json';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Tencent\TLSSigAPIv2
     */
    protected $tsa;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 腾讯即时通讯 IM 服务 REST API
     *
     * @param string $service
     * @param string $cmd
     * @param array $params
     *
     * @return array
     */
    public function api(string $service, string $cmd, array $params = [])
    {
        $result = $this->post($this->buildEndpoint($service, $cmd), $params);

        if (0 === $result['ErrorCode']) {
            return $result;
        }

        throw new Exception('Tim REST API error: '. json_encode($result));
    }

    /**
     * 服务端计算 UserSig
     *
     * @param string $identifier
     * @param int $expires 签名有效期(秒)
     *
     * @return string
     */
    public function genSig(string $identifier, int $expires = 60): string
    {
        if (null == $this->tsa) {
            $this->tsa = new TLSSigAPIv2(
                $this->getConfig('sdk_app_id'),
                $this->getConfig('secret_key')
            );
        }

        return $this->tsa->genSig($identifier, $expires);
    }

    /**
     * Make a post request.
     *
     * @param string $endpoint
     * @param array  $params
     * @param array  $headers
     *
     * @return array
     */
    protected function post(string $endpoint, array $params = [], array $headers = [])
    {
        return $this->request('post', $endpoint, [
            'headers' => $headers,
            'json' => $params,
        ]);
    }

    /**
     * Make a http request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $options  http://docs.guzzlephp.org/en/latest/request-options.html
     *
     * @return array
     */
    protected function request(string $method, string $endpoint, array $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient()->{$method}($endpoint, $options));
    }

    /**
     * Return http client.
     *
     * @param array $options
     *
     * @return \GuzzleHttp\Client
     *
     * @codeCoverageIgnore
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * Convert response contents to json.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $contents = $response->getBody()->getContents();

        return json_decode($contents, true);
    }

    /**
     * Build endpoint url.
     *
     * @param string $service
     * @param string $cmd
     *
     * @return string
     */
    protected function buildEndpoint(string $service, string $cmd): string
    {
        $query = http_build_query([
            'sdkappid' => $this->getConfig('sdk_app_id'),
            'identifier' => $this->getConfig('identifier'),
            'usersig' => $this->genSig($this->getConfig('identifier')),
            'random' => mt_rand(0, 4294967295),
            'contenttype' => self::ENDPOINT_FORMAT,
        ]);

        return \sprintf(self::ENDPOINT_TEMPLATE, $service, $cmd, $query);
    }

    /**
     * Get the tim configuration.
     *
     * @param  string  $key
     *
     * @return array
     */
    protected function getConfig($key)
    {
        return $this->config[$key];
    }
}
