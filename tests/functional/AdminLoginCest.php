<?php

class AdminLoginCest
{
    public function tryLogin(FunctionalTester $I)
    {
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/admin/sign-in');
        $I->submitForm('form', ['_username' => 'admin@test.fr', '_password' => 'admin']);
        $I->seeCurrentUrlEquals('/admin');
        $I->see('Dashboard', 'h2');
    }

    public function tryLoginFail(FunctionalTester $I)
    {
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/admin/sign-in');
        $I->submitForm('form', ['_username' => 'admin@test.fr', '_password' => 'yolo']);
        $I->seeCurrentUrlEquals('/admin/sign-in');
        $I->see('Invalid credentials.', 'p');
    }

    public function tryLoginHelper(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin');
        $I->seeCurrentUrlEquals('/admin');
        $I->see('Dashboard', 'h2');
    }
}
