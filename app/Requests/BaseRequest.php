<?php

namespace App\Requests;

abstract class BaseRequest
{
    protected readonly ?array $parsedPayload;
    protected array $errors = [];
    protected string $lastCheckedItem = '';

    public function __construct(
        protected string $rawPayload,
        protected bool $isJson = true,
        protected bool $multiPayload = false,
        protected array $requiredItems = []
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getParsedData(): ?array
    {
        return $this->parsedPayload;
    }

    public function isValid(): bool
    {
        $this->validateJSON();
        if (!empty($this->errors)) {
            return false;
        }

        $this->validatePayloadFormat();
        if (!empty($this->errors)) {
            return false;
        }

        $validationResult = true;
        $multiPayload = $this->multiPayload ? $this->parsedPayload : [$this->parsedPayload];

        foreach ($multiPayload as $key => $item) {

            foreach ($this->requiredItems as $requiredItem) {
                if (!isset($item[$requiredItem])) {
                    $this->errors[] = \sprintf('Missing required item "%s"', $requiredItem);
                    $validationResult = false;
                }
            }

            foreach ($item as $attributeName => $attributeValue) {

                $method = 'validate' . ucfirst(strtolower($attributeName));
                $this->lastCheckedItem = $attributeName;

                if (\method_exists($this, $method) && !$this->$method($attributeValue)) {
                    $this->errors[] = \sprintf('Invalid item "%d->%s"', $key, $this->lastCheckedItem);
                    $validationResult = false;
                }
            }
        }

        return $validationResult;
    }

    private function validateJSON(): void
    {
        if (!is_string($this->rawPayload))
            return;

        $this->parsedPayload = json_decode($this->rawPayload, true);
        if ($this->parsedPayload === null) {
            $this->errors[] = 'The payload is not in JSON format';
        }
    }

    private function validatePayloadFormat(): void
    {
        if (!$this->multiPayload)
            return;

        foreach ($this->parsedPayload as $item) {
            if (!is_array($item)) {
                $this->errors[] = 'The payload is not in correct format';
                break;
            }
        }
    }
}