<?php

namespace NotificationChannels\Kavenegar;

use Illuminate\Support\Facades\View;
use JsonSerializable;

/**
 * Class KavenegarMessage.
 */
class KavenegarMessage implements JsonSerializable
{
    private $payload = [
        "method" => "sms",
        "token" => null,
        "token2" => null,
        "token3" => null,
        "template" => null,
        "token10" => null,
        "token20" => null,
        "receptor" => null,
        "message" => null
    ];
    /**
     * @param string $content
     *
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct()
    {
    }

    public function method($method) {
        $this->payload['method'] = $method;
        return $this;
    }

    public function getMethod() {
        return $this->payload['method'];
    }

    public function token($token) {
        $this->payload['token'] = $token;
        return $this;
    }

    public function token2($token='') {
        $this->payload['token2'] = $token;
        return $this;
    }

    public function token3($token='') {
        $this->payload['token3'] = $token;
        return $this;
    }

    public function token10($token='') {
        $this->payload['token10'] = $token;
        return $this;
    }

    public function token20($token='') {
        $this->payload['token20'] = $token;
        return $this;
    }

    public function template($template) {
        $this->payload['template'] = $template;
        return $this;
    }

    public function message($message) {
        $this->payload['message'] = $message;
        return $this;
    }

    public function to($phone) {
        $this->payload['receptor'] = $phone;
        return $this;
    }

    public function hasNoReceptor()
    {
        return !isset($this->payload['receptor']);
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function hasExtraTokens()
    {
        return ($this->payload['token10'] !== null) || ($this->payload['token20'] !== null);
    }
}