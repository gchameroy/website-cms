<?php

use Codeception\Util\HttpCode;

class AdminStaticPageCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages');
        $I->seeCurrentUrlEquals('/admin/static-pages');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des pages', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 4);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages/2');
        $I->seeCurrentUrlEquals('/admin/static-pages/2');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Titre de la page statique n°1', 'h2');
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages/add');
        $I->seeCurrentUrlEquals('/admin/static-pages/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'static_page[title]' => 'Test ajout title',
            'static_page[content]' => 'I love unicorns',
        ]);
        $I->see('Test ajout title', 'h2');
        $I->amOnPage('/admin/static-pages');
        $I->see('Test ajout title', 'a');
        $I->seeNumberOfElements('.ibox-content tr', 5);
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);

        $I->amOnPage('/admin/static-pages/2/unpublish');
        $I->seeCurrentUrlEquals('/admin/static-pages/2/unpublish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);

        $I->amOnPage('/admin/static-pages');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 3);

        $I->amOnPage('/admin/static-pages/2/publish');
        $I->seeCurrentUrlEquals('/admin/static-pages/2/publish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);

        $I->amOnPage('/admin/static-pages');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 4);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages/2/edit');
        $I->seeCurrentUrlEquals('/admin/static-pages/2/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'static_page[title]' => 'Test edit title',
            'static_page[content]' => 'I love unicorns',
        ]);
        $I->see('Test edit title', 'h2');
        $I->amOnPage('/admin/static-pages');
        $I->see('Test edit title', 'a');
        $I->seeNumberOfElements('.ibox-content tr', 4);
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/static-pages');
        $I->seeNumberOfElements('.ibox-content tr', 4);
        $I->amOnPage('/admin/static-pages/2/delete');
        $I->seeCurrentUrlEquals('/admin/static-pages/2/delete');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);
        $I->seeCurrentUrlEquals('/admin/static-pages');
        $I->seeNumberOfElements('.ibox-content tr', 3);
    }
}
