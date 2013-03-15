namespace Unislug\N2Bundle\User;

use Serializable;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class User implements UserInterface, Serializable, AdvancedUserInterface {
    private $username;
    private $password;
    public function __construct($username, $password) { $this->username = $username; $this->password = $password; }
    public function getUsername() {}
    public function getSalt() {}
    public function getPassword() { return $password; }
    public function getRoles() { return ['ROLE_USER']; };
    public function eraseCredentials() {}
    public function serialize() { return serialize([ $this->username; ]) }
    public function unserialize($serialized) { list($this->username) = unserialize($serialized); }
    
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}

# Embryo code