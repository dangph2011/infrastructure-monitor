<!DOCTYPE html>
<html>

<head>
    <script src="https://cdn.rawgit.com/inexorabletash/polyfill/v0.1.42/polyfill.js"></script>
    <script src="https://cdn.rawgit.com/inexorabletash/polyfill/v0.1.42/typedarray.js"></script>
    {{-- <script src="/js/polyfill.js"></script>
    <script src="/js/typedarray.js"></script> --}}

    {{-- <script src="{{ asset('js/polyfill.js') }}" defer></script>
    <script src="{{ asset('js/typedarray.js') }}" defer></script> --}}

    <!-- Note: Using plotly-cartesian-latest here instead of plotly-latest -->
    <script src="https://cdn.plot.ly/plotly-basic-latest.min.js"></script>
    {{-- <script src="https://cdn.plot.ly/plotly-latest.min.js"></script> --}}
</head>

<body>
    <div id="plotid">
        <!-- Plotly chart will be drawn inside this DIV -->
    </div>
    <script>
        var data = {!!$data!!};
        var layout = {!!$layout!!}
        var myDiv = document.getElementById('plotid')
        Plotly.newPlot(myDiv, data, layout);
    </script>
</body>

</html>
