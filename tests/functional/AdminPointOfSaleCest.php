<?php

use Codeception\Util\HttpCode;

class AdminPointOfSaleCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/point-of-sale');
        $I->seeCurrentUrlEquals('/admin/point-of-sale');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des points de ventes', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 3);
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/point-of-sale/add');
        $I->seeCurrentUrlEquals('/admin/point-of-sale/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'point_of_sale[address][company]' => 'Test ajout Wololo-companie',
            'point_of_sale[address][address]' => '9 place du docteur michel',
            'point_of_sale[address][zipCode]' => '52800',
            'point_of_sale[address][city]' => 'Foulain',
        ]);
        $I->seeCurrentUrlEquals('/admin/point-of-sale');
        $I->see('Test ajout Wololo-companie', 'strong');
        $I->seeNumberOfElements('.ibox-content tr', 4);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/point-of-sale/1/edit');
        $I->seeCurrentUrlEquals('/admin/point-of-sale/1/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'point_of_sale[address][company]' => 'Test edit Wololo-companie',
            'point_of_sale[address][address]' => '9 place du docteur michel',
            'point_of_sale[address][zipCode]' => '52800',
            'point_of_sale[address][city]' => 'Foulain',
        ]);
        $I->seeCurrentUrlEquals('/admin/point-of-sale');
        $I->see('Test edit Wololo-companie', 'strong');
        $I->seeNumberOfElements('.ibox-content tr', 3);
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/point-of-sale');
        $I->seeNumberOfElements('.ibox-content tr', 3);
        $I->submitForm('tr:first-child form', []);
        $I->seeCurrentUrlEquals('/admin/point-of-sale');
        $I->seeNumberOfElements('.ibox-content tr', 2);
    }
}
