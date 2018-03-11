<?php

class AdminMenuCest
{
    public function tryList(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/menus');
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->see('Présentation', 'a');
        $I->see('Confitures', 'a');
        $I->see('Gelées', 'a');
        $I->see('Crème de cassis', 'a');
    }

    public function tryAdd(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/menus/add');
        $I->seeCurrentUrlEquals('/admin/menus/add');
        $I->submitForm('form', [
            'menu[pageName]' => 'Actualités',
            'menu[routeName]' => 'front_newsletters',
            'menu[routeSlug]' => ''
        ]);
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->see('Actualités', 'a');
    }

    public function tryEdit(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/menus/4/edit');
        $I->seeCurrentUrlEquals('/admin/menus/4/edit');
        $I->submitForm('form', [
            'menu[pageName]' => 'Crèmes de cassis',
            'menu[routeName]' => 'front_category',
            'menu[routeSlug]' => 'creme-de-cassis'
        ]);
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->see('Crèmes de cassis', 'a');
    }

    public function tryUnpublishPublish(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/menus');
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 0);

        $I->amOnPage('/admin/menus/4/unpublish');
        $I->seeCurrentUrlEquals('/admin/menus/4/unpublish');
        $I->dontSeeInRepository(\AppBundle\Entity\Menu::class, [
           'id' => 4,
            'publishedAt' => null
        ]);
        $I->submitForm('form', []);
        $I->seeInRepository(\AppBundle\Entity\Menu::class, [
            'id' => 4,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/menus');
        $I->seeNumberOfElements('.ibox-content .label.label-default', 1);
        $I->seeNumberOfElements('.ibox-content .label.label-info', 3);

        $I->amOnPage('/admin/menus/4/publish');
        $I->seeCurrentUrlEquals('/admin/menus/4/publish');
        $I->submitForm('form', []);
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->dontSeeInRepository(\AppBundle\Entity\Menu::class, [
            'id' => 4,
            'publishedAt' => null
        ]);

        $I->seeCurrentUrlEquals('/admin/menus');
        $I->seeNumberOfElements('.ibox-content .label.label-info', 4);
    }

    public function tryDelete(FunctionalTester $I)
    {
        $I->amLoggedAsAdmin();
        $I->amOnPage('/admin/menus');
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->seeNumberOfElements('.ibox-content tr', 4);
        $I->amOnPage('/admin/menus/4/delete');
        $I->seeCurrentUrlEquals('/admin/menus/4/delete');
        $I->submitForm('form', []);
        $I->seeCurrentUrlEquals('/admin/menus');
        $I->seeNumberOfElements('.ibox-content tr', 3);
    }
}
