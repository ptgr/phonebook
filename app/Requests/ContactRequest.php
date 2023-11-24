<?php

namespace App\Requests;

use App\Enum\PhoneNumberType;

class ContactRequest extends BaseRequest
{
    public function validateFirstName(string $firstName): bool
    {
        return strlen($firstName) > 0 && strlen($firstName) <= 50;
    }

    public function validateLastName(string $lastName): bool
    {
        return strlen($lastName) > 0 && strlen($lastName) <= 50;
    }

    public function validatePhoneNumbers(array $phoneNumbers): bool
    {
        foreach ($phoneNumbers as $attribute) {
            if (!isset($attribute['number']) || !$this->validatePhoneNumber($attribute['number'])) {
                $this->lastCheckedItem .= "->number";
                return false;
            }

            if (!isset($attribute['type']) || !$this->validatePhoneType($attribute['type'])) {
                $this->lastCheckedItem .= "->type";
                return false;
            }
        }

        return true;
    }

    private function validatePhoneType(string $phoneType): bool
    {
        return PhoneNumberType::tryFrom($phoneType) !== null;
    }

    private function validatePhoneNumber(string $phoneNumber): bool
    {
        return preg_match("/^(\+420|00420)?[2-8]\d{8}$/", $phoneNumber);
    }

    public function validateAttributes(array $attributes): bool
    {
        foreach ($attributes as $attribute) {
            if (!isset($attribute['name']) || !$this->validateAttributeName($attribute['name'])) {
                $this->lastCheckedItem .= "->name";
                return false;
            }

            if (!isset($attribute['value']) || !$this->validateAttributeValue($attribute['value'])) {
                $this->lastCheckedItem .= "->value";
                return false;
            }
        }

        return true;
    }

    private function validateAttributeName(string $attributeName): bool
    {
        return strlen($attributeName) > 0 && strlen($attributeName) <= 20;
    }

    private function validateAttributeValue(string $attributeValue): bool
    {
        return strlen($attributeValue) > 0 && strlen($attributeValue) <= 30;
    }
}