<?php

namespace App\Helpers;

use App\Exceptions\Http400ApiException;
use Illuminate\Validation\Factory;

/**
 * Class ApiValidator to validate request data.
 *
 * @package App\Helpers
 */
class ApiValidator
{
    /**
     * @var Factory
     */
    private $validator;

    /**
     * ApiValidator constructor.
     *
     * @param Factory $validator For validate fields.
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * This method check fields by rules.
     *
     * @param array $fieldsToValidate Fields that will be validated.
     * @return array
     */
    private function getRules(array $fieldsToValidate)
    {
        $result = [];

        $rules = [
            'title' => 'required|between:10, 45',
            'description' => 'max:65535',
            'status' => 'required|in:open,closed',
        ];

        foreach ($rules as $ruleKey => $ruleValue) {
            if (array_key_exists($ruleKey, $fieldsToValidate)) {
                $result[$ruleKey] = $ruleValue;
            }
        }

        return $result;
    }

    /**
     * Validate array of fields.
     *
     * @param array $fieldsToValidate Fields that will be validated.
     *
     * @return boolean
     */
    public function validate(array $fieldsToValidate)
    {
        $rules = $this->getRules($fieldsToValidate);

        if (empty($rules)) {
            throw new Http400ApiException();
        }

        $validatorResult = $this->validator->make($fieldsToValidate, $rules);

        if ($validatorResult->fails()) {
            throw new Http400ApiException($validatorResult->getMessageBag()->all()[0]);
        }

        return true;
    }
}
