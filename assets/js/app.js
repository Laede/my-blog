const $ = require('jquery');
require('bootstrap');
require('./jqBootstrapValidation');
require('./contact_me');
require('./clean-blog');
require('bootstrap-tagsinput');
require('./typeahead');

$('input[data-role="my_tags"]').tagsinput({
    typeaheadjs: {
        source(query,syncresults){
            let a = JSON.parse($('input[data-role="my_tags"]').attr('data-items'));
            let b = [];
            for(let i=0;i<a.length; i++){
                let item = a[i];
                if(item.indexOf(query) > -1){
                    b.push(item);
                }
            }
            return syncresults(b);
        }
    },
});