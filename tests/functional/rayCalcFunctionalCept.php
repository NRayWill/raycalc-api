<?php

use App\Tests\FunctionalTester;

$I = new FunctionalTester($scenario);
$I->wantTo('Check that calculations work correct');

//region GET-testing
echo "\nGET-TESTING\n";

//region Testing of addition
echo "ADDITION\n";

echo "0 parameters       - 400\n";
$I->amOnPage('/calc/add');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->amOnPage('/calc/add?A=123.4');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "2 parameters       - 200\n";
$I->amOnPage('/calc/add?A=12345654789723489823795283405&B=0.123172386178236182361823');
$I->seeResponseCodeIs(200);
$I->see('{"message":"12345654789723489823795283405+'
    .'0.123172386178236182361823=12345654789723489823795283405.123172386178236182361823"'
    .',"result":"12345654789723489823795283405.123172386178236182361823"}');

echo "2 wrong parameters - 400\n";
$I->amOnPage('/calc/add?A=avar&B=bvar');
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "3 parameters       - 200\n";
$I->amOnPage('/calc/add?A=123.4&B=54.32&C=22.1');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4+54.32+22.1=199.82","result":"199.82"}');

echo "3 wrong parameters - 400\n";
$I->amOnPage('/calc/add?A=avar&B=bvar&C=cvar');
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar; Invalid parameter: cvar');
//endregion Addition testing

//region Testing of substraction
echo "\nSUBSTRACTION\n";

echo "0 parameters       - 400\n";
$I->amOnPage('/calc/sub');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->amOnPage('/calc/sub?A=123.4');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "2 wrong parameters - 400\n";
$I->amOnPage('/calc/sub?A=avar&B=bvar');
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "2 parameters       - 200\n";
$I->amOnPage('/calc/sub?A=123.4&B=54.32');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4-54.32=69.08","result":"69.08"}');

echo "3 parameters       - 400\n";
$I->amOnPage('/calc/sub?A=123.4&B=54.32&C=123');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters. Should be 2.');
//endregion

//region Testing of multiplication
echo "\nMULTIPLICATION\n";

echo "0 parameters       - 400\n";
$I->amOnPage('/calc/mul');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->amOnPage('/calc/mul?A=123.4');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "2 wrong parameters - 400\n";
$I->amOnPage('/calc/mul?A=avar&B=bvar');
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "2 parameters       - 200\n";
$I->amOnPage('/calc/mul?A=123.4&B=54.32');
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4*54.32=6703.08","result":"6703.08"}');

echo "3 parameters       - 400\n";
$I->amOnPage('/calc/mul?A=123.4&B=54.32&C=123');
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters. Should be 2.');
//endregion
//endregion

//region POST-testing
echo "\nPOST-TESTING\n";

//region Testing of addition
echo "ADDITION\n";

echo "0 parameters       - 400\n";
$I->sendPOST('/calc/add', []);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->sendPOST('/calc/add', ['A' => '12345654789723489823795283405']);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');


echo "2 parameters       - 200\n";
$I->sendPOST('/calc/add', ['A' => '12345654789723489823795283405', 'B' => '0.123172386178236182361823']);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"12345654789723489823795283405+'
    .'0.123172386178236182361823=12345654789723489823795283405.123172386178236182361823"'
    .',"result":"12345654789723489823795283405.123172386178236182361823"}');

echo "2 wrong parameters - 400\n";
$I->sendPOST('/calc/add', ['A' => 'avar', 'B' => 'bvar']);
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "3 parameters       - 200\n";
$I->sendPOST('/calc/add', ['A' => '123.4', 'B' => '54.32', 'C' => '22.1']);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4+54.32+22.1=199.82","result":"199.82"}');

echo "3 wrong parameters - 400\n";
$I->sendPOST('/calc/add', ['A' => 'avar', 'B' => 'bvar', 'C' => 'cvar']);
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar; Invalid parameter: cvar');
//endregion

//region Testing of substraction
echo "\nSUBSTRACTION\n";

echo "0 parameters       - 400\n";
$I->sendPOST('/calc/sub', []);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->sendPOST('/calc/sub', ['A' => '123.4']);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "2 wrong parameters - 400\n";
$I->sendPOST('/calc/sub', ['A' => 'avar', 'B' => 'bvar']);
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "2 parameters       - 200\n";
$I->sendPOST('/calc/sub', ['A' => '123.4', 'B' => '54.32']);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4-54.32=69.08","result":"69.08"}');

echo "3 parameters       - 400\n";
$I->sendPOST('/calc/sub', ['A' => '123.4', 'B' => '54.32', 'C' => '123']);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters. Should be 2.');
//endregion

//region Testing of multiplication
echo "\nMULTIPLICATION\n";

echo "0 parameters       - 400\n";
$I->sendPOST('/calc/mul', []);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "1 parameter        - 400\n";
$I->sendPOST('/calc/mul', ['A' => '123.4']);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters');

echo "2 wrong parameters - 400\n";
$I->sendPOST('/calc/mul', ['A' => 'avar', 'B' => 'bvar']);
$I->seeResponseCodeIs(400);
$I->see('Invalid parameter: avar; Invalid parameter: bvar');

echo "2 parameters       - 200\n";
$I->sendPOST('/calc/mul', ['A' => '123.4', 'B' => '54.32']);
$I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
$I->see('{"message":"123.4*54.32=6703.08","result":"6703.08"}');

echo "3 parameters       - 400\n";
$I->sendPOST('/calc/mul', ['A' => '123.4', 'B' => '54.32', 'C' => '123']);
$I->seeResponseCodeIs(400);
$I->see('Invalid count of parameters. Should be 2.');
//endregion
//endregion
