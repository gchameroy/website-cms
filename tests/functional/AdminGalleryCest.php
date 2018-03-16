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
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery/18/edit');
        $I->seeCurrentUrlEquals('/admin/gallery/18/edit');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->submitForm('form', [
            'gallery_edit[title]' => 'photo',
            'gallery_edit[description]' => 'test'
        ]);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->see('photo', 'a');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery');
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);
        $I->dontSeeInRepository(\AppBundle\Entity\Gallery::class, [
           'id' => 18,
            'publishedAt' => null
        ]);
        $I->submitForm('tr:first-child form:nth-child(1)', []);
        $I->seeInRepository(\AppBundle\Entity\Gallery::class, [
            'id' => 18,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 11);
        $I->submitForm('tr:first-child form:nth-child(1)', []);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->dontSeeInRepository(\AppBundle\Entity\Gallery::class, [
            'id' => 18,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 12);
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/gallery');
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content tr', 12);
        $I->submitForm('tr:first-child form:nth-child(3)', []);
        $I->seeCurrentUrlEquals('/admin/gallery');
        $I->seeNumberOfElements('.ibox-content tr', 11);
    }
}
