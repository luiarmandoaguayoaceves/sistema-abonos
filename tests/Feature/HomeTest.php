<?php

it('carga el dashboard principal correctamente', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Dashboard general');
});