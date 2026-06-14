<script>
(function () {
    var key = 'dw-theme';
    var pref = localStorage.getItem(key) || 'system';
    var dark = pref === 'dark' || (pref === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    document.documentElement.dataset.themePref = pref;
})();
</script>
