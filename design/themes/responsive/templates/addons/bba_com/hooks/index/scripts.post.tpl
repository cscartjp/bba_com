{*読み込むJavaScriptファイルを指定します*}
{if $runtime.controller == 'community' || $runtime.controller == 'community_groups'}
    <script src="https://cdn.jsdelivr.net/npm/lazyload@2.0.0-rc.2/lazyload.min.js"></script>
    <script>
        (function (_, $) {
            $.ceEvent('on', 'ce.commoninit', function (context) {
                $("img.lazyload").lazyload();
            });
        })(Tygh, Tygh.$);
    </script>
{/if}

{script src="js/addons/bba_com/func.js"}