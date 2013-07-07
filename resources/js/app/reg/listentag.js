define(function(require) {
    require('placeholder');
    require('rest');
    var util = require('util');

    var $listenedTags, $tagsList;

    function fetchListenedTags() {
        var rest = $.restGet('/api/tag/listened');

        rest.done(function(msg, data) {
            $.each(data || [], function(i, e) {
                addListenedTag(e.tid, e.tag);
            });
        });
    }

    function fetchTags(filter) {
        var url = {
            search: '/api/tag/search',
            sys: '/api/tag/system'
        } [filter ? 'search' : 'sys'];

        var rest = $.restGet(url, {
            tag_name: filter
        });

        rest.done(function(msg, data) {
            $tagsList.empty();
            $.each(data || [], function(i, e) {
                var tpl = '<a href="javascript:;" data-tid="{0}">{1}</a>';
                $tagsList.append(util.formatStr(tpl, e.tid, e.tag));
            });
        });
    }

    function addListenedTag(id, name) {
        var tpl = '<a href="javascript:;" data-tid="{0}">{1}</a>';
        $listenedTags.append(util.formatStr(tpl, id, name));
    }

    function listenTag(tid, name) {
        var rest = $.restPost('/api/tag/listen', {
            tag_id: tid
        });

        rest.done(function(msg, data) {
            addListenedTag(tid, name);
        });
    }

    $(function() {
        $tagsList = $('#tagsList'),
        $listenedTags = $('#listenedTags');


        $('#searchContent').placeholder();

        $('#searchBtn').click(function() {
            fetchTags($('#searchContent').val());
        });

        $tagsList.on('click', 'a', function() {
            var tid = $(this).attr('data-tid');
            listenTag(tid, $(this).text());
        });

        fetchTags();
        fetchListenedTags();
    });

});