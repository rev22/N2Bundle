namespace Unislug\N2Bundle\User;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class WsseUserToken extends AbstractToken {
    private $created;
    private $digest;
    private $nonce;
    
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        
        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }
    
    public function getCredentials()
    {
        return '';
    } 
}

}