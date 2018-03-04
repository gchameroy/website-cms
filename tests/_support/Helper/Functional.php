<?php
namespace Helper;

use Codeception\Module\Symfony;

class Functional extends \Codeception\Module
{
    public function amLoggedAsAdmin()
    {
        try {
            /** @var Symfony $I */
            $I = $this->getModule('Symfony');
            $I->amOnPage('/admin');
            $I->seeCurrentUrlEquals('/admin/sign-in');
            $I->submitForm('form', ['_username' => 'admin@test.fr', '_password' => 'admin']);
        } catch (\Exception $e) {
            $this->debug($e->getMessage());
        }
    }
}
