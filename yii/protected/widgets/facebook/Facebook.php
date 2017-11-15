<?php
/**
 * class Facebook
 * @author Igor Ivanović 
 */
class Facebook extends CWidget{

    public $appId;
    public $status = true;
    public $cookie = true;
    public $xfbml  = true;
    public $oauth  = true;
    public $userSession;
    public $facebookButtonTitle = "Facebook Connect";
    public $fbLoginButtonId     = "fb_login_button_id";
    public $logoutButtonId      = "your_logout_button_id";
    public $facebookLoginUrl    = "social/login";
    public $facebookPermissions = "email, public_profile, user_friends";
    
	
    /**
    * Run Widget
    */
    public function run()
    {
        $this->facebookLoginUrl     = Yii::app()->createAbsoluteUrl($this->facebookLoginUrl);
        $this->userSession          = Yii::app()->session->sessionID;
	    $this->render('login');
	    $this->renderJavascript();
    }
    
    
    /**
    * Render necessary facebook  javascript
    */
    private function renderJavascript()
    {
    	$profileUrl = Yii::app()->createUrl('user/profile');
$script=<<<EOL
        window.fbAsyncInit = function() {
            FB.init({   appId: '{$this->appId}', 
                        status: {$this->status}, 
                        cookie: {$this->cookie},
                        xfbml: {$this->xfbml},
                        oauth: {$this->oauth}
            });

            function updateButton(response) {

                var b = document.getElementById("{$this->fbLoginButtonId}");
               
                    b.onclick = function(){
                        FB.login(function(response) {
                                    if(response.authResponse) {
                                            FB.api('/me', function(user) {
                                                console.log(user);
                                                $.ajax({
                                                    type : 'get',
                                                    url  : '{$this->facebookLoginUrl}',
                                                    data : ( { 
                                                        action   : 'login',
                                                        type   : 'facebook',
                                                        name     :   user.first_name, 
                                                        surname  :   user.last_name,
                                                        username :   user.name,
                                                        id       :   user.id, 
                                                        email    :   user.email, 
                                                        session  :   "{$this->userSession}" 
                                                    } ),
                                                    dataType : 'json',
                                                    success : function( data ){
                                                        if (data=1) {
                                                            window.location="{$profileUrl}";
                                                        }
                                                    }
                                                });

                                            });	   
                                    }
                        }, {scope: '{$this->facebookPermissions}'});	
                    }
                
            }
                        
            FB.getLoginStatus(updateButton);
            FB.Event.subscribe('auth.statusChange', updateButton);	

            var c = document.getElementById("{$this->logoutButtonId}");
            if(c){
                c.onclick = function(){
                    FB.logout();
                }
            }
        };
        
        
        (function(d){var e,id = "fb-root";if( d.getElementById(id) == null ){e = d.createElement("div");e.id=id;d.body.appendChild(e);}}(document));
        (function(d){var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];if (d.getElementById(id)) {return;} js = d.createElement('script'); js.id = id; js.async = true; js.src = "//connect.facebook.net/en_US/all.js"; ref.parentNode.insertBefore(js, ref); }(document));
EOL;

        Yii::app()->clientScript->registerScript('facebook-connect',$script );
    }
}
