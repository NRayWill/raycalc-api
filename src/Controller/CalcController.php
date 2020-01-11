<?php

/**
 * @OA\Info(
 *     version="1.0",
 *     title="REST API calcularor",
 *     @OA\Contact (
 *         name="Ravil Nagimov",
 *         email="nagimov.ravil@gmail.com"
 *     )
 * )
 */

namespace App\Controller;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use OpenApi\Annotations as OA;

class CalcController extends AbstractController
{
    public const OP_ADD = 'add';
    public const OP_SUB = 'sub';
    public const OP_MUL = 'mul';
    public $method = "";

    private const KEYS = [ 'A', 'B', 'C' ];

    public function getKeys() : array
    {
        return self::KEYS;
    }
    
    /**
     * @Route("/", name="index")
     *
     * @return void
     */
    public function index()
    {
        return $this->redirectToRoute('calc');
    }

    /**
     * @Route("/calc", name="calc")
     */
    public function calc()
    {
        $content = '<script src="https://rawcdn.githack.com/oscarmorrison/md-page/master/md-page.js">'
            .'</script><noscript>';
        $content .= file_get_contents("readme.md");

        return new Response(
            $content
        );
    }

    

    /**
     * [GET] Addition function
     * @Route("/calc/add", name="get_add", methods="GET")
     *
     * @OA\Get(
     *     path="/calc/add",
     *     tags={"add"},
     *     summary="Returns summ of 2 or 3 numbers",
     *     description="Returns summ of 2 or 3 numbers A + B( + C) in a JSON",
     *     operationId="addGet",
     *     @OA\Parameter(
     *         name="A",
     *         in="query",
     *         description="First number",
     *         required=true,
     *         explode=true,
     *         example=100,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="B",
     *         in="query",
     *         description="Second number",
     *         required=true,
     *         explode=true,
     *         example=50,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="C",
     *         in="query",
     *         description="Third number",
     *         required=false,
     *         explode=true,
     *         example=1,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100+50+1=151"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="151"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function addGet(Request $request) : Response
    {
        $this->method = "get";
        return $this->calculation($request, self::OP_ADD);
    }

    /**
     * [POST] Addition function
     * @Route("/calc/add", name="post_add", methods="POST")
     *
     * @OA\Post(
     *     path="/calc/add",
     *     tags={"add"},
     *     summary="Returns summ of 2 or 3 numbers",
     *     description="Returns summ of 2 or 3 numbers A + B( + C) in a JSON",
     *     operationId="addPost",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"A", "B"},
     *                 @OA\Property(
     *                     property="A",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="B",
     *                     type="number"
     *                 ),
     *                 @OA\Property(
     *                     property="C",
     *                     type="number"
     *                 ),
     *                 example={"A": 111, "B": "222", "C": "333"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100+50=150"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="150"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function addPost(Request $request) : Response
    {
        $this->method = "post";
        return $this->calculation($request, self::OP_ADD);
    }

    /**
     * [GET] Subtraction function. A - minuend, B - subtrahend.
     * @Route("/calc/sub", name="get_sub", methods="GET")
     *
     * @OA\Get(
     *     path="/calc/sub",
     *     tags={"sub"},
     *     summary="Subtraction function",
     *     description="Subtraction function. A - minuend, B - subtrahend",
     *     operationId="subGet",
     *     @OA\Parameter(
     *         name="A",
     *         in="query",
     *         description="First number",
     *         required=true,
     *         explode=true,
     *         example=100,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="B",
     *         in="query",
     *         description="Second number",
     *         required=true,
     *         explode=true,
     *         example=50,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100-50=50"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="50"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function subGet(Request $request) : Response
    {
        $this->method = "get";
        return $this->calculation($request, self::OP_SUB);
    }

    /**
     * [POST] Subtraction function. A - minuend, B - subtrahend.
     * @Route("/calc/sub", name="post_sub", methods="POST")
     *
     * @OA\Post(
     *     path="/calc/sub",
     *     tags={"sub"},
     *     summary="Subtraction function",
     *     description="Subtraction function. A - minuend, B - subtrahend",
     *     operationId="subPost",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 ref="#/components/schemas/Params2"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100-50=50"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="50"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function subPost(Request $request) : Response
    {
        $this->method = "post";
        return $this->calculation($request, self::OP_SUB);
    }

    /**
     * [GET] Multiplication function
     * @Route("/calc/mul", name="get_mul", methods="GET")
     *
     * @OA\Get(
     *     path="/calc/mul",
     *     tags={"mul"},
     *     summary="Multiplication function",
     *     description="Multiplication function A*B",
     *     operationId="mulGet",
     *     @OA\Parameter(
     *         name="A",
     *         in="query",
     *         description="First number",
     *         required=true,
     *         explode=true,
     *         example=111,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="B",
     *         in="query",
     *         description="Second number",
     *         required=true,
     *         explode=true,
     *         example=222,
     *         @OA\Schema(
     *             type="number",
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100*50=5000"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="5000"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function mulGet(Request $request) : Response
    {
        $this->method = "get";
        return $this->calculation($request, self::OP_MUL);
    }

    /**
     * [POST] Multiplication function
     * @Route("/calc/mul", name="post_mul", methods="POST")
     *
     * @OA\Post(
     *     path="/calc/mul",
     *     tags={"mul"},
     *     summary="Multiplication function",
     *     description="Multiplication function A*B",
     *     operationId="mulPost",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 ref="#/components/schemas/Params2"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     example="100*50=5000"
     *                 ),
     *                 @OA\Property(
     *                     property="result",
     *                     type="string",
     *                     example="5000"
     *                 ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *         response=400,
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request HTTP request
     * @return Response JSON result
     */
    public function mulPost(Request $request) : Response
    {
        $this->method = "post";
        return $this->calculation($request, self::OP_MUL);
    }

