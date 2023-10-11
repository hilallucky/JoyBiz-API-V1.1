<?php

/* registration */
$router->post('/member/register', [
    'as' => 'member-register',
    'uses' => 'Members\MemberController@register'
]);
