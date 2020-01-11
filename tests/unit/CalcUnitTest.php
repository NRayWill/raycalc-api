<?php namespace App\Tests;

use OpenApi\Annotations as OA;
use App\Controller\CalcController;
use Symfony\Component\Debug\ErrorHandler;

use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Codeception\Util\Debug;

class CalcUnitTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testKeys()
    {
        $container = new Container();
        $controller = new CalcController();
        $controller->setContainer($container);

        $this->assertEquals($controller->getKeys(), ['A', 'B', 'C']);
    }

    public function testScaleDefining()
    {
        $controller = new CalcController();
        $scale = $controller->defineScale([]);
        $this->assertEquals($scale, 0);
        $scale = $controller->defineScale(['24']);
        $this->assertEquals($scale, 0);
        $scale = $controller->defineScale(['24', '123.6', '234.456456456']);
        $this->assertEquals($scale, 9);
        $scale = $controller->defineScale(['0.123172386178236182361823']);
        $this->assertEquals($scale, 24);
        $scale = $controller->defineScale(['12345654789723489823795283405', '0.123172386178236182361823']);
        $this->assertEquals($scale, 24);
    }

    public function testCalculation()
    {
        $container = new Container();
        $controller = new CalcController();
        $controller->setContainer($container);

        $methods = ['get', 'post'];

        foreach ($methods as $method) {
            $controller->method = $method;
        
            //region Addition
            $request = Request::createFromGlobals();
            $a = '12345654789723489823795283405';
            $b = '0.123172386178236182361823';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
            }

            $content = $controller->calculation($request, $controller::OP_ADD)->getContent();
            $this->assertEquals(
                '{"message":"'
                .'12345654789723489823795283405'
                .'+0.123172386178236182361823'
                .'=12345654789723489823795283405.123172386178236182361823",'
                .'"result":"12345654789723489823795283405.123172386178236182361823"}',
                $content
            );

            $request = Request::createFromGlobals();
            $a = '12345654789723489823795283405';
            $b = '0.123172386178236182361823';
            $c = '94';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
                $request->query->set('C', $c);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
                $request->request->set('C', $c);
            }
            $content = $controller->calculation($request, $controller::OP_ADD)->getContent();
            $this->assertEquals(
                '{"message":"'
                .'12345654789723489823795283405'
                .'+0.123172386178236182361823'
                .'+94'
                .'=12345654789723489823795283499.123172386178236182361823",'
                .'"result":"12345654789723489823795283499.123172386178236182361823"}',
                $content
            );
            //endregion
        
            //region Substraction
            $request = Request::createFromGlobals();
            $a = '123.4';
            $b = '54.32';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
            }
            $content = $controller->calculation($request, $controller::OP_SUB)->getContent();
            $this->assertEquals(
                '{"message":"123.4-54.32=69.08",'
                .'"result":"69.08"}',
                $content
            );
            //endregion

            //region Multiplication
            $request = Request::createFromGlobals();
            $a = '123.4';
            $b = '54.32';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
            }
            $content = $controller->calculation($request, $controller::OP_MUL)->getContent();
            $this->assertEquals(
                '{"message":"123.4*54.32=6703.08",'
                .'"result":"6703.08"}',
                $content
            );
            //endregion
        }
    }

    public function testParamsExtractCount()
    {
        $container = new Container();
        $controller = new CalcController();
        $controller->setContainer($container);
    
        $methods = ['get', 'post'];
    
        foreach ($methods as $method) {
            $controller->method = $method;

            $request = Request::createFromGlobals();
            
            $params = [];
            $errors = [];
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertFalse($isValid);
            
            $a = '1';
            if ($method == 'get') {
                $request->query->set('A', $a);
            } else {
                $request->request->set('A', $a);
            }
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertFalse($isValid);

            $a = '1';
            $b = '2';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
            }
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertTrue($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertTrue($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertTrue($isValid);

            $a = '1';
            $b = '2';
            $c = '3';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
                $request->query->set('C', $c);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
                $request->request->set('C', $c);
            }
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertTrue($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertFalse($isValid);
            
            //extractParams(Request $request, array &$params, string $operation, array &$errors) : bool
        }
    }

    public function testParamsExtractNames()
    {
        $container = new Container();
        $controller = new CalcController();
        $controller->setContainer($container);
    
        $methods = ['get', 'post'];
    
        foreach ($methods as $method) {
            $controller->method = $method;

            $request = Request::createFromGlobals();
            
            $params = [];
            $errors = [];

            $a = '1';
            $b = '2';
            if ($method == 'get') {
                $request->query->set('X', $a);
                $request->query->set('Y', $b);
            } else {
                $request->request->set('X', $a);
                $request->request->set('Y', $b);
            }
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertFalse($isValid);
        }
    }

    public function testParamsExtractValues()
    {
        $container = new Container();
        $controller = new CalcController();
        $controller->setContainer($container);
    
        $methods = ['get', 'post'];
    
        foreach ($methods as $method) {
            $controller->method = $method;

            $request = Request::createFromGlobals();
            
            $params = [];
            $errors = [];

            $a = '1w';
            $b = 'q.3';
            if ($method == 'get') {
                $request->query->set('A', $a);
                $request->query->set('B', $b);
            } else {
                $request->request->set('A', $a);
                $request->request->set('B', $b);
            }
            $isValid = $controller->extractParams($request, $params, $controller::OP_ADD, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_SUB, $errors);
            $this->assertFalse($isValid);
            $isValid = $controller->extractParams($request, $params, $controller::OP_MUL, $errors);
            $this->assertFalse($isValid);
        }
    }
}
