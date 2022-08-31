@push('head')
    <meta name="robots" content="noindex" />
    <link
          href="{{ asset('/drunken_duck_Beer_2.svg') }}"
          sizes="any"
          type="image/svg+xml"
          id="favicon"
          rel="icon"
    >

    <!-- For Safari on iOS -->
    <meta name="theme-color" content="#21252a">
@endpush

<div class="h2 fw-light d-flex align-items-center">
   <x-orchid-icon path="fa.scale-balanced" width="3em" height="3em"/>

    <p class="ms-3 my-0 d-none d-sm-block">
        {{__('Balances')}}
    </p>
</div>
