<?php
declare(strict_types=1);

/**
 * Copyright 2016 - 2019, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2016 - 2019, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace CakeDC\Api\Test\TestCase\Service\Renderer;

use Cake\Core\Configure;
use CakeDC\Api\Exception\UnauthenticatedException;
use CakeDC\Api\Service\Action\Result;
use CakeDC\Api\Service\FallbackService;
use CakeDC\Api\Service\Renderer\RawRenderer;
use CakeDC\Api\Test\ConfigTrait;
use CakeDC\Api\TestSuite\TestCase;

class RawRendererTest extends TestCase
{
    use ConfigTrait;

    public ?\CakeDC\Api\Service\FallbackService $Service = null;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->_initializeRequest();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Action);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testRendererInitializeByClassName()
    {
        $response = $this
            ->getMockBuilder(\Cake\Http\Response::class)
            ->setMethods(['withStatus', 'withType', 'withStringBody'])
            ->getMock();

        $this->_initializeRequest([], 'GET', ['response' => $response]);
        $serviceOptions = [
            'version' => null,
            'request' => $this->request,
            'response' => $response,
            'rendererClass' => 'CakeDC/Api.Raw',
        ];
        $this->Service = new FallbackService($serviceOptions);
        $renderer = $this->Service->getRenderer();
        $this->assertTrue($renderer instanceof RawRenderer);
    }

    /**
     * Test render response
     *
     * @return void
     */
    public function testRendererSuccess()
    {
        $response = $this
            ->getMockBuilder(\Cake\Http\Response::class)
            ->setMethods(['withStatus', 'withType', 'withStringBody'])
            ->getMock();

        $this->_initializeRequest([], 'GET', ['response' => $response]);
        $serviceOptions = [
            'version' => null,
            'request' => $this->request,
            'response' => $response,
            'rendererClass' => 'CakeDC/Api.Raw',
        ];
        $this->Service = new FallbackService($serviceOptions);

        $result = new Result();
        $statusCode = 200;
        $result->setCode($statusCode);
        $data = 'Updated!';
        $result->setData($data);
        $renderer = $this->Service->getRenderer();

        $response->expects($this->once())
                 ->method('withStatus')
                 ->with($statusCode)
                ->will($this->returnValue($response));
        $response->expects($this->once())
                 ->method('withStringBody')
                ->with($data)
                ->will($this->returnValue($response));
        $response->expects($this->once())
                 ->method('withType')
                 ->with('text/plain')
                ->will($this->returnValue($response));

        $renderer->response($result);
    }

    /**
     * Test render error
     *
     * @return void
     */
    public function testRendererError()
    {
        $response = $this
            ->getMockBuilder(\Cake\Http\Response::class)
            ->setMethods(['withStatus', 'withType', 'withStringBody'])
            ->getMock();

        $this->_initializeRequest([], 'GET', ['response' => $response]);
        $serviceOptions = [
            'version' => null,
            'request' => $this->request,
            'response' => $response,
            'rendererClass' => 'CakeDC/Api.Raw',
        ];
        $this->Service = new FallbackService($serviceOptions);

        Configure::write('debug', 0);
        $error = new UnauthenticatedException();
        $renderer = $this->Service->getRenderer();

        $response->expects($this->once())
            ->method('withStringBody')
            ->with('Unauthenticated')
            ->will($this->returnValue($response));
        $response->expects($this->once())
            ->method('withType')
            ->with('text/plain')
            ->will($this->returnValue($response));

        $renderer->error($error);
    }
}
