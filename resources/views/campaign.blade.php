@if(preg_match('/(iphone|ipod|ipad)+/i', request()->server('HTTP_USER_AGENT')))
setTimeout(function () {
    const handler = function () {
        const time = (new Date()).getTime();
        const a = document.createElement('a');
        const id = 'ics-' + time;
        a.setAttribute('id', id);
        a.setAttribute('href', 'webcal:{{ str_replace(['http://', 'https://'], '', route('subscribe', ['campaign' => $campaign, 'uuid' => $uuid])) }}');

        const body = document.querySelector('body');
        body.append(a);

        window.isClicked = false;
        body.addEventListener('click', function () {
            if (window.isClicked) {
                return;
            }
            window.isClicked = true;
            document.getElementById(id).click();
        });
    }
    document.addEventListener('DOMContentLoaded', handler);
    if (document.readyState !== 'loading') {
        handler();
    }
}, {{ intval($campaign->delay) * 1000 }});
@endif
