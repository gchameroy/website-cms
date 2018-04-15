<?php

use Codeception\Util\HttpCode;

class AdminOrderCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/orders');
        $I->seeCurrentUrlEquals('/admin/orders');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des commandes', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 80);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/orders/1');
        $I->seeCurrentUrlEquals('/admin/orders/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Order #1', 'h2');
    }
}
