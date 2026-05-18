/**
 * Attach CSRF token to all fetch() requests (header + JSON body when applicable).
 */
(function () {
    var meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) return;

    var token = meta.getAttribute('content');
    if (!token) return;

    var originalFetch = window.fetch;

    window.fetch = function (input, init) {
        init = init || {};
        var headers = new Headers(init.headers || {});

        if (!headers.has('X-CSRF-Token')) {
            headers.set('X-CSRF-Token', token);
        }

        var method = (init.method || 'GET').toUpperCase();
        if (method !== 'GET' && method !== 'HEAD') {
            var contentType = headers.get('Content-Type') || '';
            if (contentType.indexOf('application/json') !== -1 && init.body && typeof init.body === 'string') {
                try {
                    var payload = JSON.parse(init.body);
                    if (!payload._csrf) {
                        payload._csrf = token;
                        init.body = JSON.stringify(payload);
                    }
                } catch (e) { /* not JSON */ }
            }
        }

        init.headers = headers;
        return originalFetch.call(this, input, init);
    };
})();
