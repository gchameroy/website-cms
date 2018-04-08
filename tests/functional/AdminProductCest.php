<?php

use Codeception\Util\HttpCode;

class AdminProductCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products');
        $I->seeCurrentUrlEquals('/admin/products');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des produits', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 25);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1');
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Product 1', 'h2');
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products');
        $I->seeNumberOfElements('.ibox-content tr', 25);
        $I->amOnPage('/admin/products/add');
        $I->seeCurrentUrlEquals('/admin/products/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Ajouter un produit', 'h2');
        $I->submitForm('form', [
            'product[label]' => 'Test add unicorn',
            'product[reference]' => 'UNI-001',
            'product[product_price_1]' => '10',
            'product[product_price_2]' => '8',
            'product[category]' => 1
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Test add unicorn', 'h2');
        $I->amOnPage('/admin/products');
        $I->seeNumberOfElements('.ibox-content tr', 26);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1');
        $I->see('Product 1', 'h2');
        $I->amOnPage('/admin/products/1/edit');
        $I->seeCurrentUrlEquals('/admin/products/1/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Product 1', 'h2');
        $I->submitForm('form', [
            'product[label]' => 'Test edit unicorn',
            'product[reference]' => 'UNI-edit-001',
            'product[product_price_1]' => '10',
            'product[product_price_2]' => '8',
            'product[category]' => 1
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->see('Test edit unicorn', 'h2');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products');
        $I->seeCurrentUrlEquals('/admin/products');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);
        $I->dontSeeInRepository(\AppBundle\Entity\Product::class, [
           'id' => 1,
            'publishedAt' => null
        ]);
        $I->submitForm('tr:first-child form:nth-child(1)', []);
        $I->seeInRepository(\AppBundle\Entity\Product::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/products');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 24);
        $I->submitForm('tr:first-child form:nth-child(1)', []);
        $I->seeCurrentUrlEquals('/admin/products');
        $I->dontSeeInRepository(\AppBundle\Entity\Product::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/products');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 25);
    }
}
