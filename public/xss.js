/** Shared HTML/CSS escaping for client-rendered markup (e.g. AJAX search). */
function escHtml(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escUrl(path) {
    var p = String(path || '').trim();
    if (!p || /^\s*(javascript|data|vbscript|file):/i.test(p) || p.indexOf('..') !== -1) {
        return '';
    }
    return escHtml(p);
}

function escCssClass(value, allowed) {
    var v = String(value || '');
    if (allowed && allowed.length) {
        return allowed.indexOf(v) !== -1 ? v : '';
    }
    return v.replace(/[^a-z0-9_-]/gi, '');
}
