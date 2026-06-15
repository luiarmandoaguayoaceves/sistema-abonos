<?php

it('carga la página principal correctamente', function () {
    $this->get('/')
        ->assertOk();
});
