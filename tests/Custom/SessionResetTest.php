<?php

namespace Mink\WebdriverClassDriver\Tests\Custom;

use Behat\Mink\Tests\Driver\TestCase;

class SessionResetTest extends TestCase
{
    /**
     * @dataProvider initialWindowNameDataProvider
     */
    public function testSessionResetClosesWindows(?string $initialWindowName): void
    {
        $session = $this->getSession();
        $session->visit($this->pathTo('/window.html'));

        if (null !== $initialWindowName) {
            $session->executeScript('window.name = "'.$initialWindowName.'";');
        }

        $page = $session->getPage();

        $page->clickLink('Popup #1');
        $page->clickLink('Popup #2');

        $expectedInitialWindowName = $session->evaluateScript('window.name');

        $windowNames = $session->getWindowNames();
        $this->assertCount(3, $windowNames);

        $session->reset();

        $windowNames = $session->getWindowNames();
        $this->assertCount(1, $windowNames);

        $actualInitialWindowName = $session->evaluateScript('window.name');
        $this->assertEquals($expectedInitialWindowName, $actualInitialWindowName);
    }

    public static function initialWindowNameDataProvider(): array
    {
        return array(
            'no name' => array(null),
            'non-empty name' => array('initial-window'),
        );
    }

    /**
     * @after
     */
    protected function resetSessions()
    {
        $session = $this->getSession();

        // Stop the session instead of resetting, because resetting behavior is being tested.
        if ($session->isStarted()) {
            $session->stop();
        }

        parent::resetSessions();
    }
}
