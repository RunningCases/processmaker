<?php

namespace Tests\unit\framework\src\Maveriks\Pattern\Mvc;

use Maveriks\Pattern\Mvc\SmartyView;
use Smarty;
use Tests\TestCase;

class SmartyViewTest extends TestCase
{
    /**
     * Test the constructor of the SmartyView class
     *
     * @test
     *
     * @covers \Maveriks\Pattern\Mvc\SmartyView::__construct()
     */
    public function it_should_test_the_class_constructor()
    {
        // Instance class SmartyView
        $smartyView = new SmartyView();

        // Get "smarty" property
        $smartyInstance = $smartyView->getSmarty();

        // Assert correct class instance
        $this->assertInstanceOf(Smarty::class, $smartyInstance);

        // Assert that the required folders exist
        $this->assertDirectoryExists($smartyInstance->compile_dir);
        $this->assertDirectoryExists($smartyInstance->cache_dir);
    }
}
