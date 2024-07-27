<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ (!empty($containerNav) ? $containerNav : 'container-xxl') }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
      <div class="mb-2 mb-md-0">
{{--        طور ب <span class="text-danger">❤️</span> بواسطة <a href="{{ (!empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '') }}" target="_blank" class="footer-link fw-medium">{{ (!empty(config('variables.creatorName')) ? config('variables.creatorName') : '') }}</a>--}}
{{--        ,© <script>--}}
{{--          document.write(new Date().getFullYear())--}}
{{--        </script>--}}

       تطوير <a href="{{ (!empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '') }}" target="_blank" class="footer-link fw-medium">{{ (!empty(config('variables.creatorName')) ? config('variables.creatorName') : '') }}</a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
