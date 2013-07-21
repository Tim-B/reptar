document.observe("dom:loaded", function() {
    $$(".reptar_rate li").each(function (element) {
        element.observe('click', function(event) {
            rateUser(element.up('ul').readAttribute('rel'), element.readAttribute('rel'));
        });
    });
});


function rateUser(uid, value) {
    new Ajax.Request('misc.php', {
        method:'post',
        parameters: {action: 'reptar_rate', postkey: my_post_key, uid: uid, value: value},
        onSuccess: function(transport, json){
            console.log(json);
        }
    });
}