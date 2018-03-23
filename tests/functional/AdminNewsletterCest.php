<?php

use Codeception\Util\HttpCode;

class AdminNewsletterCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters');
        $I->seeCurrentUrlEquals('/admin/newsletters');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Liste des actualités', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 5);
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters/add');
        $I->seeCurrentUrlEquals('/admin/newsletters/add');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'newsletter[title]' => 'Test ajout actualité',
            'newsletter[content]' => 'I love unicorns',
        ]);
        $I->see('Test ajout actualité', 'h2');
        $I->amOnPage('/admin/newsletters');
        $I->see('Test ajout actualité', 'a');
        $I->seeNumberOfElements('.ibox-content tr', 6);
    }

    public function tryView(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters/1');
        $I->seeCurrentUrlEquals('/admin/newsletters/1');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->see('Titre de l\'actualité', 'h2');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);

        $I->amOnPage('/admin/newsletters/1/unpublish');
        $I->seeCurrentUrlEquals('/admin/newsletters/1/unpublish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);

        $I->amOnPage('/admin/newsletters');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 4);

        $I->amOnPage('/admin/newsletters/1/publish');
        $I->seeCurrentUrlEquals('/admin/newsletters/1/publish');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);

        $I->amOnPage('/admin/newsletters');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 5);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters/1/edit');
        $I->seeCurrentUrlEquals('/admin/newsletters/1/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'newsletter[title]' => 'Test edit actualité',
            'newsletter[content]' => 'I love unicorns',
        ]);
        $I->seeCurrentUrlEquals('/admin/newsletters/1');
        $I->see('Test edit actualité', 'h2');
        $I->amOnPage('/admin/newsletters');
        $I->see('Test edit actualité', 'a');
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/newsletters');
        $I->seeNumberOfElements('.ibox-content tr', 5);

        $I->amOnPage('/admin/newsletters/1/delete');
        $I->seeCurrentUrlEquals('/admin/newsletters/1/delete');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', []);

        $I->seeCurrentUrlEquals('/admin/newsletters');
        $I->seeNumberOfElements('.ibox-content tr', 4);
    }
}
