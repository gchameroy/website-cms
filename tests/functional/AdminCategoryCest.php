<?php

use Codeception\Util\HttpCode;

class AdminCategoryCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories');
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->see('Liste des catégories', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 4);
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories');
        $I->seeNumberOfElements('.ibox-content tr', 4);

        $I->amOnPage('/admin/categories/add');
        $I->seeCurrentUrlEquals('/admin/categories/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'category[label]' => 'Test add category',
            'category[description]' => 'I love unicorns'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->see('Test add category', 'td');
        $I->seeNumberOfElements('.ibox-content tr', 5);
    }

    public function tryAddAjax(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories');
        $I->seeNumberOfElements('.ibox-content tr', 4);

        $I->amOnPage('/admin/categories/add-modal');
        $I->seeCurrentUrlEquals('/admin/categories/add-modal');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'category[label]' => 'Test add modal category',
            'category[description]' => 'I love unicorns'
        ]);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeCurrentUrlEquals('/admin/categories/add-modal');
        $I->see('"success":true');
        $I->see('"label":"Test add modal category"');

        $I->amOnPage('/admin/categories');
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->see('Test add modal category', 'td');
        $I->seeNumberOfElements('.ibox-content tr', 5);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories/2/edit');
        $I->seeCurrentUrlEquals('/admin/categories/2/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
                'category[label]' => 'Test edit category',
                'category[description]' => 'Test'
            ]);
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->see('Test edit category', 'td');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories');
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);
        $I->dontSeeInRepository(\AppBundle\Entity\Category::class, [
            'id' => 1,
            'publishedAt' => null
        ]);
        $I->amOnPage('/admin/categories/1/unpublish');
        $I->seeCurrentUrlEquals('/admin/categories/1/unpublish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);
        $I->seeInRepository(\AppBundle\Entity\Category::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/categories');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 3);
        $I->amOnPage('/admin/categories/1/publish');
        $I->seeCurrentUrlEquals('/admin/categories/1/publish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->dontSeeInRepository(\AppBundle\Entity\Category::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/categories');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 4);
    }

    /*public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/categories');
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->seeNumberOfElements('.ibox-content tr', 3);
        $I->submitForm('tr:first-child form:nth-child(2)', []);
        $I->seeCurrentUrlEquals('/admin/categories');
        $I->seeNumberOfElements('.ibox-content tr', 2);
    }*/
}
