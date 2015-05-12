(function () {
    tinymce.PluginManager.add('kamoha_tc_button', function (editor, url) {
        editor.addButton('kamoha_tc_button', {
            title: editor.getLang('kamoha_tc_button.button_label'),
            icon: 'icon dashicons-clock', /*dashicons-cart*/
            onclick: function () {
                editor.windowManager.open({
                    title: editor.getLang('kamoha_tc_button.button_label'),
                    body: [{
                            type: 'textbox',
                            name: 'title',
                            label: editor.getLang('kamoha_tc_button.first_option_text')
                        },
                        {
                            type: 'textbox',
                            name: 'price_list',
                            label: editor.getLang('kamoha_tc_button.price_list')
                        },
                        {
                            type: 'textbox',
                            name: 'price_text',
                            label: editor.getLang('kamoha_tc_button.price_text')
                        },
                        {
                            type: 'textbox',
                            name: 'payments',
                            label: editor.getLang('kamoha_tc_button.payments_text')
                        }
                    ],
                    onsubmit: function (e) {
                        /* enclose string in double quotes, so attribute values can be enclosed in single quotes, so that double quotes can be used in values*/
                        editor.insertContent("[kamoha_pelepay_form first_option='" + e.data.title + "' price_list='" + e.data.price_list + "' price_text='" + e.data.price_text + "'" + " payments='" + e.data.payments + "' ]");
                        /* old code, enclosed in single quotes:
                         * editor.insertContent('[kamoha_pelepay_form first_option="' + e.data.title + '" price_list="' + e.data.price_list + '" price_text="' + e.data.price_text + '"'+ ' payments="' + e.data.payments + '" ]');*/
                    }
                });
            }
        });
    });
})();

function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };

    return text.replace(/[&<>"']/g, function (m) {
        return map[m];
    });
}