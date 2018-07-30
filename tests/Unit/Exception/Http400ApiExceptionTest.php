<?php

namespace Tests\Unit\Exception;

use Tests\TestCase;
use App\Exceptions\Http400ApiException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests for Class Http400ApiException.
 *
 * @package Tests\Unit\Exception.
 */
class Http400ApiExceptionTest extends TestCase
{
    const TEST_CODE = 'GL-API-BAD-REQUEST-001';
    const TEST_TITLE = 'Bad Request';
    const TEST_DETAIL = 'Item not found in database or could not be edited';

    /**
     * @var JsonResponse|\PHPUnit_Framework_MockObject_MockObject $jsonResponseMock Request API Mock.
     */
    private $jsonResponseMock;

    /**
     * This methods runs before all tests and set up some needed values.
     *
     * @return void
     */
    public function setUp()
    {
        $this->jsonResponseMock = $this->getMockBuilder(JsonResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test object can be instantiated.
     *
     * @return void
     */
    public function testHttp400ApiException()
    {
        $exception = new Http400ApiException();

        $this->assertInstanceOf(Http400ApiException::class, $exception);
    }


    /**
     * * Test actuality response from Class Http400ApiException.
     *
     * @dataProvider providerParamsForTest400Exception
     *
     * @param array   $data   Expected JSON data.
     * @param integer $status Expected status code.
     *
     * @return void
     */
    public function testGetException(array $data, int $status)
    {
        $http400Exception = new Http400ApiException();

        $this->assertEquals(new JsonResponse($data, $status), $http400Exception->getResponse());
    }

    /**
     * Provider to make tests with different parameters.
     *
     * @return array
     */
    public function providerParamsForTest400Exception()
    {
        return [
            [
                [
                    'errors' => [
                        'code' => self::TEST_CODE,
                        'title' => self::TEST_TITLE,
                        'detail' => self::TEST_DETAIL
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            ]
        ];
    }
}
