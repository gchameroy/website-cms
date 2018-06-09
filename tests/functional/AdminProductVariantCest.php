<?php

use Codeception\Util\HttpCode;

class AdminProductVariantCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1');
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeNumberOfElements('.tabs-container [data-toggle="tab"]', 3);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1');
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSee('Pot de 50g', 'h2');
        $I->canSee('P1806-001', 'h3');
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1');
        $I->seeNumberOfElements('.tabs-container [data-toggle="tab"]', 3);
        $I->amOnPage('/admin/products/1/add-variant');
        $I->seeCurrentUrlEquals('/admin/products/1/add-variant');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Ajouter une variante', 'h4');
        $I->submitForm('form', [
            'product_variant[variantName]' => 'Test add variant unicorn',
            'product_variant[reference]' => 'UNI-001',
            'product[product_price_1]' => '10',
            'product[product_price_2]' => '8'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->seeNumberOfElements('.tabs-container [data-toggle="tab"]', 4);
        $I->canSee('Test add variant unicorn', '.tabs-container a');
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/products/1/edit-variant?variant=1');
        $I->seeCurrentUrlEquals('/admin/products/1/edit-variant?variant=1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Editer une variante', 'h4');
        $I->submitForm('form', [
            'product_variant[variantName]' => 'Test edit variant unicorn',
            'product_variant[reference]' => 'UNI-001',
            'product_variant[product_price_1]' => '10',
            'product_variant[product_price_2]' => '8'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/products/1');
        $I->canSee('Test edit variant unicorn', 'h2');
    }
}
