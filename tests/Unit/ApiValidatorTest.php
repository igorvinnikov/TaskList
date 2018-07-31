<?php

namespace Tests\Unit;

use App\Exceptions\Http400ApiException;
use Tests\TestCase;
use App\Helpers\ApiValidator;
use Illuminate\Validation\Factory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use ReflectionClass;

/**
 * Unit test ApiValidator class Extends TestCase
 * @package Tests\Unit
 */
class ApiValidatorTest extends TestCase
{
    const TEST_VALIDATOR_ATTR = 'validator';

    const VALIDATE_FIELDS = [
        'title' => 'required|between:10, 45',
        'description' => 'max:65535',
        'status' => 'required|in:open,closed',
    ];

    /**
     * @var Factory Mock for testing validator Factory.
     */
    private $validatorFactoryMock;

    /**
     * @var Validator Mock for testing validator.
     */
    private $validatorMock;

    /**
     * @var MessageBag Mock for testing validator.
     */
    private $messageBagMock;

    /**
     * This methods runs before all tests and set up some needed values.
     *
     * @return void
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'fails',
                'getMessageBag'
            ])
            ->getMock();
        $this->validatorFactoryMock = $this->getMockBuilder(Factory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageBagMock = $this->getMockBuilder(MessageBag::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object $object     Instantiated object that we will run method on.
     * @param string $methodName Method name to call.
     * @param array  $parameters Array of parameters to pass into method.
     * @throws \ReflectionException Default Reflection class exception.
     *
     * @return mixed Method return.
     */
    public function invokeMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Test object can be instantiated.
     *
     * @return void
     */
    public function testCanInstantinate()
    {
        $ownValidator = new ApiValidator($this->validatorFactoryMock);

        $this->assertInstanceOf(ApiValidator::class, $ownValidator);
        $this->assertAttributeEquals($this->validatorFactoryMock, self::TEST_VALIDATOR_ATTR, $ownValidator);
    }

    /**
     * Test for validate method. Data pass validator without errors.
     *
     * @dataProvider providerValidateMethod
     *
     * @param array   $fieldsToValidate Fields to validate.
     * @param boolean $expectedResult   Expected result after validation.
     *
     * @return void
     */
    public function testValidate(array $fieldsToValidate, bool $expectedResult)
    {
        $validatorHelper = new ApiValidator($this->validatorFactoryMock);

        $this->validatorFactoryMock->expects($this->atLeastOnce())
            ->method('make')
            ->with($fieldsToValidate, [key(self::VALIDATE_FIELDS) => self::VALIDATE_FIELDS['title']])
            ->will($this->returnValue($this->validatorMock));

        $this->validatorMock->expects($this->atLeastOnce())->method('fails')->will($this->returnValue(false));

        $actualResult = $validatorHelper->validate($fieldsToValidate);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for testValidate.
     *
     * @return array
     */
    public function providerValidateMethod()
    {
        return [
            [
                ['title' => 'testTitleTask'],
                true
            ]
        ];
    }

    /**
     * Test for validate method. Data pass validator with errors.
     *
     * @return void
     */
    public function testValidateHasError()
    {
        $validatorHelper = new ApiValidator($this->validatorFactoryMock);

        $this->validatorFactoryMock->expects($this->atLeastOnce())
            ->method('make')
            ->with(['title' => 'testTitle'], [key(self::VALIDATE_FIELDS) => self::VALIDATE_FIELDS['title']])
            ->will($this->returnValue($this->validatorMock));

        $this->validatorMock->expects($this->atLeastOnce())->method('fails')->will($this->returnValue(true));
        $this->validatorMock->expects($this->any())
            ->method('getMessageBag')
            ->will($this->returnValue($this->messageBagMock));
        $this->messageBagMock->expects($this->any())
            ->method('all')
            ->will($this->returnValue('some error'));

        $this->expectException(Http400ApiException::class);

        $validatorHelper->validate(['title' => 'testTitle']);
    }

    /**
     * Unit test for invalid fields
     *
     * @dataProvider providerInvalidFields
     *
     * @param array $invalidField Invalid fields.
     *
     * @return void
     */
    public function testInvalidFieldsToValidate(array $invalidField)
    {
        $validatorHelper = new ApiValidator($this->validatorFactoryMock);

        $this->expectException(Http400ApiException::class);

        $validatorHelper->validate($invalidField);
    }

    /**
     * Provider for InvalidFieldsToValidate test.
     *
     * @return array
     */
    public function providerInvalidFields()
    {
        return [
            [
                ['titles' => 'test field']
            ]
        ];
    }

    /**
     * Unit test for getRules() method.
     *
     * @dataProvider providerGetRules
     *
     * @param array $fieldsToValidate Fields that should be validated.
     * @param array $expectedRules    Expected set of rules.
     * @throws \ReflectionException Default Reflection class exception.
     *
     * @return void
     */
    public function testGetRules(array $fieldsToValidate, array $expectedRules)
    {
        $validatorHelper = new ApiValidator($this->validatorFactoryMock);

        $actualRules = $this->invokeMethod($validatorHelper, 'getRules', [$fieldsToValidate]);

        $this->assertEquals($expectedRules, $actualRules);
    }

    /**
     * Provider for testGetRules.
     *
     * @return array
     */
    public function providerGetRules()
    {
        return [
            [
                ['title' => 'test_value'],
                ['title' => 'required|between:10, 45']
            ],
            [
                ['titles' => 'test_value'],
                []
            ]
        ];
    }
}
