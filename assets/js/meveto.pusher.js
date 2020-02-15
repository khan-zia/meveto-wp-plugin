// Enable pusher logging - don't include this in production
Pusher.logToConsole = true;
if(data.userId && data.key)
{
    var pusher = new Pusher(data.key, {
        cluster: data.cluster,
        authEndpoint: data.authEndpoint,
        forceTLS: true
    });
    var channel = pusher.subscribe('private-Meveto-Kill.'+data.userId);
    var home = data.homeUrl;
    channel.bind('logout', function(data) {
        Toastify({
            text: "You have been logged out from this website using your Meveto dashboard.",
            duration: 3000,
            destination: "https://meveto.com",
            newWindow: true,
            close: false,
            gravity: "top",
            position: 'center',
            backgroundColor: "linear-gradient(to right, #0079bb, #3CC98E)",
            stopOnFocus: true,
            className: 'meveto-info-toaster',
            onClick: function(){}
        }).showToast();
        setTimeout(function(){
            window.location.replace(home);
        }, 3000)
    });
}