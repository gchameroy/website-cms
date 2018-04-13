<?php

use Codeception\Util\HttpCode;

class AdminUserCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/users');
        $I->seeCurrentUrlEquals('/admin/users');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des clients', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 21);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/users/1');
        $I->seeCurrentUrlEquals('/admin/users/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Informations du client', 'h2');
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/users');
        $I->seeNumberOfElements('.ibox-content tr', 21);

        $I->amOnPage('/admin/users/add');
        $I->seeCurrentUrlEquals('/admin/users/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'user[firstName]' => 'Test add user first name',
            'user[lastName]' => 'Test add user last name',
            'user[company]' => 'Test add user company',
            'user[email]' => 'test_add@test.com',
            'user[plainPassword]' => 'ILoveUnicorns',
            'user[phone]' => '0123456789',
            'user[offer]' => 1,
            'user[billingAddress][firstName]' => 'Test add user first name',
            'user[billingAddress][lastName]' => 'Test add user last name',
            'user[billingAddress][company]' => 'Test add user company',
            'user[billingAddress][address]' => '5 av of unicorns',
            'user[billingAddress][zipCode]' => '12345',
            'user[billingAddress][city]' => 'Unicorn city',
            'user[billingAddress][country]' => 'France',
            'user[deliveryAddress][firstName]' => 'Test add user first name',
            'user[deliveryAddress][lastName]' => 'Test add user last name',
            'user[deliveryAddress][company]' => 'Test add user company',
            'user[deliveryAddress][address]' => '5 av of unicorns',
            'user[deliveryAddress][zipCode]' => '12345',
            'user[deliveryAddress][city]' => 'Unicorn city',
            'user[deliveryAddress][country]' => 'France',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSee('Test add user first name', 'p');

        $I->amOnPage('/admin/users');
        $I->canSee('Test add user first name', 'td');
        $I->seeNumberOfElements('.ibox-content tr', 22);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/users/1/edit');
        $I->seeCurrentUrlEquals('/admin/users/1/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'user_edit[firstName]' => 'Test edit user first name',
            'user_edit[lastName]' => 'Test edit user last name',
            'user_edit[company]' => 'Test edit user company',
            'user_edit[email]' => 'test_edit@test.com',
            'user_edit[phone]' => '0123456789',
            'user_edit[offer]' => 1,
            'user_edit[billingAddress][firstName]' => 'Test edit user first name',
            'user_edit[billingAddress][lastName]' => 'Test edit user last name',
            'user_edit[billingAddress][company]' => 'Test edit user company',
            'user_edit[billingAddress][address]' => '5 av of unicorns',
            'user_edit[billingAddress][zipCode]' => '12345',
            'user_edit[billingAddress][city]' => 'Unicorn city',
            'user_edit[billingAddress][country]' => 'France',
            'user_edit[deliveryAddress][firstName]' => 'Test edit user first name',
            'user_edit[deliveryAddress][lastName]' => 'Test edit user last name',
            'user_edit[deliveryAddress][company]' => 'Test edit user company',
            'user_edit[deliveryAddress][address]' => '5 av of unicorns',
            'user_edit[deliveryAddress][zipCode]' => '12345',
            'user_edit[deliveryAddress][city]' => 'Unicorn city',
            'user_edit[deliveryAddress][country]' => 'France',
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/users/1');

        $I->amOnPage('/admin/users/1');
        $I->canSee('Test edit user first name', 'p');
    }
}
