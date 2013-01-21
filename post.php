<!DOCTYPE html>
<html>
   <head>
       <link href="fb.css" rel="stylesheet" type="text/css" />
       <link href="css3-facebook-buttons.css" rel="stylesheet" type="text/css" />
       <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
   </head>
   <body>

    <div id="fb-root"></div>
    <script>
      // Load the SDK Asynchronously
      (function(d){
         var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement('script'); js.id = id; js.async = true;
         js.src = "//connect.facebook.net/en_US/all.js";
         ref.parentNode.insertBefore(js, ref);
       }(document));

      // Init the SDK upon load
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '73073806064', // App ID
          channelUrl : '//'+window.location.hostname+'/channel.js', // Path to your Channel File
          status     : true, // check login status
          cookie     : true, // enable cookies to allow the server to access the session
          xfbml      : true  // parse XFBML
        });

        // listen for and handle auth.statusChange events
        FB.Event.subscribe('auth.statusChange', function(response) {
          if (response.authResponse) {
            // user has auth'd your app and is logged into Facebook
            FB.api('/me', function(me){
              if (me.name) {
                document.getElementById('auth-displayname').innerHTML = me.name;
              }
            })
            document.getElementById('auth-loggedout').style.display = 'none';
            document.getElementById('auth-loggedin').style.display = 'block';
          } else {
            // user has not auth'd your app, or is not logged into Facebook
            document.getElementById('auth-loggedout').style.display = 'block';
            document.getElementById('auth-loggedin').style.display = 'none';
          }
        });

        // respond to clicks on the login and logout links
        document.getElementById('auth-loginlink').addEventListener('click', function(){
          FB.login();
        });
        document.getElementById('auth-logoutlink').addEventListener('click', function(){
          FB.logout();
        }); 
      } 
    </script>



      <div class="fbbody">
         <h2>Project Euler Wall Poster</h2>

         <div id="contents">
         <div id="auth-status">
            <div id="auth-loggedout">
               <a href="#" class="uibutton" id="auth-loginlink">Login</a>
            </div>
            <div id="auth-loggedin" style="display:none">
               Hi, <span id="auth-displayname"></span>  
               <a href="#" class="uibutton" id="auth-logoutlink">logout</a>
            </div>
         </div>

         <p>This app will post your solved problem update on your wall. Use it responsibly! You will be embarrassed if your friends caught you for the problems you did not solve!!</p>
         <label for="pid">Problem ID:</label>
         <input type="text" id="pid"/>
         <div id="desc"></div>
         <div class="update" id="update_section" >
            <div id="update_text"></div>
            <button class="uibutton" id="update_btn">Post Update</button>
         </div>
         </div>  <!-- contents -->

         <div id="update_status">
         </div>
      </div>


      <script>
          var solvedId = null,
              solvedDesc = null;
          

          function clear() {
              solvedId = null;
              $("#desc").html("");  
              $("#update_section").css({visibility: "hidden", display: "none"});
          }

          function showButton(n) {
              $("#update_text").html("Yes, I really solved problem #" +n + " myself!");
              $("#update_section").css({visibility: "visible", display: "block"});
          }
          
          $(document).ready(function() {
             $("#pid").keyup(function () {
                 var id = $(this).val();
                 if (id != "") {
                    solvedId = id;
                    $.ajax({ type: "GET",  url: "prob.php?id=" + id }).done(
                       function( msg ) {
                          if (msg != "0") {
                             solvedDesc = msg;
                             $("#desc").html('<div class="fbgreybox" id="desc_content">' + msg + '</div>');                
                             showButton(id);        
                          } else {
                            clear();
                          }  
                       }
                    );
                 } else {                    
                    clear();
                 }
             });

             $("#update_btn").click(function() {
                 $("#contents").css({visibility: "hidden", display: "none"});
                 $("#update_status").html('Working...');
                 var body = 'Solved problem #' + solvedId + ' on Project Euler: ' + solvedDesc;
                 var link = 'http://geekraj.com/eulerfb/redirect.php?id=' + solvedId;
                 FB.api('/me/feed', 'post', { message: body, link: link, picture: "http://projecteuler.net/images/euler_portrait.png" }, function(response) {
                    if (!response || response.error) {
                      $("#update_status").html('<div class="fberrorbox" style="width: 500px;">An error occurred!</div>');
                    } else {
                      var msg = 'Kudos on solving problem #' + solvedId + '. Now your friends also know about it! Spread the word on Project Euler!';
                      $("#update_status").html('<div class="fbinfobox" style="width: 500px;">' + msg + '</div>');
                    }
                 });
             });
          });

      </script>
   </body>
</html>