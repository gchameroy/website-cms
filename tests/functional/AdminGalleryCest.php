<?php

use Codeception\Util\HttpCode;

class AdminGalleryCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery');
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->see('Liste des photos', 'h2');
        $I->seeNumberOfElements('.ibox-content tr', 3);
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery/1/edit');
        $I->seeCurrentUrlEquals('/admin/gallery/1/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'gallery_edit[title]' => 'Test Edit Unicorn',
            'gallery_edit[description]' => 'test'
        ]);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->see('Test Edit Unicorn', 'a');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery');
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);
        $I->dontSeeInRepository(\AppBundle\Entity\Gallery::class, [
           'id' => 1,
            'publishedAt' => null
        ]);
        $I->submitForm('tr:last-child form:nth-child(1)', []);
        $I->seeInRepository(\AppBundle\Entity\Gallery::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 2);
        $I->submitForm('tr:last-child form:nth-child(1)', []);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->dontSeeInRepository(\AppBundle\Entity\Gallery::class, [
            'id' => 1,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 3);
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery');
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content tr', 3);
        $I->submitForm('tr:first-child form:nth-child(3)', []);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content tr', 2);
    }
}
