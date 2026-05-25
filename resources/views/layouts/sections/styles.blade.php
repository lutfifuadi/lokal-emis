<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
  rel="stylesheet" />

<style>
  body, h1, h2, h3, h4, h5, h6, p, a, div, span, button, input, select, textarea {
    font-family: 'Quicksand', sans-serif !important;
  }
</style>

<!-- Fonts Icons -->
@vite(['resources/assets/vendor/fonts/iconify/iconify.css'])

<!-- BEGIN: Vendor CSS-->
@vite(['resources/assets/vendor/libs/node-waves/node-waves.scss'])

@if ($configData['hasCustomizer'])
  @vite(['resources/assets/vendor/libs/pickr/pickr-themes.scss'])
@endif

<!-- Core CSS -->
@vite(['resources/assets/vendor/scss/core.scss', 'resources/assets/css/demo.css', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss'])

<!-- Vendor Styles -->
@vite(['resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
@yield('vendor-style')

<!-- Page Styles -->
@yield('page-style')

<!-- app CSS -->
@vite(['resources/css/app.css'])
<!-- END: app CSS-->
