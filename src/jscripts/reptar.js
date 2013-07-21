document.observe("dom:loaded", function () {
    $$(".reptar_rate li").each(function (element) {
        element.observe('click', function (event) {
            rateUser(element.up('ul').readAttribute('rel'), element.readAttribute('rel'));
        });
    });
});


function rateUser(uid, value) {
    new Ajax.Request('xmlhttp.php', {
        method: 'post',
        parameters: {action: 'reptar_rate', my_post_key: my_post_key, uid: uid, value: value},
        onSuccess: function (transport, json) {
            resetValues(uid, value);
        }
    });
}

function resetValues(uid, value) {
    $$('ul.reptar_rate[rel=' + uid + ']').each(function (list) {
        list.childElements().each(function (item) {
            console.log(item);
            item.removeClassName('active');
            if (item.match('li[rel=' + value + ']')) {
                item.addClassName('active');
            }
        });
    });
}