    /**
     * Common calculation function
     *
     * @param Request HTTP request
     * @param string Mathematical operation
     * @return Response JSON result
     */
    public function calculation(Request $request, string $operation) : Response
    {
        $result = '0';
        $message = '';
        $errors = [];
        $params = [];

        if ($this->extractParams($request, $params, $operation, $errors)) {
            $scale = $this->defineScale($params);
            bcscale($scale);

            $paramsCount = count($params);
            switch ($operation) {
                case self::OP_ADD:
                    $result = bcadd($params[self::KEYS[0]], $params[self::KEYS[1]]);
                    $message =  $params[self::KEYS[0]] . '+' . $params[self::KEYS[1]];
                    if ($paramsCount == 3) {
                        $result = bcadd($result, $params[self::KEYS[2]]);
                        $message .= '+' . $params[self::KEYS[2]];
                    }
                    $message .= '=' . $result;
                    break;
                case self::OP_SUB:
                    $result = bcsub($params[self::KEYS[0]], $params[self::KEYS[1]]);
                    $message =  $params[self::KEYS[0]] . '-' . $params[self::KEYS[1]] . '=' . $result;
                    break;
                case self::OP_MUL:
                    $result = bcmul($params[self::KEYS[0]], $params[self::KEYS[1]]);
                    $message =  $params[self::KEYS[0]] . '*' . $params[self::KEYS[1]] . '=' . $result;
                    break;
                default:
                    throw new HttpException(500, "Wrong operation type");
                    break;
            }
        } else {
            throw new HttpException(400, implode("; ", $errors));
        }
        
        return $this->json([
            'message' => $message,
            'result' => $result
        ]);
    }

    /**
     * Extracting and validation of parameters
     *
     * @param Request HTTP request
     * @param array Parameters
     * @param string Matehmatical operation
     * @param array Errors
     * @return boolean Validation status
     */
    public function extractParams(Request $request, array &$params, string $operation, array &$errors) : bool
    {
        $errorsCount = count($errors);
        $params = [];
        if ($this->method == "get") {
            $params = $request->query->all();
        } else {
            $params = $request->request->all();
        }
        
        $paramsCount = count($params);

        if ($paramsCount != 2 && $operation != self::OP_ADD) {
            $errors[] =  "Invalid count of parameters. Should be 2.";
        } elseif ($operation == self::OP_ADD && ($paramsCount < 2 || $paramsCount >3)) {
            $errors[] =  "Invalid count of parameters. Should be 2 or 3.";
        }

        $keys = array_keys($params);
        for ($i = 0; $i < count($params); $i++) {
            if (!preg_match("/[ABC]/", $keys[$i])) {
                $errors[] = "Wrong parameter name: " . $keys[$i];
            }

            if (preg_match("/^[0-9]+([.][0-9]+)?$/s", $params[$keys[$i]])) {
            } else {
                $errors[] = "Invalid parameter: " . $params[$keys[$i]];
            }
        }

        if ($errorsCount != count($errors)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Defining necessary scale of BSMath
     *
     * @param array $numbers
     * @return int Scale
     */
    public function defineScale(array $numbers) : int
    {
        $scaleResult = 0;
        foreach ($numbers as $number) {
            $separatorPos = strrpos($number, '.');
            if (!$separatorPos) {
                continue;
            } else {
                $decimalPlaces = strlen(substr($number, strrpos($number, '.') + 1));
                if ($scaleResult < $decimalPlaces) {
                    $scaleResult = $decimalPlaces;
                }
            }
        }
        return $scaleResult;
    }
}



/**
 * @OA\Schema(
 *     schema="Params2",
 *     required={"A", "B"},
 *     @OA\Property(
 *         property="A",
 *         type="number",
 *         example=100
 *     ),
 *     @OA\Property(
 *         property="B",
 *         type="number",
 *         example=50
 *     ),
 * )
 */
