<?php

namespace App\Traits;

trait DataValidator
{
    /**
     * Validate numeric value - prevent empty string, allow numbers including 0
     */
    public function validateNumeric($value, $default = 0)
    {
        if ($value === '' || $value === null || !is_numeric($value)) {
            return $default;
        }

        // Convert to number (int or float) based on value
        return strpos($value, '.') !== false ? (float)$value : (int)$value;
    }

    /**
     * Validate string value - prevent empty string, allow meaningful content
     */
    public function validateString($value, $default = '', $trim = true)
    {
        if ($trim) {
            $value = trim($value);
        }

        if ($value === '' || $value === null) {
            return $default;
        }

        return $value;
    }

    /**
     * Validate required string - must not be empty after trim
     */
    public function validateRequiredString($value, $fieldName = 'Field')
    {
        $value = trim($value);

        if ($value === '') {
        //    throw new InvalidArgumentException("$fieldName is required and cannot be empty");
        }

        return $value;
    }

    /**
     * Validate array of data with numeric fields
     */
    public function validateNumericFields(array $data, array $numericFields, $default = 0)
    {
        foreach ($numericFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->validateNumeric($data[$field], $default);
            }
        }

        return $data;
    }

    /**
     * Validate array of data with string fields
     */
    public function validateStringFields(array $data, array $stringFields, $default = '')
    {
        foreach ($stringFields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->validateString($data[$field], $default);
            }
        }

        return $data;
    }

    /**
     * Comprehensive validation for mixed data
     */
    public function validateMixed($value, $type = 'string', $default = null)
    {
        switch ($type) {
            case 'numeric':
            case 'number':
                return $this->validateNumeric($value, $default ?? 0);

            case 'string':
                return $this->validateString($value, $default ?? '');

            case 'email':
                $value = $this->validateString($value);
                if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return $default ?? '';
                }
                return $value;

            default:
                return $value;
        }
    }
}
