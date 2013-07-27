define(function(require) {
    var Talk = require('app/common/talk');
    require('rest');

    $(function () {
        var arrowNext = $('.arrow-next'), arrowPrev = $('.arrow-prev');
        $(document).on('click', '.arrow-next', function () {
            var _this = $(this);
            fadeobj = _this.closest('.slideritem');
            if (fadeobj.next('.slideritem').index() > 0) {
                fadeobj.fadeOut();
                fadeobj.next('.slideritem').fadeIn();
                $('.quark-button').show();
            }
            else {
                $('.slideritem').eq(0).fadeIn().siblings('.slideritem').fadeOut();
                $('.quark-button').hide();
            }
        });
        $(document).on('click', '.arrow-prev', function () {
            var _this = $(this);
            fadeobj = _this.closest('.slideritem');
            if (fadeobj.prev('.slideritem').index() > 0) {
                fadeobj.fadeOut();
                fadeobj.prev('.slideritem').fadeIn();
                $('.quark-button').show();
            }
            else {
                $('.slideritem').eq(0).fadeIn().siblings('.slideritem').fadeOut()
                $('.quark-button').hide();
            }
        });
        $('.collection-button').bind('click', function () {
            $(this).addClass('on');
        });
        $('.itemshow').each(function () {
            var _this = $(this), _index = _this.index(), size = $('.itemshow').size();
            //(_index+1)+'/'+size
            _this.append('<div class="curnum"><a href="javascript:;" class="arrow-prev">&lt;&lt;</a>' + _index + '/' + size + '<a href="javascript:;" class="arrow-next">&gt;&gt;</a></div>');
        });
    });

    function initOpts(id) {
        $(function() {
            $(document).on('click', '.js-collectbtn', function() {
                var $count = $(this).find('.js-count');

                var rest = $.restPost('/api/album/listen', {
                    album_id: id
                });

                rest.done(function(msg) {
                    $('.js-collectBtn').addClass('checked');

                    if ($count.length) {
                        $count.text(($count.text() * 1 || 0) + 1);
                    }
                });

                rest.fail(function(msg) {
                    alert(msg || '收藏失败');
                });
            });

            $(document).on('click', '.js-replaybtn', function() {
                var talk = new Talk({
                    fetchUrl: '/api/album/replylist',
                    title: '评论',
                    params: {
                        album_id: id
                    },
                    sendUrl: '/api/album/reply'
                });
                talk.showTalk();
            });
        });
    }

    return {
        initOpts: initOpts
    }
});