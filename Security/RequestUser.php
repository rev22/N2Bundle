<?php

namespace Unislug\N2Bundle\Security;

class RequestUser {
    protected $user;
    protected $roles;

    public function __construct() {
        $roles = [];
    }

    function getUser() {
        return $user;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function hasRole($role) {
        return isset($this->roles);
    }

    function giveRole($role) {
        $this->roles[$role] = 1;
    }

    function annulRole($role) {
        unset($this->roles[$role]);
    }
}
