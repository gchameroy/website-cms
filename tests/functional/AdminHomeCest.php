<?php

use Codeception\Util\HttpCode;

class AdminHomeCest
{
    public function tryHomePage(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/admin');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Dashboard', 'h2');
        $I->see('80', 'h2.font-bold');
        $I->see('0 €', 'h2.font-bold');
    }
}
