<?php

namespace distantnative\Retour;

class DialogsTest extends TestCase
{
    public function testCreateRedirectDialog(): void
    {
        $dialog = $this->dialog('retour.redirect.create');
        $this->assertSame('retour/redirects/create', $dialog['pattern']);

        $load = $dialog['load']();
        $this->assertSame('k-form-dialog', $load['component']);
        $this->assertIsArray($load['props']['fields']);
        $this->assertArrayNotHasKey('value', $load['props']);

        $_GET['from'] = 'foo';
        $redirects = Plugin::instance()->redirects();
        $this->assertSame(0, $redirects->count());
        $submit = $dialog['submit']();
        $this->assertSame(1, $redirects->count());
        unset($_GET['from']);
    }

    public function testDeleteRedirectDialog(): void
    {
        $dialog = $this->dialog('retour.redirect.delete');
        $this->assertSame('retour/redirects/(:any)/delete', $dialog['pattern']);

        $redirects = Plugin::instance()->redirects();
        $this->assertSame(0, $redirects->count());
        $redirects->prepend(new Redirect(['from' => 'foo']));
        $this->assertSame(1, $redirects->count());

        $load = $dialog['load']('foo');
        $this->assertSame('k-remove-dialog', $load['component']);

        $submit = $dialog['submit']('foo');
        $this->assertSame(0, $redirects->count());
    }

    public function testEditRedirectDialog(): void
    {
        $_GET['from'] = 'bar';
        $dialog = $this->dialog('retour.redirect.edit');
        $this->assertSame('retour/redirects/(:any)/edit', $dialog['pattern']);

        $redirects = Plugin::instance()->redirects();
        $this->assertSame(0, $redirects->count());
        $redirects->prepend(new Redirect(['from' => 'foo']));
        $this->assertSame(1, $redirects->count());
        $this->assertSame('foo', $redirects->first()->from());

        $load = $dialog['load']('foo');
        $this->assertSame('k-form-dialog', $load['component']);
        $this->assertIsArray($load['props']['fields']);
        $this->assertIsArray($load['props']['value']);
        $this->assertSame('foo', $load['props']['value']['from']);

        $submit = $dialog['submit']('foo');
        $this->assertSame(1, $redirects->count());
        $this->assertSame('bar', $redirects->first()->from());
        unset($_GET['from']);
    }

    protected function dialog(string $key): array
    {
        $dialogs = require dirname(__DIR__, 2) . '/src/extensions/dialogs.php';
        return $dialogs[$key];
    }
}
